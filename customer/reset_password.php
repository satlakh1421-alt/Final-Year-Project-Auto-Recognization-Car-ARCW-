<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // include database connection and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (token hash, password check, etc.)

$msg = ""; // variable to store message (error/success)
$msg_type = ""; // variable to store message type for styling

$token = trim($_GET["t"] ?? ""); // get token from URL parameter "t" and remove spaces
$token_hash_value = token_hash($token); // hash the token to compare with stored hashed token in database

$valid = false; // flag to check whether token is valid
$reset_row = null; // variable to store reset record

if ($token !== "") { // check if token exists in URL
    $stmt = $conn->prepare("SELECT id, user_id, expires_at, used_at FROM password_resets WHERE user_type='customer' AND token_hash = ? ORDER BY id DESC LIMIT 1"); // find latest reset record using hashed token
    $stmt->bind_param("s", $token_hash_value); // bind hashed token
    $stmt->execute(); // execute query
    $reset_row = $stmt->get_result()->fetch_assoc(); // fetch result

    if ($reset_row) { // if record found
        $is_used = !empty($reset_row["used_at"]); // check if token already used
        $is_expired = (strtotime($reset_row["expires_at"]) < time()); // check if token expired

        if (!$is_used && !$is_expired) { // token is valid only if not used and not expired
            $valid = true; // mark token as valid
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check if form submitted
    $token_post = trim($_POST["t"] ?? ""); // get token from hidden input
    $new_password = $_POST["new_password"] ?? ""; // get new password input

    if ($token_post === "" || $new_password === "") { // validate input
        $msg = "Missing token or password."; // error message
        $msg_type = "error"; // type
    } else {

        // trong password check
        $pw_error = ""; // variable to store password error
        if (!is_strong_password($new_password, $pw_error)) { // check password strength
            $msg = $pw_error; // set error message
            $msg_type = "error"; // type
        } else {

            $hash_post = token_hash($token_post); // hash token again for validation

            $stmt2 = $conn->prepare("SELECT id, user_id, expires_at, used_at FROM password_resets WHERE user_type='customer' AND token_hash = ? ORDER BY id DESC LIMIT 1"); // find reset record
            $stmt2->bind_param("s", $hash_post); // bind hashed token
            $stmt2->execute(); // execute query
            $row2 = $stmt2->get_result()->fetch_assoc(); // fetch result

            if (!$row2) { // if no matching record
                $msg = "Invalid reset token."; // error
                $msg_type = "error"; // type
            } else {

                $is_used2 = !empty($row2["used_at"]); // check if already used
                $is_expired2 = (strtotime($row2["expires_at"]) < time()); // check if expired

                if ($is_used2 || $is_expired2) { // invalid if used or expired
                    $msg = "Token expired or already used."; // error message
                    $msg_type = "error"; // type
                } else {

                    $customer_id = (int)$row2["user_id"]; // get customer id
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT); // hash new password securely

                    $conn->begin_transaction(); // start database transaction (important for data consistency)

                    try {
                        $stmtU = $conn->prepare("UPDATE customers SET password_hash = ? WHERE id = ?"); // update customer password
                        $stmtU->bind_param("si", $new_hash, $customer_id); // bind values
                        $stmtU->execute(); // execute update

                        $used_at = date("Y-m-d H:i:s"); // current timestamp
                        $stmtM = $conn->prepare("UPDATE password_resets SET used_at = ? WHERE id = ?"); // mark token as used
                        $stmtM->bind_param("si", $used_at, $row2["id"]); // bind values
                        $stmtM->execute(); // execute update

                        $conn->commit(); // commit transaction (save both updates)

                        $msg = "Password reset successful. You can login now."; // success message
                        $msg_type = "success"; // type
                    } catch (Exception $e) { // if any error occurs
                        $conn->rollback(); // rollback transaction (undo changes)
                        $msg = "Reset failed. Please try again."; // error message
                        $msg_type = "error"; // type
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
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design -->
    <title>Reset Password (Customer)</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS -->
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('../assets/customer_background.jpg') no-repeat center center/cover;
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
<div class="center"> <!-- center wrapper -->
    <div class="container"> <!-- main container -->

        <h1>Reset Password</h1> <!-- page heading -->

        <?php if ($msg !== ""): ?> <!-- check if message exists -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- message box -->
                <?php echo htmlspecialchars($msg); ?> <!-- display message safely -->
            </div>
        <?php endif; ?> <!-- end condition -->

        <?php if (!$valid && $msg === ""): ?> <!-- if token invalid and no message yet -->
            <div class="msg error">Invalid / expired reset link.</div> <!-- show error -->
            <p style="margin-top:12px;"><a href="forgot_password.php">Back to Forgot Password</a></p> <!-- back link -->
        <?php else: ?> <!-- if token valid -->

            <form method="POST"> <!-- reset password form -->
                <input type="hidden" name="t" value="<?php echo htmlspecialchars($token); ?>"> <!-- hidden token field -->

                <label for="new_password">New Password</label> <!-- label -->
                <input id="new_password" name="new_password" type="password" class="input" required> <!-- password input -->

                <p class="small" style="margin-top:8px;"> <!-- helper text -->
                    Password must be at least 8 characters and include a letter, number, and special character (example: !@#).
                </p>

                <button type="submit">Reset Password</button> <!-- submit button -->
            </form>

            <p style="margin-top:12px;"><a href="login.php">Back to Login</a></p> <!-- back to login -->

        <?php endif; ?> <!-- end condition -->

    </div>
</div>
</body>
</html>