<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // Load DB + session
require_once __DIR__ . "/../includes/functions.php"; // Load helpers

if (!is_customer_logged_in()) { // If customer not logged in
    redirect("login.php"); // Go login
} // End login check

if (!isset($_SESSION["order_id"])) { // If no order in session
    redirect("dashboard.php"); // Go dashboard
} // End check

$order_id = (int)$_SESSION["order_id"]; // Read order id
$customer_id = (int)$_SESSION["customer_id"]; // Read customer id

require_once __DIR__ . "/../includes/fpdf/fpdf.php"; // Load FPDF library file

$stmtO = $conn->prepare("SELECT o.id, o.plate_no, o.car_color, o.car_type, o.total_price, o.created_at, s.name AS service_name, s.price AS service_price
                         FROM orders o
                         LEFT JOIN services s ON s.id = o.service_id
                         WHERE o.id = ? AND o.customer_id = ?"); // Get order + service (only for this customer)
$stmtO->bind_param("ii", $order_id, $customer_id); // Bind order id + customer id
$stmtO->execute(); // Execute query
$order = $stmtO->get_result()->fetch_assoc(); // Fetch row

if (!$order) { // If order not found or not owned by customer
    die("Order not found."); // Stop
} // End check

$stmtP = $conn->prepare("SELECT amount, payment_status, paid_at, txn_ref FROM payments WHERE order_id=? ORDER BY id DESC LIMIT 1"); // Get latest payment
$stmtP->bind_param("i", $order_id); // Bind order id
$stmtP->execute(); // Execute
$payment = $stmtP->get_result()->fetch_assoc(); // Fetch payment

$addons = []; // Create addons array
$stmtA = $conn->prepare("SELECT a.name, a.price
                         FROM order_addons oa
                         JOIN addons a ON a.id = oa.addon_id
                         WHERE oa.order_id = ?"); // Get addons for this order
$stmtA->bind_param("i", $order_id); // Bind order id
$stmtA->execute(); // Execute
$resA = $stmtA->get_result(); // Get results

while ($row = $resA->fetch_assoc()) { // Loop addon rows
    $addons[] = $row; // Add to array
} // End loop

$pdf = new FPDF(); // Create PDF object
$pdf->AddPage(); // Add one page
$pdf->SetFont("Arial", "B", 16); // Set font for title
$pdf->Cell(0, 10, "Servpro Autospa Detailing by Persada Car Wash Receipt", 0, 1, "C"); // Title centered

$pdf->Ln(4); // Line break
$pdf->SetFont("Arial", "", 12); // Normal font

$pdf->Cell(0, 8, "Order ID: " . $order["id"], 0, 1); // Order id
$pdf->Cell(0, 8, "Plate No: " . $order["plate_no"], 0, 1); // Plate
$pdf->Cell(0, 8, "Car Type: " . $order["car_type"], 0, 1); // Type
$pdf->Cell(0, 8, "Car Color: " . $order["car_color"], 0, 1); // Color
$pdf->Cell(0, 8, "Order Date: " . $order["created_at"], 0, 1); // Date

$pdf->Ln(4); // Line break
$pdf->SetFont("Arial", "B", 12); // Bold heading
$pdf->Cell(0, 8, "Service", 0, 1); // Service heading

$pdf->SetFont("Arial", "", 12); // Normal font
$serviceLine = ($order["service_name"] ? $order["service_name"] : "N/A") . "  (RM " . number_format((float)($order["service_price"] ?? 0), 2) . ")"; // Build service line
$pdf->MultiCell(0, 7, $serviceLine); // Print service line

$pdf->Ln(2); // Small break
$pdf->SetFont("Arial", "B", 12); // Bold heading
$pdf->Cell(0, 8, "Add-ons", 0, 1); // Addons heading

$pdf->SetFont("Arial", "", 12); // Normal font
if (count($addons) === 0) { // If no addons
    $pdf->Cell(0, 7, "- None", 0, 1); // Print none
} else { // If addons exist
    foreach ($addons as $a) { // Loop addons
        $line = "- " . $a["name"] . "  (RM " . number_format((float)$a["price"], 2) . ")"; // Line text
        $pdf->Cell(0, 7, $line, 0, 1); // Print line
    } // End loop
} // End addons check

$pdf->Ln(4); // Break
$pdf->SetFont("Arial", "B", 12); // Bold
$pdf->Cell(0, 8, "Payment", 0, 1); // Payment heading

$pdf->SetFont("Arial", "", 12); // Normal
if ($payment) { // If payment exists
    $pdf->Cell(0, 7, "Status: " . $payment["payment_status"], 0, 1); // Status
    $pdf->Cell(0, 7, "Paid At: " . $payment["paid_at"], 0, 1); // Paid at
    $pdf->Cell(0, 7, "Txn Ref: " . $payment["txn_ref"], 0, 1); // Ref
} else { // If no payment
    $pdf->Cell(0, 7, "No payment record found.", 0, 1); // Note
} // End payment check

$pdf->Ln(4); // Break
$pdf->SetFont("Arial", "B", 14); // Big bold
$pdf->Cell(0, 10, "TOTAL: RM " . number_format((float)$order["total_price"], 2), 0, 1, "R"); // Total right-aligned

$filename = "receipt_order_" . $order_id . ".pdf"; // File name
$pdf->Output("D", $filename); // Force download
exit(); // Stop script
?>
