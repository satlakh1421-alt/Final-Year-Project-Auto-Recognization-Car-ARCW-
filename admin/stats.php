<?php // Start PHP tag

// Load database connection + start session
require_once __DIR__ . "/../includes/db.php"; // DB connection
require_once __DIR__ . "/../includes/functions.php"; // Helpers (redirect + login checks)

// If admin is not logged in, block access
if (!isset($_SESSION["admin_id"])) { // Check admin session (change if your admin session name is different)
    redirect("login.php"); // Send to admin login
} // End admin login check


// ----------------------------
// 1) ORDERS PER MONTH
// ----------------------------

// Prepare query: count orders grouped by YYYY-MM
$stmtOrders = $conn->prepare("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS ym, 
        COUNT(*) AS total_orders
    FROM orders
    GROUP BY ym
    ORDER BY ym ASC
"); // SQL: orders per month

$stmtOrders->execute(); // Execute query
$resOrders = $stmtOrders->get_result(); // Get result set

$ordersMap = []; // Store orders per month
$monthsSet = []; // Store all months found

while ($row = $resOrders->fetch_assoc()) { // Loop each month row
    $ym = $row["ym"]; // Read month key
    $ordersMap[$ym] = (int)$row["total_orders"]; // Save total orders
    $monthsSet[$ym] = true; // Mark this month exists
} // End loop


// ----------------------------
// 2) CUSTOMERS PER MONTH (Distinct customers in orders)
// ----------------------------

// Prepare query: count distinct customers grouped by YYYY-MM
$stmtCustomers = $conn->prepare("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS ym, 
        COUNT(DISTINCT customer_id) AS total_customers
    FROM orders
    GROUP BY ym
    ORDER BY ym ASC
"); // SQL: customers per month (based on orders)

$stmtCustomers->execute(); // Execute query
$resCustomers = $stmtCustomers->get_result(); // Get result set

$customersMap = []; // Store customers per month

while ($row = $resCustomers->fetch_assoc()) { // Loop each month row
    $ym = $row["ym"]; // Read month key
    $customersMap[$ym] = (int)$row["total_customers"]; // Save total customers
    $monthsSet[$ym] = true; // Mark this month exists
} // End loop


// ----------------------------
// 3) REVENUE PER MONTH (Payments SUCCESS)
// ----------------------------

// Prepare query: sum payments grouped by YYYY-MM (paid_at)
// Only count SUCCESS payments
$stmtRevenue = $conn->prepare("
    SELECT 
        DATE_FORMAT(paid_at, '%Y-%m') AS ym, 
        SUM(amount) AS total_revenue
    FROM payments
    WHERE payment_status = 'SUCCESS'
    GROUP BY ym
    ORDER BY ym ASC
"); // SQL: revenue per month

$stmtRevenue->execute(); // Execute query
$resRevenue = $stmtRevenue->get_result(); // Get result set

$revenueMap = []; // Store revenue per month

while ($row = $resRevenue->fetch_assoc()) { // Loop each month row
    $ym = $row["ym"]; // Read month key
    $revenueMap[$ym] = (float)$row["total_revenue"]; // Save revenue
    $monthsSet[$ym] = true; // Mark this month exists
} // End loop


// ----------------------------
// 4) BUILD FINAL MONTH LIST (Sorted)
// ----------------------------

// Convert months set to array
$months = array_keys($monthsSet); // Extract all months

sort($months); // Sort months ascending (YYYY-MM works with normal sort)


// ----------------------------
// 5) BUILD ARRAYS FOR CHART
// ----------------------------

$labels = []; // Month labels for chart
$ordersData = []; // Orders series
$customersData = []; // Customers series
$revenueData = []; // Revenue series

foreach ($months as $ym) { // Loop each month
    $labels[] = $ym; // Add label
    $ordersData[] = (int)($ordersMap[$ym] ?? 0); // Orders (default 0)
    $customersData[] = (int)($customersMap[$ym] ?? 0); // Customers (default 0)
    $revenueData[] = (float)($revenueMap[$ym] ?? 0); // Revenue (default 0)
} // End loop


// ----------------------------
// 6) SUMMARY TOTALS (All time)
// ----------------------------

$totalOrdersAll = array_sum($ordersData); // Total orders all months
$totalCustomersAll = array_sum($customersData); // Total customers sum (note: monthly sum, not unique all-time)
$totalRevenueAll = array_sum($revenueData); // Total revenue all months

?>
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- Start HTML -->
<head> <!-- Head start -->

    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Admin Statistics</title> <!-- Title -->

    <link rel="stylesheet" href="../assets/style.css"> <!-- Use your existing CSS -->

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;

    /* better background control */
    background-image: url('../assets/Admin_Work_Page.jpg?v=2');
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

    <!-- Chart.js (needs internet) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart library -->

</head> <!-- Head end -->
<body> <!-- Body start -->

    <div class="center"> <!-- Center wrapper -->
        <div class="container wide"> <!-- Wide container -->

            <h1>Monthly Statistics</h1> <!-- Heading -->

            <p class="small"> <!-- Small text line -->
                Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?></b> <!-- Show admin -->
                | <a href="dashboard.php">Back to Dashboard</a> <!-- Back link -->
                | <a href="logout.php">Logout</a> <!-- Logout link -->
            </p> <!-- End small -->

            <div class="card"> <!-- Summary card -->
                <h2>Summary (All Time)</h2> <!-- Title -->

                <p class="small">Total Orders: <b><?php echo (int)$totalOrdersAll; ?></b></p> <!-- Total orders -->
                <p class="small">Total Revenue (RM): <b><?php echo number_format((float)$totalRevenueAll, 2); ?></b></p> <!-- Total revenue -->

                <p class="small" style="margin-top:10px;"> <!-- Note -->
                    Customers/month is based on <b>distinct customer inside orders</b>. <!-- Explanation -->
                </p> <!-- End note -->
            </div> <!-- End summary card -->


            <div class="card"> <!-- Chart card -->
                <h2>Charts</h2> <!-- Title -->

                <p class="small" style="margin-bottom:10px;"> <!-- Info -->
                    If the chart does not load, your PC has no internet <!-- Note -->
                </p> <!-- End info -->

                <canvas id="ordersChart" height="140"></canvas> <!-- Orders chart -->
                <div style="height:16px;"></div> <!-- Space -->
                <canvas id="revenueChart" height="140"></canvas> <!-- Revenue chart -->

            </div> <!-- End chart card -->


            <div class="card"> <!-- Table card -->
                <h2>Monthly Table</h2> <!-- Title -->

                <div class="table-wrap"> <!-- Table wrap -->
                    <table> <!-- Table start -->
                        <tr> <!-- Header row -->
                            <th>Month</th> <!-- Month -->
                            <th>Customers</th> <!-- Customers -->
                            <th>Orders</th> <!-- Orders -->
                            <th>Revenue (RM)</th> <!-- Revenue -->
                        </tr> <!-- End header -->

                        <?php if (count($months) === 0): ?> <!-- If no data -->
                            <tr> <!-- Row -->
                                <td colspan="4">No data yet.</td> <!-- Message -->
                            </tr> <!-- End row -->
                        <?php else: ?> <!-- If data exists -->

                            <?php foreach ($months as $ym): ?> <!-- Loop months -->
                                <tr> <!-- Row -->
                                    <td><?php echo htmlspecialchars($ym); ?></td> <!-- Month -->
                                    <td><?php echo (int)($customersMap[$ym] ?? 0); ?></td> <!-- Customers -->
                                    <td><?php echo (int)($ordersMap[$ym] ?? 0); ?></td> <!-- Orders -->
                                    <td><?php echo number_format((float)($revenueMap[$ym] ?? 0), 2); ?></td> <!-- Revenue -->
                                </tr> <!-- End row -->
                            <?php endforeach; ?> <!-- End loop -->

                        <?php endif; ?> <!-- End check -->
                    </table> <!-- End table -->
                </div> <!-- End table wrap -->

            </div> <!-- End table card -->

        </div> <!-- End container -->
    </div> <!-- End center -->

<script> // Start JS

// Chart labels (months)
const labels = <?php echo json_encode($labels); ?>; // Month labels from PHP

// Orders dataset
const ordersData = <?php echo json_encode($ordersData); ?>; // Orders per month

// Customers dataset
const customersData = <?php echo json_encode($customersData); ?>; // Customers per month

// Revenue dataset
const revenueData = <?php echo json_encode($revenueData); ?>; // Revenue per month


// 1) Orders + Customers chart
const ctxOrders = document.getElementById("ordersChart"); // Get canvas

new Chart(ctxOrders, { // Create chart
    type: "line", // Line chart
    data: { // Data object
        labels: labels, // X labels
        datasets: [ // Datasets list
            { // Orders dataset
                label: "Orders", // Label
                data: ordersData, // Data
                tension: 0.25 // Smooth curve
            }, // End orders dataset
            { // Customers dataset
                label: "Customers (Distinct in Orders)", // Label
                data: customersData, // Data
                tension: 0.25 // Smooth curve
            } // End customers dataset
        ] // End datasets
    }, // End data
    options: { // Options
        responsive: true, // Responsive
        plugins: { // Plugins
            legend: { display: true } // Show legend
        } // End plugins
    } // End options
}); // End chart


// 2) Revenue chart
const ctxRevenue = document.getElementById("revenueChart"); // Get canvas

new Chart(ctxRevenue, { // Create chart
    type: "bar", // Bar chart
    data: { // Data object
        labels: labels, // X labels
        datasets: [ // Datasets list
            { // Revenue dataset
                label: "Revenue (RM)", // Label
                data: revenueData // Data
            } // End revenue dataset
        ] // End datasets
    }, // End data
    options: { // Options
        responsive: true, // Responsive
        plugins: { // Plugins
            legend: { display: true } // Show legend
        } // End plugins
    } // End options
}); // End chart

</script> <!-- End JS -->

</body> <!-- End body -->
</html> <!-- End HTML -->
