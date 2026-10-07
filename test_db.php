<?php
require_once "includes/db.php";

// Run SQL query to count how many rows are in services table
$result = $conn->query("SELECT COUNT(*) AS total FROM services");
$row = $result->fetch_assoc();

echo "Database Connected! Total services = " . $row["total"];
?>
