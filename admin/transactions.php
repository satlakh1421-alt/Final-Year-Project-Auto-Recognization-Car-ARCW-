<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as login checking and redirect

if (!is_admin_logged_in()) { // check whether current user is logged in as admin
    redirect("login.php"); // if admin session does not exist, redirect to admin login page
}

// Optional filters
$search_plate = trim($_GET["plate"] ?? ""); // get plate search value from URL using GET and remove extra spaces
$search_email = trim($_GET["email"] ?? ""); // get email search value from URL if provided, also remove extra spaces

// Base SQL (payments + order + customer)
$sql = "
    SELECT
        p.id AS payment_id,
        p.amount,
        p.payment_status,
        p.paid_at,
        p.txn_ref,
        o.id AS order_id,
        o.plate_no,
        o.status AS order_status,
        c.email AS customer_email
    FROM payments p
    JOIN orders o ON o.id = p.order_id
    LEFT JOIN customers c ON c.id = o.customer_id
"; // main SQL query to join payments table with orders table and customers table

// WHERE conditions
$where = []; // array to store dynamic WHERE conditions
$params = []; // array to store values that will be bound into prepared statement
$types = ""; // string to store parameter types for bind_param, for example "s" for string

// FORCE: only show completed jobs
$where[] = "p.payment_status = 'SUCCESS'"; // only show rows where payment was successful
$where[] = "o.status = 'DONE'"; // only show rows where order/wash process is completed

// Optional plate filter
if ($search_plate !== "") { // if admin entered a plate number search
    $where[] = "o.plate_no LIKE ?"; // add SQL condition for matching plate number
    $params[] = "%" . $search_plate . "%"; // add wildcard search value so partial matches can work
    $types .= "s"; // add string type because plate number search value is a string
}

// Optional email filter (for future use)
if ($search_email !== "") { // if email filter is provided
    $where[] = "c.email LIKE ?"; // add SQL condition for matching customer email
    $params[] = "%" . $search_email . "%"; // use wildcard search for partial email matching
    $types .= "s"; // add one more string type for bind_param
}

// Apply WHERE
if (count($where) > 0) { // if there is at least one WHERE condition
    $sql .= " WHERE " . implode(" AND ", $where); // combine all conditions using AND and append to SQL
}

$sql .= " ORDER BY p.id DESC "; // sort latest payment records first based on payment id descending

$stmt = $conn->prepare($sql); // prepare SQL statement safely

if (!$stmt) { // check if prepare failed
    die("Prepare failed: " . $conn->error); // show actual database prepare error for debugging
}

// Bind params if needed
if ($types !== "") { // if there are dynamic parameters to bind
    $stmt->bind_param($types, ...$params); // bind all parameters dynamically using spread operator
}

if (!$stmt->execute()) { // execute statement and check if execution failed
    die("Execute failed: " . $stmt->error); // show actual execution error for debugging
}

$result = $stmt->get_result(); // get final result set from executed query

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- make page responsive on mobile and desktop -->
    <title>Customer Transactions</title> <!-- page title shown in browser tab -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS stylesheet -->
    
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
    
</head>
<body>

<div class="center"> <!-- wrapper to center page content -->
    <div class="container wide"> <!-- wide container to hold full transaction table -->

        <h1>Customer Transactions</h1> <!-- main page heading -->

        <p class="small"> <!-- small info text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?></b> <!-- safely show admin username from session -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- link back to admin dashboard -->
            | <a href="logout.php">Logout</a> <!-- logout link -->
        </p>

        <div class="card"> <!-- card section for filters -->
            <h2>Search Filters</h2> <!-- section heading -->

            <p class="small" style="margin-bottom:10px;"> <!-- explanation text -->
                Showing only: <b>Payment SUCCESS</b> and <b>Order DONE</b> <!-- show current fixed filters -->
            </p>

            <form method="GET"> <!-- search form using GET so values appear in URL -->
                <label for="plate">Plate :</label> <!-- label for plate input -->
                <input id="plate" name="plate" type="text" class="input"
                       placeholder="Example: ABC1234"
                       value="<?php echo htmlspecialchars($search_plate); ?>"> <!-- keep previous search value after submit -->

                <button type="submit">Search</button> <!-- submit search button -->
            </form>
        </div>

        <div class="table-wrap"> <!-- wrapper for transaction table -->
            <table> <!-- start table -->

                <tr> <!-- table header row -->
                    <th>Payment ID</th> <!-- payment id column -->
                    <th>Order ID</th> <!-- order id column -->
                    <th>Plate</th> <!-- plate number column -->
                    <th>Customer Email</th> <!-- customer email column -->
                    <th>Amount (RM)</th> <!-- payment amount column -->
                    <th>Payment Status</th> <!-- payment status column -->
                    <th>Order Status</th> <!-- order status column -->
                    <th>Paid At</th> <!-- payment date/time column -->
                    <th>Txn Ref</th> <!-- transaction reference column -->
                </tr>

                <?php if ($result && $result->num_rows > 0): ?> <!-- check whether query returned at least one row -->
                    <?php while ($row = $result->fetch_assoc()): ?> <!-- loop through all returned rows one by one -->
                        <tr> <!-- table data row -->
                            <td><?php echo (int)$row["payment_id"]; ?></td> <!-- display payment id as integer -->
                            <td><?php echo (int)$row["order_id"]; ?></td> <!-- display order id as integer -->
                            <td><?php echo htmlspecialchars($row["plate_no"]); ?></td> <!-- safely display vehicle plate number -->
                            <td><?php echo htmlspecialchars($row["customer_email"] ?? "-"); ?></td> <!-- safely display customer email or dash if empty -->
                            <td><?php echo number_format((float)$row["amount"], 2); ?></td> <!-- format amount to 2 decimal places -->
                            <td><?php echo htmlspecialchars($row["payment_status"]); ?></td> <!-- safely display payment status -->
                            <td><?php echo htmlspecialchars($row["order_status"]); ?></td> <!-- safely display order status -->
                            <td><?php echo htmlspecialchars($row["paid_at"] ?? "-"); ?></td> <!-- safely display payment time or dash -->
                            <td><?php echo htmlspecialchars($row["txn_ref"] ?? "-"); ?></td> <!-- safely display transaction reference or dash -->
                        </tr>
                    <?php endwhile; ?> <!-- end loop -->
                <?php else: ?> <!-- if query returned no rows -->
                    <tr>
                        <td colspan="9">No completed transactions found.</td> <!-- show message across all 9 columns -->
                    </tr>
                <?php endif; ?> <!-- end result check -->

            </table> <!-- end table -->
        </div>

    </div>
</div>

</body>
</html>