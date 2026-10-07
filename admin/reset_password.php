<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session automatically
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as token hashing, redirect helpers, and strong password validation

$msg = ""; // variable to store feedback message text for the user
$msg_type = ""; // variable to store the type of message, usually "error" or "success", for CSS styling

$token = trim($_GET["t"] ?? ""); // read reset token from URL parameter "t" and remove extra spaces from beginning/end
$token_hash_value = token_hash($token); // hash the token so it can be compared securely with the hashed token stored in the database

$valid = false; // default token validity is false until proven valid

if ($token !== "") { // only check database if token is not empty
    $stmt = $conn->prepare("SELECT id, user_id, expires_at, used_at FROM password_resets WHERE user_type='admin' AND token_hash = ? ORDER BY id DESC LIMIT 1"); // prepare query to get the latest matching admin reset record using hashed token
    $stmt->bind_param("s", $token_hash_value); // bind hashed token as string to the prepared statement
    $stmt->execute(); // execute query
    $row = $stmt->get_result()->fetch_assoc(); // fetch one matching row as associative array

    if ($row) { // if a matching reset record exists
        $is_used = !empty($row["used_at"]); // token is considered used if used_at is not empty
        $is_expired = (strtotime($row["expires_at"]) < time()); // token is expired if expires_at time is earlier than current time
        if (!$is_used && !$is_expired) { // token is valid only when it is not used and not expired
            $valid = true; // mark token as valid so reset form can be shown
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check whether reset form has been submitted
    $token_post = trim($_POST["t"] ?? ""); // read token from hidden form field and trim extra spaces
    $new_password = $_POST["new_password"] ?? ""; // read newly entered password

    if ($token_post === "" || $new_password === "") { // validate required inputs
        $msg = "Missing token or password."; // set error message if token or password is missing
        $msg_type = "error"; // set message type to error
    } else {

        // strong password check
        $pw_error = ""; // variable to receive password validation error message from helper function
        if (!is_strong_password($new_password, $pw_error)) { // check whether new password meets strong password rules
            $msg = $pw_error; // show detailed password rule error returned by helper function
            $msg_type = "error"; // set message type to error
        } else {

            $hash_post = token_hash($token_post); // hash posted token before checking it in database

            $stmt2 = $conn->prepare("SELECT id, user_id, expires_at, used_at FROM password_resets WHERE user_type='admin' AND token_hash = ? ORDER BY id DESC LIMIT 1"); // prepare query to load the latest matching admin reset record again during form submission
            $stmt2->bind_param("s", $hash_post); // bind hashed posted token as string
            $stmt2->execute(); // execute query
            $row2 = $stmt2->get_result()->fetch_assoc(); // fetch matching reset record

            if (!$row2) { // if no reset record matches this token
                $msg = "Invalid reset token."; // show invalid token message
                $msg_type = "error"; // set error type
            } else {

                $is_used2 = !empty($row2["used_at"]); // check whether token has already been used
                $is_expired2 = (strtotime($row2["expires_at"]) < time()); // check whether token is expired

                if ($is_used2 || $is_expired2) { // reject token if already used or expired
                    $msg = "Token expired or already used."; // show error message
                    $msg_type = "error"; // set type to error
                } else {

                    $admin_id = (int)$row2["user_id"]; // get admin user ID from reset record and convert to integer
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT); // securely hash the new password before saving into database

                    $conn->begin_transaction(); // start database transaction so both updates succeed together or fail together

                    try { // start try block for safe transaction handling
                        $stmtU = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?"); // prepare query to update admin password in admin_users table
                        $stmtU->bind_param("si", $new_hash, $admin_id); // bind new password hash and admin ID
                        $stmtU->execute(); // execute password update

                        $used_at = date("Y-m-d H:i:s"); // get current date and time to mark when token was used
                        $stmtM = $conn->prepare("UPDATE password_resets SET used_at = ? WHERE id = ?"); // prepare query to mark reset token as used
                        $stmtM->bind_param("si", $used_at, $row2["id"]); // bind used timestamp and password reset row ID
                        $stmtM->execute(); // execute token-used update

                        $conn->commit(); // commit transaction so both password update and token update are saved permanently

                        $msg = "Admin password reset successful. You can login now."; // show success message
                        $msg_type = "success"; // set success type
                    } catch (Exception $e) { // catch any exception if one of the database operations fails
                        $conn->rollback(); // undo all changes made inside transaction
                        $msg = "Reset failed. Please try again."; // show generic error message
                        $msg_type = "error"; // set error type
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding as UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- make layout responsive on phone, tablet, and desktop -->
    <title>Reset Password (Admin)</title> <!-- page title shown in browser tab -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- load external stylesheet for page styling -->
    
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
    
    
</head>
<body>
<div class="center"> <!-- wrapper used to center content area -->
    <div class="container"> <!-- main content container -->

        <h1>Admin Reset Password</h1> <!-- main page heading -->

        <?php if ($msg !== ""): ?> <!-- if there is any success or error message to display -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- message box with dynamic class based on message type -->
                <?php echo htmlspecialchars($msg); ?> <!-- safely display feedback message to avoid XSS -->
            </div>
        <?php endif; ?>

        <?php if (!$valid && $msg === ""): ?> <!-- if token is invalid/expired and no submitted-form message exists yet -->
            <div class="msg error">Invalid / expired reset link.</div> <!-- show invalid or expired link message -->
            <p style="margin-top:12px;"><a href="forgot_password.php">Back</a></p> <!-- link back to forgot password page -->
        <?php else: ?> <!-- if token is valid, show reset password form -->

            <form method="POST"> <!-- form submits using POST method -->
                <input type="hidden" name="t" value="<?php echo htmlspecialchars($token); ?>"> <!-- hidden input keeps token available during form submission -->

                <label for="new_password">New Password</label> <!-- label for new password field -->
                <input id="new_password" name="new_password" type="password" class="input" required> <!-- password input for new admin password -->

                <p class="small" style="margin-top:8px;"> <!-- helper text below password field -->
                    Password must be at least 8 characters and include a letter, number, and special character (example: !@#).
                </p>

                <button type="submit">Reset Password</button> <!-- submit button to complete password reset -->
            </form>

            <p style="margin-top:12px;"><a href="login.php">Back to Admin Login</a></p> <!-- link back to admin login page -->

        <?php endif; ?>

    </div>
</div>
</body>
</html>