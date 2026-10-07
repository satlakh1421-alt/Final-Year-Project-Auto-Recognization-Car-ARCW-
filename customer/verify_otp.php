<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../includes/db.php";
require_once "../includes/mailer.php";
require_once "../includes/functions.php";

$msg = "";
$msg_type = "error";

if (!isset($_SESSION["otp"]) || !isset($_SESSION["temp_user"])) {
    die("Session expired. Please register again.");
}

function otp_customer_column_exists($conn, $column) {
    $safe = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM customers LIKE '{$safe}'");
    return ($res && $res->num_rows > 0);
}

$has_phone = otp_customer_column_exists($conn, "phone");
$has_is_active = otp_customer_column_exists($conn, "is_active");
$has_last_login = otp_customer_column_exists($conn, "last_login");

if (isset($_POST["verify"])) {
    $entered_otp = trim($_POST["otp"] ?? "");

    if ($entered_otp === "") {
        $msg = "Please enter the OTP code.";
    } elseif (time() > ($_SESSION["otp_expiry"] ?? 0)) {
        $msg = "OTP has expired. Please resend a new code.";
    } elseif ($entered_otp != $_SESSION["otp"]) {
        $msg = "Invalid OTP. Please try again.";
    } else {
        $temp_user = $_SESSION["temp_user"];

        $name = $temp_user["name"];
        $email = $temp_user["email"];
        $phone = $temp_user["phone"];
        $password_hash = $temp_user["password_hash"];

        if ($has_phone && $has_is_active && $has_last_login) {
            $stmt = $conn->prepare("
                INSERT INTO customers (name, email, phone, password_hash, is_active, last_login)
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->bind_param("ssss", $name, $email, $phone, $password_hash);
        } elseif ($has_phone && $has_is_active) {
            $stmt = $conn->prepare("
                INSERT INTO customers (name, email, phone, password_hash, is_active)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->bind_param("ssss", $name, $email, $phone, $password_hash);
        } elseif ($has_phone) {
            $stmt = $conn->prepare("
                INSERT INTO customers (name, email, phone, password_hash)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("ssss", $name, $email, $phone, $password_hash);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO customers (name, email, password_hash)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $name, $email, $password_hash);
        }

        if ($stmt->execute()) {
            $stmt->close();

            send_welcome_email($email, $name);

            unset($_SESSION["otp"]);
            unset($_SESSION["otp_expiry"]);
            unset($_SESSION["temp_user"]);

            // user must login after OTP, as you requested
            redirect("login.php");
        } else {
            $msg = "Failed to complete registration. Please try again.";
        }
    }
}

if (isset($_POST["resend"])) {
    $new_otp = rand(100000, 999999);
    $_SESSION["otp"] = $new_otp;
    $_SESSION["otp_expiry"] = time() + 300;

    $email = $_SESSION["temp_user"]["email"];
    $sent = send_otp_email($email, $new_otp);

    if ($sent) {
        $msg = "A new OTP has been sent to your email.";
        $msg_type = "success";
    } else {
        $msg = "Failed to resend OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="../assets/style.css">
    
    <style>
body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: url('../assets/customer_background.jpg') no-repeat center/cover;
    font-family: Arial, sans-serif;
}

/* dark overlay */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: -1;
}

/* center layout */
.wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* OTP BOX */
.otp-box {
    width: 100%;
    max-width: 380px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 25px;
    text-align: center;
    color: white;
}

/* title */
.otp-box h1 {
    margin-top: 0;
}

/* OTP INPUT */
.otp-input {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    border: none;
    text-align: center;
    font-size: 24px;
    letter-spacing: 8px; /* THIS FIXES PROFESSIONAL LOOK */
    margin-top: 15px;
}

/* BUTTON */
.otp-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 12px;
    margin-top: 15px;
    background: linear-gradient(90deg, #9fd0ff, #d7a8ff);
    font-weight: bold;
    cursor: pointer;
}

/* resend */
.resend-btn {
    background: none;
    border: none;
    color: #d7b8ff;
    cursor: pointer;
    margin-top: 10px;
}

/* message */
.msg {
    margin-top: 10px;
}
</style>
    
</head>
<body>

<div class="wrapper">

    <div class="otp-box">

        <h1>OTP Verification</h1>
        <p>Enter the 6-digit code sent to your email</p>

        <?php if ($msg != ""): ?>
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="otp" class="otp-input" placeholder="000000" maxlength="6" required autocomplete="off">
            <button type="submit" name="verify" class="otp-btn">Verify OTP</button>
        </form>

        <form method="POST">
            <p>Didn't receive a code?</p>
            <button type="submit" name="resend" class="resend-btn">Resend OTP</button>
        </form>

    </div>

</div>

</body>
</html>