<?php
// ============================
// LOAD SYSTEM FILES
// ============================
require_once __DIR__ . "/../includes/db.php";          // DB connection + session
require_once __DIR__ . "/../includes/functions.php";   // helper functions
require_once __DIR__ . "/../includes/config.php";      // config (BASE_URL etc)
require_once __DIR__ . "/../includes/toyyibpay.php";   // ToyyibPay API

// ============================
// LOGIN CHECK
// ============================
if (!is_customer_logged_in()) {
    redirect("login.php");
}

// ============================
// ORDER SESSION CHECK
// ============================
if (!isset($_SESSION["order_id"])) {
    redirect("dashboard.php");
}

$order_id    = (int) $_SESSION["order_id"];
$customer_id = (int) $_SESSION["customer_id"];


// ============================
// FETCH ORDER + CUSTOMER DATA
// ============================
$stmt = $conn->prepare("
    SELECT 
        o.id,
        o.customer_id,
        o.plate_no,
        o.total_price,
        o.status,
        c.name  AS customer_name,
        c.email AS customer_email,
        c.phone AS customer_phone
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
    WHERE o.id = ?
    LIMIT 1
");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();


// ============================
// SECURITY CHECK
// ============================
if (!$order) {
    unset($_SESSION["order_id"]);
    redirect("dashboard.php");
}

// prevent user accessing other people's order
if ((int)$order["customer_id"] !== $customer_id) {
    unset($_SESSION["order_id"]);
    redirect("dashboard.php");
}

// if already paid or washing → go status
$status = strtoupper(trim($order["status"] ?? ""));

if ($status === "PAID" || $status === "WASHING" || $status === "DONE") {
    redirect("status.php");
}

// ============================
// CREATE NEW BILL
// ============================
$amount_rm  = (float) $order["total_price"];
$amount_sen = (int) round($amount_rm * 100);

// URLs
$returnUrl   = BASE_URL . "/customer/payment_return.php";
$callbackUrl = BASE_URL . "/api/toyyibpay_callback.php";

// safe fallback values
$billTo    = trim($order["customer_name"] ?? "") ?: "Customer";
$billEmail = trim($order["customer_email"] ?? "") ?: "test@test.com";
$billPhone = trim($order["customer_phone"] ?? "") ?: "0123456789";

// payload for ToyyibPay
$payload = [
    "userSecretKey" => TOYYIBPAY_SECRET_KEY,
    "categoryCode"  => TOYYIBPAY_CATEGORY_CODE,

    "billName"        => "Car Wash Payment",
    "billDescription" => "Plate: " . $order["plate_no"],

    "billPriceSetting" => 1,
    "billPayorInfo"    => 1,
    "billAmount"       => $amount_sen,

    "billReturnUrl"   => $returnUrl,
    "billCallbackUrl" => $callbackUrl,

    "billExternalReferenceNo" => "ORDER-" . $order["id"],

    "billTo"    => $billTo,
    "billEmail" => $billEmail,
    "billPhone" => $billPhone,
];

// mark old pending payment attempts as abandoned before saving new bill
$stmtOld = $conn->prepare("
    UPDATE payments
    SET payment_status = 'ABANDONED'
    WHERE order_id = ?
      AND payment_status = 'PENDING'
");
$stmtOld->bind_param("i", $order_id);
$stmtOld->execute();
$stmtOld->close();

// ============================
// CALL TOYYIBPAY API
// ============================
$billcode = create_bill($payload);

// ============================
// SAVE PAYMENT RECORD
// ============================
$stmt = $conn->prepare("
    INSERT INTO payments (order_id, amount, payment_status, billcode, gateway)
    VALUES (?, ?, 'PENDING', ?, 'TOYYIBPAY')
");

$stmt->bind_param("ids", $order_id, $amount_rm, $billcode);
$stmt->execute();
$stmt->close();


// ============================
// REDIRECT TO PAYMENT PAGE
// ============================
header("Location: " . rtrim(TOYYIBPAY_BASE_URL, "/") . "/" . $billcode);
exit;