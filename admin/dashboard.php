<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions

// If admin is not logged in
if (!isset($_SESSION["admin_id"])) { // Check admin session
    redirect("login.php"); // Redirect to admin login
} // End login check

// Get counts for summary cards
$cnt_customers = 0; // Default customers count
$cnt_orders = 0; // Default orders count
$cnt_paid = 0; // Default paid orders count
$cnt_revenue = 0.00; // Default revenue

// Count customers
$res1 = $conn->query("SELECT COUNT(*) AS c FROM customers"); // Query total customers
if ($res1) { // If query ok
    $cnt_customers = (int)($res1->fetch_assoc()["c"] ?? 0); // Read count
} // End customers count

// Count orders
$res2 = $conn->query("SELECT COUNT(*) AS c FROM orders"); // Query total orders
if ($res2) { // If query ok
    $cnt_orders = (int)($res2->fetch_assoc()["c"] ?? 0); // Read count
} // End orders count

// Count PAID/WASHING/DONE (payment received)
$res3 = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status IN ('PAID','WASHING','DONE')"); // Paid or beyond
if ($res3) { // If query ok
    $cnt_paid = (int)($res3->fetch_assoc()["c"] ?? 0); // Read count
} // End paid count

// Sum revenue from successful payments (best source is payments table)
$res4 = $conn->query("SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE payment_status='SUCCESS'"); // Revenue sum
if ($res4) { // If query ok
    $cnt_revenue = (float)($res4->fetch_assoc()["s"] ?? 0); // Read sum
} // End revenue sum

?>
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- Start HTML -->
<head> <!-- Head start -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Admin Dashboard</title> <!-- Title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Same CSS -->
    
    <!-- BACKGROUND STYLE (ADDED) -->
<style>
        body {
            margin: 0;
            padding: 0;
            background: url('../assets/Staff_BackgroundWorkshop.jpg') no-repeat center center/cover;
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
    </style>
    
</head> <!-- Head end -->
<body> <!-- Body start -->

<div class="page"> <!-- Page wrapper -->

    <div class="topbar"> <!-- Topbar row -->
        <div class="badge">Boss / Admin Dashboard</div> <!-- Badge -->
        <div class="badge">
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"]); ?></b> <!-- Admin name -->
            | <a href="logout.php">Logout</a> <!-- Logout link -->
        </div> <!-- End badge -->
    </div> <!-- End topbar -->

   <div class="center"> <!-- Center wrapper -->
        <div class="container wide"> <!-- Wide container -->
        <h1>Admin Dashboard</h1> <!-- Title -->

        <div class="row"> <!-- Row for cards -->

            <div class="col"> <!-- Column -->
                <div class="card"> <!-- Card -->
                    <h3>Total Registered Customers</h3> <!-- Title -->
                    <p style="font-size:28px; font-weight:800; margin-top:6px;">
                        <?php echo (int)$cnt_customers; ?> <!-- Print customers -->
                    </p>
                </div> <!-- End card -->
            </div> <!-- End col -->

            <div class="col"> <!-- Column -->
                <div class="card"> <!-- Card -->
                    <h3>Total Orders Inluding Pending</h3> <!-- Title -->
                    <p style="font-size:28px; font-weight:800; margin-top:6px;">
                        <?php echo (int)$cnt_orders; ?> <!-- Print orders -->
                    </p>
                </div> <!-- End card -->
            </div> <!-- End col -->

        </div> <!-- End row -->

        <div class="row"> <!-- Row for cards -->

            <div class="col"> <!-- Column -->
                <div class="card"> <!-- Card -->
                    <h3>Total Orders Paid / In Progress / Done</h3> <!-- Title -->
                    <p style="font-size:28px; font-weight:800; margin-top:6px;">
                        <?php echo (int)$cnt_paid; ?> <!-- Print paid-ish -->
                    </p>
                </div> <!-- End card -->
            </div> <!-- End col -->

            <div class="col"> <!-- Column -->
                <div class="card"> <!-- Card -->
                    <h3>Total Revenue (RM)</h3> <!-- Title -->
                    <p style="font-size:28px; font-weight:800; margin-top:6px;">
                        <?php echo number_format((float)$cnt_revenue, 2); ?> <!-- Print revenue -->
                    </p>
                </div> <!-- End card -->
            </div> <!-- End col -->

        </div> <!-- End row -->

        <div class="card"> <!-- Links card -->
            <h2>Management</h2> <!-- Section title -->
            <p class="small">
                <a href="visits.php">All Car Visit History</a> <!-- Link -->
                | <a href="transactions.php">Customer Transactions</a> <!-- Link -->
                | <a href="staff_logs.php">Staff Login/Logout Logs</a> <!-- Link -->
                | <a href="stats.php">Monthly Stats (Charts)</a> <!-- Link -->
                | <a href="staff_manage.php">Manage Staff</a><!-- Link -->

            </p> <!-- End small -->
        </div> <!-- End card -->

    </div> <!-- End container -->

</div> <!-- End page -->

</body> <!-- End body -->
</html> <!-- End html -->
