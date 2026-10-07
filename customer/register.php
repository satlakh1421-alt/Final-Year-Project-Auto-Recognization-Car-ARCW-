<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";
require_once __DIR__ . "/../includes/mailer.php";

$msg = "";
$msg_type = "error";

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $phone === "" || $password === "") {
        $msg = "Please fill in all fields.";
    } else {
        $pw_error = "";
        if (!is_strong_password($password, $pw_error)) {
            $msg = $pw_error;
        } else {
            $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->fetch_assoc();
            $stmt->close();

            if ($exists) {
                $msg = "Email already registered. Please login.";
            } else {
                $otp = rand(100000, 999999);

                $_SESSION["otp"] = $otp;
                $_SESSION["otp_expiry"] = time() + 300;
                $_SESSION["temp_user"] = [
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "password_hash" => password_hash($password, PASSWORD_DEFAULT)
                ];

                $sent = send_otp_email($email, $otp);

                if ($sent) {
                    redirect("verify_otp.php");
                } else {
                    $msg = "Failed to send OTP email. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register</title>
    <link rel="stylesheet" href="../assets/style.css">
    
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
        
        .title {
            text-align: center;
            color: #cdbdff;
            font-size: 50px;
            font-weight: bold;
            margin-bottom: -100px; /* THIS controls distance */
            text-shadow: 
            0 10px 0 #000,
            0 50px 10px rgba(0,0,0,0.6);
            transform: translateY( 30px);
        }
        
        ..wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* center everything */
            align-items: center;
            gap: 15px; /* controls space between title & box */
        }
        
        @media (max-width: 768px) {
        body {
        background-size: cover; /* CHANGE THIS */
        background-position: center;
    }
}
        
    </style>
    
</head>
<body>
<div class="center">
    <div class="container">

        <h1>Customer Register</h1>

        <?php if ($msg !== ""): ?>
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" class="input" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="input" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="phone">Phone Number</label>
            <input id="phone" name="phone" type="text" class="input" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Example: 01159774982" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="input" required>

            <p class="small" style="margin-top:8px;">
                Password must be at least 8 characters and include a letter, number, and special character (example: !@#).
            </p>

            <button class="primary" type="submit">Register</button>
        </form>

        <p style="margin-top:12px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>

    </div>
</div>
</body>
</html>