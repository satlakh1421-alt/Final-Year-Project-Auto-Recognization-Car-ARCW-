<?php

require_once __DIR__ . "/../includes/db.php"; // include database connection and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (token generation, hashing, etc.)
require_once __DIR__ . "/../includes/mailer.php"; // include mailer function to send emails

$msg = ""; // variable to store message text (success/error)
$msg_type = ""; // variable to store message type for styling

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check if form is submitted via POST

    $email = trim($_POST["email"] ?? ""); // get email input, remove spaces, default empty if not set

    if ($email === "") { // check if email is empty
        $msg = "Please enter your email."; // set error message
        $msg_type = "error"; // set message type
    } else {

        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?"); // prepare query to find customer by email
        $stmt->bind_param("s", $email); // bind email parameter safely
        $stmt->execute(); // execute query
        $row = $stmt->get_result()->fetch_assoc(); // fetch result
        $stmt->close(); // close statement

        if (!$row) { // if no account found with that email
            $msg = "Account not found. Please register."; // set error message
            $msg_type = "error"; // set type
        } else {

            $customer_id = (int)$row["id"]; // get customer ID and convert to integer
            $token = generate_token(40); // generate random token (length 40)
            $hash = token_hash($token); // hash the token for secure storage in database
            $expires_at = date("Y-m-d H:i:s", time() + 3600); // set expiry time (1 hour from now)

            $stmtI = $conn->prepare("INSERT INTO password_resets (user_type, user_id, token_hash, expires_at) VALUES ('customer', ?, ?, ?)"); // prepare insert query for reset token
            $stmtI->bind_param("iss", $customer_id, $hash, $expires_at); // bind values
            $ok = $stmtI->execute(); // execute insert
            $stmtI->close(); // close statement

            if ($ok) { // if token successfully saved

                $base = rtrim(BASE_URL, "/"); // remove trailing slash from base URL
                $reset_link = $base . "/customer/reset_password.php?t=" . urlencode($token); // build password reset link with token

                $email_sent = send_reset_email($email, $reset_link); // send reset email with link

                if ($email_sent) { // if email sent successfully
                    $msg = "Password reset link sent to your email."; // success message
                    $msg_type = "success"; // type success
                } else { // if email failed
                    $msg = "Email failed to send. Please try again."; // error message
                    $msg_type = "error"; // type error
                }

            } else { // if failed to insert token
                $msg = "Failed to generate reset link. Please try again."; // error message
                $msg_type = "error"; // type error
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design -->
    <title>Forgot Password (Customer)</title> <!-- page title -->
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
        
        /* =========================
   MOBILE RESPONSIVE FIX
   ========================= */
@media (max-width: 768px) {

    .otp-container {
        width: 92%;
        max-width: 300px;
        padding: 22px;
    }

    .otp-container h1 {
        font-size: 22px;
    }

    .otp-container p {
        font-size: 13px;
    }

    input[type="text"] {
        font-size: 14px;
        padding: 10px;
        letter-spacing: 4px;
    }

    button[name="verify"] {
        font-size: 15px;
        padding: 10px;
    }

    .resend {
        font-size: 13px;
    }
}
    </style>

    
</head>
<body>
<div class="center"> <!-- center wrapper -->
    <div class="container"> <!-- main container -->

        <h1>Forgot Password</h1> <!-- page heading -->

        <?php if ($msg !== ""): ?> <!-- check if message exists -->
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>"> <!-- message box with dynamic class -->
                <?php echo htmlspecialchars($msg); ?> <!-- display message safely -->
            </div>
        <?php endif; ?> <!-- end condition -->

        <form method="POST"> <!-- form to submit email -->
            <label for="email">Your Email</label> <!-- label -->
            <input id="email" name="email" type="email" class="input" required> <!-- email input field -->
            <button type="submit">Generate Reset Link</button> <!-- submit button -->
        </form>

        <p style="margin-top:12px;"> <!-- spacing -->
            Back to <a href="login.php">Login</a> <!-- link back to login page -->
        </p>

    </div>
</div>
</body>
</html>