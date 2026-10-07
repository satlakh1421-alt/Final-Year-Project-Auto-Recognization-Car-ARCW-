<?php // Start PHP tag

function generate_token($length = 20) { // Function to generate random token
    return bin2hex(random_bytes((int)($length / 2))); // Return token as hex
} // End function

function token_hash($token) { // Function to hash a token (so we don't store raw token)
    return hash("sha256", $token); // Return SHA256 hash
} // End function

function redirect($url) { // Redirect helper
    header("Location: " . $url); // Send header redirect
    exit(); // Stop script
} // End function

function is_customer_logged_in() { // Check customer login
    return isset($_SESSION["customer_id"]); // True if session exists
} // End function

function is_staff_logged_in() { // Check staff login
    return isset($_SESSION["staff_id"]); // True if session exists
} // End function

function is_admin_logged_in() { // Check admin login
    return isset($_SESSION["admin_id"]); // True if session exists
} // End function

function update_staff_last_seen($conn) { // Update staff last_seen for online tracking
    if (!isset($_SESSION["staff_log_id"])) { // If no log id in session
        return; // Stop
    } // End check

    $now = date("Y-m-d H:i:s"); // Current datetime

    $stmt = $conn->prepare("UPDATE staff_logs SET last_seen = ? WHERE id = ?"); // Prepare update query
    $stmt->bind_param("si", $now, $_SESSION["staff_log_id"]); // Bind datetime + log id
    $stmt->execute(); // Execute query
} // End function

// Strong password validation
// Rules: min 8 chars, at least 1 letter, 1 number, 1 special char
function is_strong_password($password, &$error = "") {
    $password = (string)$password;

    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
        return false;
    }
    if (!preg_match("/[A-Za-z]/", $password)) {
        $error = "Password must include at least 1 letter.";
        return false;
    }
    if (!preg_match("/[0-9]/", $password)) {
        $error = "Password must include at least 1 number.";
        return false;
    }
    if (!preg_match("/[^A-Za-z0-9]/", $password)) {
        $error = "Password must include at least 1 special character (example: !@#).";
        return false;
    }

    return true;
}
?>