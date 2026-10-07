<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions

if (!is_staff_logged_in()) { // If staff not logged in
    redirect("login.php"); // Send to staff login
} // End staff login check

$order_id = (int)($_GET["id"] ?? 0); // Read order id from URL

if ($order_id <= 0) { // If invalid id
    die("Invalid order id."); // Stop
} // End id check

require_once __DIR__ . "/../includes/fpdf/fpdf.php"; // Load FPDF library

$stmtO = $conn->prepare("SELECT 
                            o.id,
                            o.plate_no,
                            o.car_color,
                            o.car_type,
                            o.total_price,
                            o.created_at,
                            s.name AS service_name,
                            s.price AS service_price
                         FROM orders o
                         LEFT JOIN services s ON s.id = o.service_id
                         WHERE o.id = ?"); // Get order + service

$stmtO->bind_param("i", $order_id); // Bind order id
$stmtO->execute(); // Execute query
$order = $stmtO->get_result()->fetch_assoc(); // Fetch order row

if (!$order) { // If order not found
    die("Order not found."); // Stop
} // End order check

$stmtP = $conn->prepare("SELECT amount, payment_status, paid_at, txn_ref 
                         FROM payments 
                         WHERE order_id=? 
                         ORDER BY id DESC 
                         LIMIT 1"); // Get latest payment

$stmtP->bind_param("i", $order_id); // Bind order id
$stmtP->execute(); // Execute query
$payment = $stmtP->get_result()->fetch_assoc(); // Fetch payment row

$addons = []; // Prepare addons array

$stmtA = $conn->prepare("SELECT a.name, a.price
                         FROM order_addons oa
                         JOIN addons a ON a.id = oa.addon_id
                         WHERE oa.order_id = ?"); // Get addons list

$stmtA->bind_param("i", $order_id); // Bind order id
$stmtA->execute(); // Execute query
$resA = $stmtA->get_result(); // Get results

while ($row = $resA->fetch_assoc()) { // Loop addons
    $addons[] = $row; // Add to array
} // End loop

$pdf = new FPDF(); // Create PDF object
$pdf->AddPage(); // Add one page
$pdf->SetFont("Arial", "B", 16); // Set big bold font
$pdf->Cell(0, 10, "Servpro Autospa Detailing by Persada Car Wash Receipt (STAFF)", 0, 1, "C"); // Title

$pdf->Ln(4); // Line break
$pdf->SetFont("Arial", "", 12); // Normal font

$pdf->Cell(0, 8, "Order ID: " . $order["id"], 0, 1); // Order id
$pdf->Cell(0, 8, "Plate No: " . $order["plate_no"], 0, 1); // Plate
$pdf->Cell(0, 8, "Car Type: " . $order["car_type"], 0, 1); // Car type
$pdf->Cell(0, 8, "Car Color: " . $order["car_color"], 0, 1); // Car color
$pdf->Cell(0, 8, "Order Date: " . $order["created_at"], 0, 1); // Date

$pdf->Ln(4); // Break
$pdf->SetFont("Arial", "B", 12); // Bold heading
$pdf->Cell(0, 8, "Service", 0, 1); // Service heading

$pdf->SetFont("Arial", "", 12); // Normal font
$serviceLine = ($order["service_name"] ? $order["service_name"] : "N/A") . " (RM " . number_format((float)($order["service_price"] ?? 0), 2) . ")"; // Service text
$pdf->MultiCell(0, 7, $serviceLine); // Print service line

$pdf->Ln(2); // Break
$pdf->SetFont("Arial", "B", 12); // Bold heading
$pdf->Cell(0, 8, "Add-ons", 0, 1); // Addons heading

$pdf->SetFont("Arial", "", 12); // Normal font
if (count($addons) === 0) { // If none
    $pdf->Cell(0, 7, "- None", 0, 1); // Print none
} else { // If addons exist
    foreach ($addons as $a) { // Loop addons
        $line = "- " . $a["name"] . " (RM " . number_format((float)$a["price"], 2) . ")"; // Addon line
        $pdf->Cell(0, 7, $line, 0, 1); // Print line
    } // End loop
} // End addons check

$pdf->Ln(4); // Break
$pdf->SetFont("Arial", "B", 12); // Bold heading
$pdf->Cell(0, 8, "Payment", 0, 1); // Payment heading

$pdf->SetFont("Arial", "", 12); // Normal font
if ($payment) { // If payment exists
    $pdf->Cell(0, 7, "Status: " . $payment["payment_status"], 0, 1); // Status
    $pdf->Cell(0, 7, "Paid At: " . $payment["paid_at"], 0, 1); // Paid at
    $pdf->Cell(0, 7, "Txn Ref: " . $payment["txn_ref"], 0, 1); // Ref
    $pdf->Cell(0, 7, "Amount: RM " . number_format((float)$payment["amount"], 2), 0, 1); // Amount
} else { // If no payment
    $pdf->Cell(0, 7, "No payment record found.", 0, 1); // Note
} // End payment check

$pdf->Ln(4); // Break
$pdf->SetFont("Arial", "B", 14); // Big bold
$pdf->Cell(0, 10, "TOTAL: RM " . number_format((float)$order["total_price"], 2), 0, 1, "R"); // Total

$filename = "staff_receipt_order_" . $order_id . ".pdf"; // File name
$pdf->Output("D", $filename); // Force download
exit(); // Stop script
