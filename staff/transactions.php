<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // include database connection and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (login check, redirect, etc.)

// If staff not logged in
if (!is_staff_logged_in()) { // check if staff session exists (user logged in)
    redirect("login.php"); // if not logged in, redirect to login page
} // End check

// Update staff last_seen
update_staff_last_seen($conn); // update staff last active time in database

$sql = " 
    SELECT 
        p.id AS payment_id,
        p.amount,
        p.payment_status,
        p.paid_at,
        p.txn_ref,
        o.id AS order_id,
        o.plate_no
    FROM payments p
    JOIN orders o ON o.id = p.order_id
    ORDER BY p.id DESC
"; // SQL query to get all payment records with related order info, sorted by latest first

$result = $conn->query($sql); // execute SQL query and store result

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design -->
    <title>Transaction Records</title> <!-- page title -->
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

        <h1>Transaction Records</h1> <!-- page heading -->

        <p class="small"> <!-- small info text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["staff_username"]); ?></b> <!-- display logged-in staff username safely -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- link back to dashboard -->
            | <a href="logout.php">Logout</a> <!-- logout link -->
        </p>

        <div class="card"> <!-- card container -->
            <h2>All Payments</h2> <!-- section title -->

            <div class="table-wrap"> <!-- table wrapper for styling/scroll -->
                <table> <!-- start table -->
                    <tr> <!-- table header row -->
                        <th>ID</th> <!-- payment ID column -->
                        <th>Order</th> <!-- order ID column -->
                        <th>Plate</th> <!-- car plate column -->
                        <th>Amount (RM)</th> <!-- payment amount column -->
                        <th>Status</th> <!-- payment status column -->
                        <th>Date</th> <!-- payment date column -->
                        <th>Reference</th> <!-- transaction reference column -->
                    </tr>

                    <?php if ($result && $result->num_rows > 0): ?> <!-- check if query returned data -->
                        <?php while ($row = $result->fetch_assoc()): ?> <!-- loop through each record -->
                            <tr> <!-- data row -->
                                <td><?php echo (int)$row["payment_id"]; ?></td> <!-- display payment ID -->
                                <td><?php echo (int)$row["order_id"]; ?></td> <!-- display order ID -->
                                <td><?php echo htmlspecialchars($row["plate_no"]); ?></td> <!-- display plate number safely -->
                                <td><?php echo number_format((float)$row["amount"], 2); ?></td> <!-- format amount to 2 decimal places -->
                                <td><?php echo htmlspecialchars($row["payment_status"]); ?></td> <!-- display payment status -->
                                <td><?php echo htmlspecialchars($row["paid_at"]); ?></td> <!-- display payment date -->
                                <td><?php echo htmlspecialchars($row["txn_ref"]); ?></td> <!-- display transaction reference -->
                            </tr>
                        <?php endwhile; ?> <!-- end loop -->
                    <?php else: ?> <!-- if no data found -->
                        <tr>
                            <td colspan="7">No transactions found.</td> <!-- show message across all columns -->
                        </tr>
                    <?php endif; ?> <!-- end condition -->

                </table> <!-- end table -->
            </div>
        </div>

    </div>
</div>

</body>
</html>