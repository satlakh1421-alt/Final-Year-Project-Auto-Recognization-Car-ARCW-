<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions (redirect + login checks)

if (!is_admin_logged_in()) { // If admin not logged in
    redirect("login.php"); // Send admin to login
} // End admin login check

// Read optional search plate from URL (example: visits.php?plate=ABC)
$search_plate = trim($_GET["plate"] ?? ""); // Read plate search input safely

// Base SQL: show orders (this is the visit history)
$sql = "
    SELECT 
        o.id,                 -- Order ID
        o.plate_no,           -- Plate number
        o.car_type,           -- Car type
        o.car_color,          -- Car color
        o.status,             -- Wash workflow status
        o.total_price,        -- Total price
        o.created_at,         -- Order created time (visit time)
        c.email AS customer_email -- Customer email
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
"; // End base SQL

$params = []; // Prepare params array
$types = ""; // Prepare bind types string

if ($search_plate !== "") { // If admin typed something
    $sql .= " WHERE o.plate_no LIKE ? "; // Add WHERE filter
    $params[] = "%" . $search_plate . "%"; // Add wildcard value
    $types .= "s"; // Add type string
} // End search filter

$sql .= " ORDER BY o.id DESC "; // Show latest first

$stmt = $conn->prepare($sql); // Prepare SQL statement

if ($types !== "") { // If we have parameters
    $stmt->bind_param($types, ...$params); // Bind parameters
} // End bind

$stmt->execute(); // Execute query
$result = $stmt->get_result(); // Get results

?>
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- HTML start -->
<head> <!-- Head start -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>All Car Visit History</title> <!-- Title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Main CSS -->
    
    <!-- BACKGROUND STYLE (ADDED) -->
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
    
</head> <!-- Head end -->
<body> <!-- Body start -->

<div class="center"> <!-- Center wrapper -->
    <div class="container wide"> <!-- Wide container -->

        <h1>All Car Visit History</h1> <!-- Heading -->

        <p class="small"> <!-- Info line -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?></b> <!-- Admin name -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- Back -->
            | <a href="logout.php">Logout</a> <!-- Logout -->
        </p> <!-- End info line -->

        <div class="card"> <!-- Search card -->
            <h2>Search by Plate</h2> <!-- Title -->

            <form method="GET"> <!-- GET form -->
                <label for="plate">Plate Number</label> <!-- Label -->
                <input id="plate" name="plate" type="text" class="input" placeholder="Example: ABC1234" value="<?php echo htmlspecialchars($search_plate); ?>"> <!-- Input -->
                <button type="submit">Search</button> <!-- Button -->
            </form> <!-- End form -->
        </div> <!-- End card -->

        <div class="table-wrap"> <!-- Table wrapper -->
            <table> <!-- Table start -->

                <tr> <!-- Header row -->
                    <th>Order ID</th> <!-- Column -->
                    <th>Plate</th> <!-- Column -->
                    <th>Customer</th> <!-- Column -->
                    <th>Car Type</th> <!-- Column -->
                    <th>Car Color</th> <!-- Column -->
                    <th>Total (RM)</th> <!-- Column -->
                    <th>Visit Time and Date</th> <!-- Column -->
                </tr> <!-- End header -->

                <?php if ($result && $result->num_rows > 0): ?> <!-- If data exists -->

                    <?php while ($row = $result->fetch_assoc()): ?> <!-- Loop rows -->
                        <tr> <!-- Row start -->
                            <td><?php echo (int)$row["id"]; ?></td> <!-- Order ID -->
                            <td><?php echo htmlspecialchars($row["plate_no"]); ?></td> <!-- Plate -->
                            <td><?php echo htmlspecialchars($row["customer_email"] ?? "-"); ?></td> <!-- Customer -->
                            <td><?php echo htmlspecialchars($row["car_type"] ?? "-"); ?></td> <!-- Car type -->
                            <td><?php echo htmlspecialchars($row["car_color"] ?? "-"); ?></td> <!-- Car color -->
                            <td><?php echo number_format((float)($row["total_price"] ?? 0), 2); ?></td> <!-- Total -->
                            <td><?php echo htmlspecialchars($row["created_at"] ?? "-"); ?></td> <!-- Visit time -->
                        </tr> <!-- Row end -->
                    <?php endwhile; ?> <!-- End loop -->

                <?php else: ?> <!-- No data -->
                    <tr> <!-- Empty row -->
                        <td colspan="7">No visit history found.</td> <!-- Message -->
                    </tr> <!-- End empty row -->
                <?php endif; ?> <!-- End check -->

            </table> <!-- End table -->
        </div> <!-- End table wrapper -->

    </div> <!-- End container -->
</div> <!-- End center -->

</body> <!-- End body -->
</html> <!-- End HTML -->
