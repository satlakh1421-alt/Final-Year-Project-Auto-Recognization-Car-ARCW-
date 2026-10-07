<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

$token = trim($_GET["t"] ?? "");
$msg = "";
$msg_type = "error";

if ($token === "") {
    $msg = "Invalid QR: token missing.";
} else {
    $stmt = $conn->prepare("SELECT id, plate_no, status FROM captures WHERE token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $msg = "Invalid QR: record not found.";
    } else {
        $cap = $result->fetch_assoc();
        $stmt->close();

        $_SESSION["qr_token"] = $token;
        $_SESSION["capture_id"] = (int)$cap["id"];
        $_SESSION["plate_no"] = $cap["plate_no"];

        if (!is_customer_logged_in()) {
    $_SESSION["redirect_after_login"] = "start.php?t=" . urlencode($token);
    redirect("login.php");
}

        $customer_id = (int)$_SESSION["customer_id"];
        $capture_id = (int)$cap["id"];

        $stmtO = $conn->prepare("
            SELECT id, status, customer_id
            FROM orders
            WHERE capture_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmtO->bind_param("i", $capture_id);
        $stmtO->execute();
        $order = $stmtO->get_result()->fetch_assoc();
        $stmtO->close();

        if ($order) {
    $order_owner = (int)$order["customer_id"];

    if ($order_owner !== $customer_id) {
        $_SESSION["dashboard_msg"] = "You have to scan a new QR code to make an order.";
        unset($_SESSION["qr_token"], $_SESSION["capture_id"], $_SESSION["plate_no"], $_SESSION["order_id"]);
        redirect("dashboard.php");
    }

    $_SESSION["order_id"] = (int)$order["id"];
    $st = strtoupper(trim($order["status"] ?? ""));

    if ($st === "PENDING") {
        redirect("payment.php");
    } elseif ($st === "PAID" || $st === "WASHING") {
        redirect("status.php");
    } elseif ($st === "DONE") {
        unset($_SESSION["order_id"]);
        $_SESSION["dashboard_msg"] = "You have to scan a new QR code to make an order.";
        redirect("dashboard.php");
    } else {
        unset($_SESSION["order_id"]);
        redirect("dashboard.php");
    }
} else {
    redirect("order_form.php");
}
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Start</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="center">
    <div class="container">
        <h1>QR Entry</h1>

        <div class="msg <?php echo htmlspecialchars($msg_type); ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>

        <p class="small" style="margin-top:12px;">
            If you scanned a QR, please try again or ask staff to regenerate the QR.
        </p>
    </div>
</div>
</body>
</html>