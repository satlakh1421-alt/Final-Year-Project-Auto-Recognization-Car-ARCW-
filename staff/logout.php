<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB + session
require_once __DIR__ . "/../includes/functions.php"; // Load helpers

// If log id exists, update logout_time + last_seen
if (isset($_SESSION["staff_log_id"])) { // Check staff log id exists

    $now = date("Y-m-d H:i:s"); // Current datetime

    // Update logout_time and last_seen so admin sees OFFLINE immediately
    $stmt = $conn->prepare("UPDATE staff_logs SET logout_time = ?, last_seen = ? WHERE id = ?"); // Prepare query
    $stmt->bind_param("ssi", $now, $now, $_SESSION["staff_log_id"]); // Bind values
    $stmt->execute(); // Execute update

} // End log update

session_unset(); // Clear session
session_destroy(); // Destroy session
header("Location: login.php"); // Redirect to login
exit(); // Stop script

?>
