<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/toyyibpay.php";

$billcode = $_GET["billcode"] ?? "";
$refno = $_GET["refno"] ?? "";
$status = $_GET["status"] ?? "";

if (!$billcode) {
    exit("OK");
}

$verify = verify_bill($billcode);

$isPaid = false;

foreach ($verify as $row) {
    if ($row["status_id"] == "1") {
        $isPaid = true;
        break;
    }
}

if ($isPaid) {
    $stmt = $conn->prepare("UPDATE payments SET payment_status='SUCCESS', txn_ref=?, paid_at=NOW() WHERE billcode=?");
    $stmt->bind_param("ss", $refno, $billcode);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE orders SET status='PAID' WHERE id=(SELECT order_id FROM payments WHERE billcode=? LIMIT 1)");
    $stmt->bind_param("s", $billcode);
    $stmt->execute();
    $stmt->close();
}

echo "OK";