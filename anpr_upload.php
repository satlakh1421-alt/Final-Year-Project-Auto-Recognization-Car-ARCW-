<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$api_key = "fc2a5ce1ba7eaba348351095c3b8368d1995e672";

require_once __DIR__ . "/includes/db.php";

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* =========================
   SETTINGS
   ========================= */
$min_score = 0.85; // Increase if still too many false detections

/* =========================
   HELPERS
   ========================= */
function clean_plate($plate) {
    $plate = strtoupper(trim($plate ?? ""));
    $plate = preg_replace('/[^A-Z0-9]/', '', $plate);
    return $plate;
}

function looks_like_malaysia_plate($plate) {
    return preg_match('/^[A-Z]{1,3}[0-9]{1,4}[A-Z]{0,1}$/', $plate);
}

function update_live_status_message($message, $type = "info") {
    $data = [
        "message" => $message,
        "type" => $type,
        "updated_at" => date("Y-m-d H:i:s")
    ];

    file_put_contents(__DIR__ . "/live_status.json", json_encode($data, JSON_PRETTY_PRINT));
}

/* =========================
   HANDLE CAMERA CAPTURE IF USED
   ========================= */
$upload_dir = __DIR__ . "/uploads/";
$debug_log = __DIR__ . "/anpr_debug.log";

if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true) && !file_exists($upload_dir)) {
        die("Failed to create uploads folder: " . $upload_dir);
    }
}

if (isset($_POST['image_data'])) {
    $data = $_POST['image_data'];
    $data = str_replace('data:image/jpeg;base64,', '', $data);
    $data = base64_decode($data);

    if ($data === false) {
        file_put_contents($debug_log, date("Y-m-d H:i:s") . " | base64 decode failed\n", FILE_APPEND);
        die("Base64 decode failed");
    }

    $temp_file = $upload_dir . time() . "_camera.jpg";
    $bytes = file_put_contents($temp_file, $data);

    if ($bytes === false) {
        file_put_contents($debug_log, date("Y-m-d H:i:s") . " | file_put_contents failed: $temp_file\n", FILE_APPEND);
        die("Failed to save camera image");
    }

    $_FILES['image']['tmp_name'] = $temp_file;
    $_FILES['image']['name'] = "camera.jpg";
}

file_put_contents(
    __DIR__ . "/live_status.json",
    json_encode([
        "message" => "Reading number plate",
        "type" => "info",
        "updated_at" => date("Y-m-d H:i:s")
    ], JSON_PRETTY_PRINT)
);

/* =========================
   PROCESS UPLOADED IMAGE
   ========================= */
if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {

    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target = $upload_dir . $image_name;
    $image_path_for_db = "uploads/" . $image_name;

    $saved = false;

    if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
        $saved = move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    } else {
        $saved = copy($_FILES["image"]["tmp_name"], $target);
    }

    if (!$saved || !file_exists($target)) {
        file_put_contents(
            $debug_log,
            date("Y-m-d H:i:s") . " | failed saving upload | tmp=" . ($_FILES['image']['tmp_name'] ?? 'NULL') . " | target=$target\n",
            FILE_APPEND
        );
        die("Failed to save uploaded image to: " . $target);
    }

    file_put_contents(
        $debug_log,
        date("Y-m-d H:i:s") . " | image saved successfully | target=$target\n",
        FILE_APPEND
    );
    /* Send image to Plate Recognizer API */
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.platerecognizer.com/v1/plate-reader/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Token $api_key"
        ),
        CURLOPT_POSTFIELDS => array(
            'upload' => new CURLFile($target)
        ),
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        die("API request failed: " . curl_error($curl));
    }

    curl_close($curl);

    $result = json_decode($response, true);

    /* =========================
       READ BEST RESULT
       ========================= */
    $plate_number = "";
    $plate_score = 0;

    if (isset($result['results'][0]['plate'])) {
        $plate_number = clean_plate($result['results'][0]['plate']);
        $plate_score = (float)($result['results'][0]['score'] ?? 0);
    }

    /* =========================
       RULE 1: NO VALID PLATE = DO NOT SAVE
       ========================= */
    if ($plate_number === "") {
        update_live_status_message("Number plate not found", "error");
        echo "<h2>No valid plate detected.</h2>";
        echo "<p>Capture ignored. Nothing saved into captures table.</p>";
        exit;
    }

    if (strlen($plate_number) < 4) {
        update_live_status_message("Number plate not found", "error");
        echo "<h2>Ignored: detected text too short (" . htmlspecialchars($plate_number) . ")</h2>";
        echo "<p>Nothing saved into captures table.</p>";
        exit;
    }

    if ($plate_score < $min_score) {
        update_live_status_message("Number plate not found", "error");
        echo "<h2>Ignored: low confidence plate detection.</h2>";
        echo "<p>Detected plate: " . htmlspecialchars($plate_number) . "</p>";
        echo "<p>Score: " . htmlspecialchars((string)$plate_score) . "</p>";
        echo "<p>Nothing saved into captures table.</p>";
        exit;
    }

    if (!looks_like_malaysia_plate($plate_number)) {
        update_live_status_message("Invalid number plate", "error");
        echo "<h2>Ignored: text does not look like a valid number plate.</h2>";
        echo "<p>Detected text: " . htmlspecialchars($plate_number) . "</p>";
        echo "<p>Nothing saved into captures table.</p>";
        exit;
    }

    /* RULE 2: ignore only exact same plate if latest same plate was within 20 minutes */
    $stmtRecent = $conn->prepare("
        SELECT id, plate_no, captured_at
        FROM captures
        WHERE UPPER(TRIM(plate_no)) = ?
        ORDER BY captured_at DESC
        LIMIT 1
    ");

    if (!$stmtRecent) {
        die("Prepare failed (duplicate check): " . $conn->error);
    }

    $stmtRecent->bind_param("s", $plate_number);
    $stmtRecent->execute();
    $recentCapture = $stmtRecent->get_result()->fetch_assoc();
    $stmtRecent->close();

    if ($recentCapture) {
        $last_time = strtotime($recentCapture["captured_at"]);
        $now_time = time();

        if ($last_time !== false && ($now_time - $last_time) < (20 * 60)) {
            update_live_status_message("Duplicate plate ignored", "warning");
            echo "<h2>Duplicate ignored: same plate already exists in last 20 minutes (" . htmlspecialchars($plate_number) . ")</h2>";
            exit;
        }
    }

    /* =========================
       SAVE INTO CAPTURES TABLE
       ========================= */
    $qr_token = bin2hex(random_bytes(16));
    $captured_at = date("Y-m-d H:i:s");
    $status = "NEW";

    $stmt = $conn->prepare("
        INSERT INTO captures (plate_no, captured_at, token, status, image_path)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $plate_number, $captured_at, $qr_token, $status, $image_path_for_db);

    if (!$stmt->execute()) {
        die("Insert into captures failed: " . $stmt->error);
    }

    $stmt->close();

    /* =========================
       UPDATE LIVE STATUS JSON
       ========================= */
    $live_status_data = [
        "message" => "Ready for QR scan",
        "type" => "success",
        "updated_at" => $captured_at,
        "plate_no" => $plate_number,
        "captured_at" => $captured_at,
        "image_path" => $image_path_for_db
    ];

    file_put_contents(__DIR__ . "/live_status.json", json_encode($live_status_data, JSON_PRETTY_PRINT));

    echo "<h2>Plate Detected: " . htmlspecialchars($plate_number) . "</h2>";
    echo "<p>Score: " . htmlspecialchars((string)$plate_score) . "</p>";
    echo "<p>Saved into captures table successfully.</p>";
    echo "<p>Live vehicle status updated.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ANPR Upload Test</title>
</head>
<body>

<h2>Upload Plate Image (Test)</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <br><br>
    <button type="submit">Upload</button>
</form>

</body>
</html>