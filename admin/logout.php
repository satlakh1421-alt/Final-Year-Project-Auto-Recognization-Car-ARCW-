<?php // Start PHP tag

require_once __DIR__ . "/../includes/config.php"; // Start session only

session_unset(); // Clear session variables
session_destroy(); // Destroy session

header("Location: login.php"); // Go back to admin login
exit(); // Stop script
