<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // Load database connection + session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions

if (!is_staff_logged_in()) { // If staff is not logged in
    redirect("login.php"); // Redirect to staff login
} // End login check

if ($_SERVER["REQUEST_METHOD"] !== "POST") { // If request is not POST
    redirect("dashboard.php"); // Redirect back to dashboard
} // End method check

$order_id = (int)($_POST["order_id"] ?? 0); // Read order_id safely as integer
$new_status = trim($_POST["new_status"] ?? ""); // Read new_status safely as string

$allowed_next = ["WASHING", "DONE"]; // Allowed statuses staff can set (workflow control)

if ($order_id <= 0) { // If order_id is invalid
    redirect("dashboard.php"); // Go back
} // End order_id validation

if (!in_array($new_status, $allowed_next, true)) { // If new status is not allowed
    redirect("dashboard.php"); // Go back
} // End status validation

$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?"); // Prepare query to get current status
$stmt->bind_param("i", $order_id); // Bind order id
$stmt->execute(); // Execute query
$row = $stmt->get_result()->fetch_assoc(); // Fetch order row

if (!$row) { // If order not found
    redirect("dashboard.php"); // Go back
} // End not found check

$current_status = $row["status"]; // Store current order status

// Enforce correct workflow:
// Only allow PAID -> WASHING
// Only allow WASHING -> DONE
if ($new_status === "WASHING" && $current_status !== "PAID") { // If trying to start wash but order not PAID
    redirect("dashboard.php"); // Reject and go back
} // End PAID->WASHING rule

if ($new_status === "DONE" && $current_status !== "WASHING") { // If trying to finish but order not WASHING
    redirect("dashboard.php"); // Reject and go back
} // End WASHING->DONE rule

$stmtU = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?"); // Prepare update statement
$stmtU->bind_param("si", $new_status, $order_id); // Bind new status and order id
$stmtU->execute(); // Execute update query

redirect("dashboard.php"); // Return to dashboard after update
?>
