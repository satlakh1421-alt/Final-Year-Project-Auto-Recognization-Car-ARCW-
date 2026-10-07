<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + session
require_once __DIR__ . "/../includes/functions.php"; // Load helpers (redirect + login check)

if (!is_customer_logged_in()) { // If customer is not logged in
    redirect("login.php"); // Send to customer login
} // End login check

$customer_id = (int)$_SESSION["customer_id"]; // Read customer id from session safely as int

$stmt = $conn->prepare( // Prepare SQL to get latest order
    "SELECT id, plate_no, car_color, car_type, status, total_price, created_at
     FROM orders
     WHERE customer_id = ?
     ORDER BY id DESC
     LIMIT 1"
); // End prepare

$stmt->bind_param("i", $customer_id); // Bind customer_id as integer
$stmt->execute(); // Execute query
$order = $stmt->get_result()->fetch_assoc(); // Fetch the row as associative array

$status_label = ""; // Store friendly status label
$status_note = ""; // Store friendly explanation
$status_class = "badge"; // Default badge class container

$can_edit = false; // Flag if customer is allowed to edit
$st = ""; // Store normalized status text

if (!$order) { // If no order found for this customer
    $status_label = "NO ORDER"; // Friendly label
    $status_note = "No order found yet. Please scan the QR and submit your service order."; // Friendly message
} else { // If order exists

    $_SESSION["order_id"] = (int)$order["id"]; // Save active order id in session for edit_order.php and receipt pages

    $st = strtoupper(trim($order["status"] ?? "")); // Read status safely and normalize

    if ($st === "PENDING") { // If order is pending
        $status_label = "WAITING"; // Friendly label for user
        $status_note = "Your order is created. Please complete payment or wait for staff to start washing."; // Note
        $can_edit = true; // Allow edit before washing starts
    } elseif ($st === "PAID") { // If payment done but washing not started
        $status_label = "PROCESSING"; // Friendly label
        $status_note = "Payment received. Your car is in queue. Staff will start washing soon."; // Note
        $can_edit = true; // Still allow edit before washing starts
    } elseif ($st === "WASHING") { // If staff started washing
        $status_label = "WASHING"; // Friendly label
        $status_note = "Your car wash has started. Editing is now locked."; // Note
        $can_edit = false; // Do not allow edit
    } elseif ($st === "DONE") { // If staff finished washing
        $status_label = "DONE"; // Friendly label
        $status_note = "Your car wash is complete. Thank you! You can view or download your receipt."; // Note
        $can_edit = false; // Do not allow edit
    } else { // Any unknown status
        $status_label = "UNKNOWN"; // Friendly label
        $status_note = "Status not recognized. Please contact staff."; // Note
        $can_edit = false; // Do not allow edit
    } // End status mapping
} // End order check

?>
<!DOCTYPE html> <!-- HTML5 document -->
<html lang="en"> <!-- Start HTML -->
<head> <!-- Start head -->
    <meta charset="UTF-8"> <!-- Set encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <meta http-equiv="refresh" content="5"> <!-- Auto refresh every 5 seconds -->
    <title>Order Status</title> <!-- Page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Your CSS -->
    
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
<body> <!-- Start body -->

<div class="center"> <!-- Center layout wrapper -->
    <div class="container wide"> <!-- Wide container -->

        <h1>Order Status</h1> <!-- Page heading -->

        <p class="small"> <!-- Small text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["customer_email"]); ?></b> <!-- Show customer email -->
            | <a href="dashboard.php">Dashboard</a> <!-- Back link -->
            | <a href="logout.php">Logout</a> <!-- Logout link -->
        </p> <!-- End small text -->

        <div class="card"> <!-- Card container -->

            <div class="topbar"> <!-- Top bar row -->
                <div class="badge"> <!-- Badge style -->
                    Status: <b><?php echo htmlspecialchars($status_label); ?></b> <!-- Show friendly label -->
                </div> <!-- End badge -->

                <div class="badge"> <!-- Badge style -->
                    Auto refresh: <b>5s</b> <!-- Show refresh info -->
                </div> <!-- End badge -->
            </div> <!-- End topbar -->

            <p class="small"><?php echo htmlspecialchars($status_note); ?></p> <!-- Show status note -->

            <?php if ($order && $can_edit): ?> <!-- If order exists and editing is allowed -->
                <p style="margin-top:12px;"> <!-- Spacing -->
                    <a href="edit_order.php">Edit Order (before washing starts)</a> <!-- Edit link -->
                </p> <!-- End spacing -->
            <?php endif; ?> <!-- End edit check -->

        </div> <!-- End card -->

        <?php if ($order): ?> <!-- If order exists -->
            <div class="card"> <!-- Order info card -->

                <h2>Order Details</h2> <!-- Section title -->

                <p class="small">Order ID: <b><?php echo (int)$order["id"]; ?></b></p> <!-- Order id -->
                <p class="small">Plate: <b><?php echo htmlspecialchars($order["plate_no"]); ?></b></p> <!-- Plate -->
                <p class="small">Car Type: <b><?php echo htmlspecialchars($order["car_type"]); ?></b></p> <!-- Car type -->
                <p class="small">Car Color: <b><?php echo htmlspecialchars($order["car_color"]); ?></b></p> <!-- Car color -->
                <p class="small">Total (RM): <b><?php echo number_format((float)$order["total_price"], 2); ?></b></p> <!-- Total -->
                <p class="small">Created At: <b><?php echo htmlspecialchars($order["created_at"]); ?></b></p> <!-- Created at -->

                <?php if (strtoupper($order["status"]) === "DONE"): ?> <!-- If done -->
                    <p style="margin-top:12px;"> <!-- Spacing -->
                        <a href="receipt.php">Open Receipt</a> <!-- Receipt link -->
                        | <a href="receipt_pdf.php">Download PDF</a> <!-- PDF link -->
                    </p> <!-- End spacing -->
                <?php endif; ?> <!-- End done check -->

            </div> <!-- End order info card -->
        <?php endif; ?> <!-- End order exists check -->

        <div class="outro-container"> <!-- Footer container -->
            <p>Keep this page open. It will update automatically.</p> <!-- Footer note -->
        </div> <!-- End footer -->

    </div> <!-- End container -->
</div> <!-- End center -->

</body> <!-- End body -->
</html> <!-- End HTML -->
