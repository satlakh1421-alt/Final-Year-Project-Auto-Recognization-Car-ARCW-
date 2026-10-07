<?php // Start PHP

require_once __DIR__ . "/../includes/db.php"; // include database connection file and automatically start session handling
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as token generation, hashing, redirect, validation, etc.
require_once __DIR__ . "/../includes/mailer.php"; // include mailer functions used to send reset email to admin

$msg = ""; // variable to store feedback message text (either error or success)
$msg_type = ""; // variable to store message type (used for styling, e.g., "error" or "success")

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check if form is submitted using POST method

    $username = trim($_POST["username"] ?? ""); // read admin username from form input and remove leading/trailing spaces

    if ($username === "") { // check if username is empty
        $msg = "Please enter admin username."; // set error message
        $msg_type = "error"; // set message type to error
    } else { // if username is provided, continue process

        $stmt = $conn->prepare("SELECT id, email FROM admin_users WHERE username = ? LIMIT 1"); // prepare SQL query to find admin by username
        $stmt->bind_param("s", $username); // bind username safely to prevent SQL injection
        $stmt->execute(); // execute query
        $row = $stmt->get_result()->fetch_assoc(); // fetch result as associative array
        $stmt->close(); // close prepared statement to free resources

        if (!$row) { // if no admin account found with this username
            $msg = "Admin account not found."; // set error message
            $msg_type = "error"; // set type
        } elseif (empty($row["email"])) { // if admin exists but email field is empty
            $msg = "Admin email is not set. Please contact system administrator."; // set error message
            $msg_type = "error"; // set type
        } else { // if admin exists and email is available

            $admin_id = (int)$row["id"]; // get admin ID and convert to integer for safety
            $admin_email = trim($row["email"]); // get admin email and remove extra spaces
            $token = generate_token(40); // generate a secure random token (length 40 characters)
            $hash = token_hash($token); // hash the token before storing in database (security best practice)
            $expires_at = date("Y-m-d H:i:s", time() + 3600); // set expiration time to 1 hour from now

            $stmtI = $conn->prepare("INSERT INTO password_resets (user_type, user_id, token_hash, expires_at) VALUES ('admin', ?, ?, ?)"); // prepare insert query to store reset token
            $stmtI->bind_param("iss", $admin_id, $hash, $expires_at); // bind admin ID, hashed token, and expiry time
            $ok = $stmtI->execute(); // execute insert query
            $stmtI->close(); // close statement

            if ($ok) { // if token successfully saved in database
                $base = rtrim(BASE_URL, "/"); // remove trailing slash from base URL to avoid double slashes
                $reset_link = $base . "/admin/reset_password.php?t=" . urlencode($token); // build full reset link with encoded token

                $email_sent = send_reset_email($admin_email, $reset_link); // send reset email to admin with reset link

                if ($email_sent) { // if email sent successfully
                    $msg = "Reset link sent to admin email successfully."; // success message
                    $msg_type = "success"; // set type
                } else { // if email sending failed
                    $msg = "Reset link created, but email failed to send."; // error message (token exists but email failed)
                    $msg_type = "error"; // type
                }

            } else { // if failed to insert reset token into database
                $msg = "Failed to generate reset link."; // error message
                $msg_type = "error"; // type
            } // End ok

        } // End found

    } // End validation

} // End POST

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ensure responsive layout -->
    <title>Forgot Password (Admin)</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS file for styling -->
    
    <!-- BACKGROUND STYLE (ADDED) -->
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
<div class="center"> <!-- wrapper to center content -->
    <div class="container"> <!-- main content container -->

        <h1>Admin Forgot Password</h1> <!-- page heading -->

        <?php if ($msg !== ""): ?> <!-- check if there is a message to display -->
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>"> <!-- message container with dynamic class -->
                <?php echo htmlspecialchars($msg); ?> <!-- safely display message (prevent XSS) -->
            </div>
        <?php endif; ?>

        <form method="POST"> <!-- form to submit username -->
            <label for="username">Admin username</label> <!-- label for username input -->
            <input id="username" name="username" type="text" class="input" required> <!-- input field for admin username -->
            <button type="submit">Generate Reset Link</button> <!-- submit button -->
        </form>

        <p style="margin-top:12px;"> <!-- spacing -->
            Back to <a href="login.php">Admin Login</a> <!-- link back to admin login page -->
        </p>

    </div>
</div>
</body>
</html>