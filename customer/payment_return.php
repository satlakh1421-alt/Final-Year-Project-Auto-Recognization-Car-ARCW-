<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/toyyibpay.php";

/*
=========================================================
READ TOYYIBPAY RETURN DATA
=========================================================
*/
$status_id = $_GET["status_id"] ?? "";
$billcode  = trim($_GET["billcode"] ?? "");
$order_ref = trim($_GET["order_id"] ?? "");        // ORDER-41
$txn_ref   = trim($_GET["transaction_id"] ?? "");  // TP...

/*
=========================================================
BILLCODE REQUIRED
=========================================================
*/
if ($billcode === "") {
    redirect("dashboard.php");
}

/*
=========================================================
CHECK PAYMENT STATUS
=========================================================
*/
$isPaid = ($status_id === "1");

/*
Try verify from ToyyibPay too.
This helps when sandbox/live response is slightly different.
*/
try {
    $verify = verify_bill($billcode);

    if (isset($verify["json"]) && is_array($verify["json"])) {
        foreach ($verify["json"] as $row) {
            $sid = $row["status_id"] ?? ($row["status"] ?? null);

            if ((string)$sid === "1" || strtolower((string)$sid) === "success") {
                $isPaid = true;
                break;
            }
        }
    }
} catch (Exception $e) {
    // ignore verify errors, especially during sandbox testing
}

/*
=========================================================
GET ORDER ID
=========================================================
*/
$order_id = 0;

// Try from ORDER-xx first
if (preg_match("/ORDER-(\d+)/", $order_ref, $m)) {
    $order_id = (int)$m[1];
}

// Fallback: find from payments table using billcode
if ($order_id <= 0) {
    $stmt = $conn->prepare("
        SELECT order_id
        FROM payments
        WHERE billcode = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param("s", $billcode);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $order_id = (int)$row["order_id"];
    }
}

/*
=========================================================
IF SUCCESS -> UPDATE PAYMENT + ORDER
=========================================================
*/
if ($isPaid && $order_id > 0) {

    // Update latest payment row
    $stmt = $conn->prepare("
        UPDATE payments
        SET payment_status = 'SUCCESS',
            paid_at = NOW(),
            txn_ref = ?
        WHERE billcode = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param("ss", $txn_ref, $billcode);
    $stmt->execute();
    $stmt->close();

    // Update order status
    $stmt = $conn->prepare("
        UPDATE orders
        SET status = 'PAID'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // Save order in session for status page
    $_SESSION["order_id"] = $order_id;

    redirect("status.php");
}

/*
=========================================================
IF FAILED -> GO BACK TO PAYMENT
=========================================================
*/
if ($order_id > 0) {
    $_SESSION["order_id"] = $order_id;
}

redirect("payment.php");