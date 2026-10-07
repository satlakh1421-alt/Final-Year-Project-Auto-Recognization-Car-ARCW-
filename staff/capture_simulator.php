<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (login check, token, redirect, etc.)

// If staff not logged in
if (!is_staff_logged_in()) { // check whether staff session exists (user logged in or not)
    redirect("login.php"); // if not logged in, redirect to login page
} // End check

// Update staff last_seen
update_staff_last_seen($conn); // update staff last active time in database

$msg = ""; // variable to store message text (success or error)
$msg_type = ""; // variable to store message type (success/error for styling)

$created = false; // flag to check whether capture was successfully created
$token = ""; // variable to store generated token
$plate_no = ""; // variable to store plate number
$qr_url = ""; // variable to store generated QR link
$captured_at = ""; // variable to store capture time

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check if form is submitted using POST method
    $plate_no = strtoupper(trim($_POST["plate_no"] ?? "")); // get plate number, remove spaces, convert to uppercase

    if ($plate_no === "") { // check if plate number is empty
        $msg = "Please enter a plate number."; // set error message
        $msg_type = "error"; // set message type as error
    } else { // if plate number is valid
        $token = generate_token(20); // generate random token (length 20) for QR
        $status = "NEW"; // set initial status as NEW
        $captured_at = date("Y-m-d H:i:s"); // get current date and time

        $stmt = $conn->prepare("INSERT INTO captures (plate_no, token, status, captured_at) VALUES (?, ?, ?, ?)"); // prepare SQL insert query
        $stmt->bind_param("ssss", $plate_no, $token, $status, $captured_at); // bind values safely to prevent SQL injection
        $ok = $stmt->execute(); // execute query

        if ($ok) { // if insert successful
            $created = true; // set created flag to true
            $base = rtrim(BASE_URL, "/"); // remove trailing slash from base URL if exists
            $qr_url = $base . "/customer/start.php?t=" . urlencode($token); // create customer URL with token
            $msg = "Capture created successfully. Print / give QR to customer."; // success message
            $msg_type = "success"; // set message type as success
        } else { // if insert failed
            $msg = "Failed to create capture. Please try again."; // error message
            $msg_type = "error"; // set message type as error
        } // End ok check
    } // End plate check
} // End POST

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design for mobile -->
    <title>Capture Simulator + QR Generator</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS file -->
    
    <!-- BACKGROUND STYLE (ADDED) -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('../assets/Staff_Capture.jpg') no-repeat center center/cover;
            font-family: Arial, sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: -1;
        }

        .container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(12px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<div class="center"> <!-- center wrapper -->
    <div class="container wide"> <!-- main container -->

        <h1>Capture Simulator + QR Generator</h1> <!-- main heading -->

        <p class="small"> <!-- small text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["staff_username"]); ?></b> <!-- display logged in staff username safely -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- link back to dashboard -->
        </p>

        <?php if ($msg !== ""): ?> <!-- check if there is a message -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- message box with dynamic class -->
                <?php echo htmlspecialchars($msg); ?> <!-- display message safely -->
            </div>
        <?php endif; ?> <!-- end message check -->

        <div class="card"> <!-- card section -->
            <h2>1) Simulate Car Capture</h2> <!-- section title -->

            <form method="POST"> <!-- form using POST method -->
                <label for="plate_no">Plate Number</label> <!-- label for input -->
                <input id="plate_no" name="plate_no" type="text" class="input" placeholder="Example: WXY1234" required> <!-- input field for plate -->
                <button type="submit">Create Capture + Generate QR</button> <!-- submit button -->
            </form>

            <p class="small" style="margin-top:10px;"> <!-- small explanatory text -->
                This simulates: sensor detects car → camera captures plate → token generated → staff prints QR.
            </p>
        </div>

        <?php if ($created): ?> <!-- check if capture was successfully created -->
            <div class="card"> <!-- result card -->
                <h2>2) Give This QR / Link to Customer</h2> <!-- section title -->

                <p class="small"><b>Plate:</b> <?php echo htmlspecialchars($plate_no); ?></p> <!-- display plate number -->
                <p class="small"><b>Captured At:</b> <?php echo htmlspecialchars($captured_at); ?></p> <!-- display capture time -->
                <p class="small"><b>Token:</b> <span id="tokenText"><?php echo htmlspecialchars($token); ?></span></p> <!-- display token -->

                <p class="small" style="margin-top:10px;"><b>Customer Link:</b></p> <!-- label -->
                <p class="small" id="linkText"><?php echo htmlspecialchars($qr_url); ?></p> <!-- display QR link -->

                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;"> <!-- button container -->
                    <button type="button" onclick="copyText('linkText')">Copy Link</button> <!-- copy link button -->
                    <button type="button" onclick="copyText('tokenText')">Copy Token</button> <!-- copy token button -->
                    <button type="button" onclick="window.print()">Print This Page</button> <!-- print page button -->
                </div>

                <div style="margin-top:16px; text-align:center;">
                <p class="small"><b>QR Image (Online)</b></p>
            
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?php echo urlencode($qr_url); ?>"
                    alt="QR Code"
                    style="margin-top:10px; border-radius:14px; border:1px solid rgba(255,255,255,0.08); background:#fff; padding:10px;"
                >
            
                <p class="small" style="margin-top:10px;">
                    If QR image does not load, just copy the link above and paste it in browser.
                </p>
            </div>
            </div>
        <?php endif; ?> <!-- end created check -->

    </div>
</div>

<script> // start JavaScript
function copyText(id) { // function to copy text by element id
    const el = document.getElementById(id); // get element by id
    const text = el ? el.innerText : ""; // get text content if element exists
    if (!text) { // if no text found
        alert("Nothing to copy."); // show alert
        return; // stop function
    }
    navigator.clipboard.writeText(text).then(() => { // copy text to clipboard
        alert("Copied: " + text); // success alert
    }).catch(() => { // if copy fails
        alert("Copy failed. Please copy manually."); // error alert
    });
}
</script>

</body>
</html>