<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // include database connection and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (login check, redirect, etc.)

if (!is_staff_logged_in()) { // check if staff is logged in
    redirect("login.php"); // if not logged in, redirect to login page
} // End check

update_staff_last_seen($conn); // update staff last active time in database

$order_id = (int)($_GET["id"] ?? 0); // get order id from URL (GET), convert to integer

if ($order_id <= 0) { // validate order id
    die("Invalid order id."); // stop execution if invalid
} // End check

$stmtO = $conn->prepare(" 
    SELECT 
        o.id, 
        o.plate_no, 
        o.car_color, 
        o.car_type, 
        o.status, 
        o.total_price, 
        o.created_at,
        s.name AS service_name,
        s.price AS service_price
    FROM orders o
    LEFT JOIN services s ON s.id = o.service_id
    WHERE o.id = ?
"); // prepare SQL query to get order details with service info

$stmtO->bind_param("i", $order_id); // bind order id as integer
$stmtO->execute(); // execute query
$order = $stmtO->get_result()->fetch_assoc(); // fetch result as associative array

if (!$order) { // check if order not found
    die("Order not found."); // stop execution
} // End

$stmtP = $conn->prepare(" 
    SELECT amount, payment_status, paid_at, txn_ref
    FROM payments
    WHERE order_id = ?
    ORDER BY id DESC
    LIMIT 1
"); // prepare SQL query to get latest payment record

$stmtP->bind_param("i", $order_id); // bind order id
$stmtP->execute(); // execute query
$payment = $stmtP->get_result()->fetch_assoc(); // fetch payment data

$addons = []; // initialize array to store add-ons

$stmtA = $conn->prepare(" 
    SELECT a.name, a.price
    FROM order_addons oa
    JOIN addons a ON a.id = oa.addon_id
    WHERE oa.order_id = ?
"); // prepare SQL query to get all add-ons for this order

$stmtA->bind_param("i", $order_id); // bind order id
$stmtA->execute(); // execute query
$resA = $stmtA->get_result(); // get result set

while ($row = $resA->fetch_assoc()) { // loop through each add-on record
    $addons[] = $row; // store each add-on into array
} // End loop

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design -->
    <meta http-equiv="refresh" content="5"> <!-- auto refresh page every 5 seconds -->
    <title>Receipt (Staff View)</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS -->
    
<style>
    body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;

    /* better background control */
    background-image: url('../assets/Staff_Background_Money.jpg?v=2');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

/* DARK OVERLAY (smoothed for better look) */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    
    background: rgba(0,0,0,0.75); /* lighter than before = nicer look */

    z-index: 0;
}

/* ensures ALL content stays above overlay */
body * {
    position: relative;
    z-index: 1;
}
</style>
</head>
<body>

<div class="center"> <!-- center wrapper -->
    <div class="container wide"> <!-- main container -->

        <h1>Receipt (Staff)</h1> <!-- page heading -->

        <p class="small"> <!-- small info text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["staff_username"]); ?></b> <!-- show logged-in staff username safely -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- link to dashboard -->
            | <a href="transactions.php">Transactions</a> <!-- link to transactions page -->
        </p>

        <div class="card"> <!-- order info section -->
            <h2>Order Info</h2> <!-- section title -->
            <p class="small">Order ID: <b><?php echo (int)$order["id"]; ?></b></p> <!-- display order id -->
            <p class="small">Plate: <b><?php echo htmlspecialchars($order["plate_no"]); ?></b></p> <!-- display plate number -->
            <p class="small">Car Type: <b><?php echo htmlspecialchars($order["car_type"]); ?></b></p> <!-- display car type -->
            <p class="small">Car Color: <b><?php echo htmlspecialchars($order["car_color"]); ?></b></p> <!-- display car color -->
            <p class="small">Order Status: <b><?php echo htmlspecialchars($order["status"]); ?></b></p> <!-- display order status -->
            <p class="small">Created At: <b><?php echo htmlspecialchars($order["created_at"]); ?></b></p> <!-- display order creation time -->
        </div>

        <div class="card"> <!-- service section -->
            <h2>Service</h2> <!-- section title -->
            <p class="small">
                <?php echo htmlspecialchars($order["service_name"] ?? "N/A"); ?> <!-- display service name or N/A -->
                (RM <?php echo number_format((float)($order["service_price"] ?? 0), 2); ?>) <!-- display service price formatted -->
            </p>
        </div>

        <div class="card"> <!-- add-ons section -->
            <h2>Add-ons</h2> <!-- section title -->

            <?php if (count($addons) === 0): ?> <!-- check if no add-ons -->
                <p class="small">- None</p> <!-- show none -->
            <?php else: ?> <!-- if add-ons exist -->
                <?php foreach ($addons as $a): ?> <!-- loop through add-ons -->
                    <p class="small">
                        - <?php echo htmlspecialchars($a["name"]); ?> <!-- display add-on name -->
                        (RM <?php echo number_format((float)$a["price"], 2); ?>) <!-- display add-on price -->
                    </p>
                <?php endforeach; ?> <!-- end loop -->
            <?php endif; ?> <!-- end condition -->
        </div>

        <div class="card"> <!-- payment section -->
            <h2>Payment</h2> <!-- section title -->

            <?php if ($payment): ?> <!-- check if payment exists -->
                <p class="small">Status: <b><?php echo htmlspecialchars($payment["payment_status"]); ?></b></p> <!-- payment status -->
                <p class="small">Paid At: <b><?php echo htmlspecialchars($payment["paid_at"]); ?></b></p> <!-- payment time -->
                <p class="small">Txn Ref: <b><?php echo htmlspecialchars($payment["txn_ref"]); ?></b></p> <!-- transaction reference -->
                <p class="small">Amount: <b>RM <?php echo number_format((float)$payment["amount"], 2); ?></b></p> <!-- payment amount -->
            <?php else: ?> <!-- if no payment record -->
                <div class="msg error">No payment record found for this order.</div> <!-- show error message -->
            <?php endif; ?> <!-- end condition -->
        </div>

        <div class="card"> <!-- actions section -->
            <h2>Actions</h2> <!-- section title -->
            <p class="small">
                <a href="receipt_pdf.php?id=<?php echo (int)$order["id"]; ?>">Download Receipt PDF</a> <!-- link to download receipt PDF -->
            </p>
        </div>

    </div>
</div>

</body>
</html>