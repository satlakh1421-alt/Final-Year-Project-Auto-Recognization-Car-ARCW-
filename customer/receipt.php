<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // Load DB + session
require_once __DIR__ . "/../includes/functions.php"; // Helpers

if (!is_customer_logged_in()) { // Check login
    redirect("login.php"); // Redirect if not logged in
} // End login check

if (!isset($_SESSION["order_id"])) { // Check order session
    redirect("dashboard.php"); // Redirect if missing
} // End order session check

$order_id = (int)$_SESSION["order_id"]; // Get order id

$stmtO = $conn->prepare("SELECT id, plate_no, car_color, car_type, status, total_price, created_at FROM orders WHERE id=?"); // Query order
$stmtO->bind_param("i", $order_id); // Bind
$stmtO->execute(); // Execute
$order = $stmtO->get_result()->fetch_assoc(); // Fetch order

if (!$order) { // If order missing
    redirect("dashboard.php"); // Redirect
} // End check

$stmtP = $conn->prepare("SELECT amount, payment_status, paid_at, txn_ref FROM payments WHERE order_id=? ORDER BY id DESC LIMIT 1"); // Query latest payment
$stmtP->bind_param("i", $order_id); // Bind
$stmtP->execute(); // Execute
$payment = $stmtP->get_result()->fetch_assoc(); // Fetch payment
?>
<!DOCTYPE html> <!-- HTML type -->
<html lang="en"> <!-- HTML start -->
<head> <!-- Head -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Receipt</title> <!-- Title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- CSS -->
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('../assets/Staff_Background.jpg') no-repeat center center/cover;
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
        
        .title {
            text-align: center;
            color: #cdbdff;
            font-size: 50px;
            font-weight: bold;
            margin-bottom: -100px; /* THIS controls distance */
            text-shadow: 
            0 10px 0 #000,
            0 50px 10px rgba(0,0,0,0.6);
            transform: translateY( 30px);
        }
        
        ..wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* center everything */
            align-items: center;
            gap: 15px; /* controls space between title & box */
        }
        
        @media (max-width: 768px) {
        body {
        background-size: cover; /* CHANGE THIS */
        background-position: center;
    }
}
        
    </style>
    
    
</head> <!-- End head -->
<body> <!-- Body -->
    <div class="center"> <!-- Center -->
        <div class="container wide"> <!-- Container -->
            <h1>Receipt</h1> <!-- Heading -->

            <div class="card"> <!-- Card -->
                <p class="small">Order ID: <b><?php echo (int)$order["id"]; ?></b></p> <!-- Order id -->
                <p class="small">Plate: <b><?php echo htmlspecialchars($order["plate_no"]); ?></b></p> <!-- Plate -->
                <p class="small">Car Color: <b><?php echo htmlspecialchars($order["car_color"]); ?></b></p> <!-- Color -->
                <p class="small">Car Type: <b><?php echo htmlspecialchars($order["car_type"]); ?></b></p> <!-- Type -->
                <p class="small">Order Status: <b><?php echo htmlspecialchars($order["status"]); ?></b></p> <!-- Status -->
                <p class="small">Order Date: <b><?php echo htmlspecialchars($order["created_at"]); ?></b></p> <!-- Date -->
            </div> <!-- End card -->

            <h2>Payment Details</h2> <!-- Payment title -->

            <?php if ($payment): ?> <!-- If payment exists -->
                <div class="card"> <!-- Card -->
                    <p class="small">Amount Paid (RM): <b><?php echo number_format((float)$payment["amount"], 2); ?></b></p> <!-- Amount -->
                    <p class="small">Payment Status: <b><?php echo htmlspecialchars($payment["payment_status"]); ?></b></p> <!-- Status -->
                    <p class="small">Paid At: <b><?php echo htmlspecialchars($payment["paid_at"]); ?></b></p> <!-- Paid at -->
                    <p class="small">Transaction Ref: <b><?php echo htmlspecialchars($payment["txn_ref"]); ?></b></p> <!-- Ref -->
                </div> <!-- End card -->
            <?php else: ?> <!-- If no payment -->
                <div class="msg error">Payment record not found.</div> <!-- Error -->
            <?php endif; ?> <!-- End payment check -->
            <p><a href="receipt_pdf.php">Download Receipt PDF</a></p>

            <div class="outro-container"> <!-- Footer -->
                <p>You can logout now. Staff will start washing once your car is ready.</p> <!-- Note -->
                <p><a href="logout.php">Logout</a></p> <!-- Logout -->
            </div> <!-- End footer -->
        </div> <!-- End container -->
    </div> <!-- End center -->
</body> <!-- End body -->
</html> <!-- End html -->
