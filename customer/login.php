<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

$msg = "";
$msg_type = "error";
$email = trim($_POST["email"] ?? "");

// check if required columns exist
function customer_column_exists($conn, $column) {
    $safe = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM customers LIKE '{$safe}'");
    return ($res && $res->num_rows > 0);
}

$has_last_login = customer_column_exists($conn, "last_login");
$has_is_active  = customer_column_exists($conn, "is_active");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $msg = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("
            SELECT id, name, email, password_hash" .
            ($has_last_login ? ", last_login" : "") .
            ($has_is_active ? ", is_active" : "") . "
            FROM customers
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $msg = "Account not found. Please register.";
        } else {
            // auto-disable if last_login older than 1 year
            if ($has_last_login && $has_is_active && !empty($user["last_login"])) {
                $last_login_ts = strtotime($user["last_login"]);
                $one_year_ago = time() - (365 * 24 * 60 * 60);

                if ($last_login_ts !== false && $last_login_ts < $one_year_ago && (int)$user["is_active"] === 1) {
                    $stmtD = $conn->prepare("UPDATE customers SET is_active = 0 WHERE id = ?");
                    $stmtD->bind_param("i", $user["id"]);
                    $stmtD->execute();
                    $stmtD->close();
                    $user["is_active"] = 0;
                }
            }

            $password_ok = password_verify($password, $user["password_hash"]);

            // 🔥 correct logic
            if ($has_is_active && isset($user["is_active"]) && (int)$user["is_active"] === 0) {
            
                if (!$password_ok) {
                    $msg = "Your account has been deactivated due to 1 year+ inactivity. Enter the correct password to reactivate it.";
                } else {
                    // do nothing here → will continue to login (reactivate below)
                }
            
            } elseif (!$password_ok) {
            
                $msg = "Wrong password.";
            
            } else {
                $now = date("Y-m-d H:i:s");

                if ($has_is_active && isset($user["is_active"]) && (int)$user["is_active"] === 0) {
                    if ($has_last_login) {
                        $stmtR = $conn->prepare("UPDATE customers SET is_active = 1, last_login = ? WHERE id = ?");
                        $stmtR->bind_param("si", $now, $user["id"]);
                    } else {
                        $stmtR = $conn->prepare("UPDATE customers SET is_active = 1 WHERE id = ?");
                        $stmtR->bind_param("i", $user["id"]);
                    }
                    $stmtR->execute();
                    $stmtR->close();
                } else {
                    if ($has_last_login) {
                        $stmtU = $conn->prepare("UPDATE customers SET last_login = ? WHERE id = ?");
                        $stmtU->bind_param("si", $now, $user["id"]);
                        $stmtU->execute();
                        $stmtU->close();
                    }
                }

                $_SESSION["customer_id"] = (int)$user["id"];
                $_SESSION["customer_name"] = $user["name"];
                $_SESSION["customer_email"] = $user["email"];
                

                // if customer came from QR scan, return to that QR flow first
                if (!empty($_SESSION["redirect_after_login"])) {
                    $go = $_SESSION["redirect_after_login"];
                    unset($_SESSION["redirect_after_login"]);
                    redirect($go);
                }
                if (!empty($_SESSION["qr_token"]) && !empty($_SESSION["capture_id"]) && !empty($_SESSION["plate_no"])) {
                    redirect("start.php?t=" . urlencode($_SESSION["qr_token"]));
                }
                
                // normal login from link -> check latest order
                $stmt2 = $conn->prepare("
                    SELECT id, status
                    FROM orders
                    WHERE customer_id = ?
                    ORDER BY id DESC
                    LIMIT 1
                ");
                $stmt2->bind_param("i", $user["id"]);
                $stmt2->execute();
                $latest = $stmt2->get_result()->fetch_assoc();
                $stmt2->close();
                
                if ($latest) {
                    $_SESSION["order_id"] = (int)$latest["id"];
                    $st = strtoupper(trim($latest["status"] ?? ""));
                
                    if ($st === "PENDING") {
                        redirect("payment.php");
                    } elseif ($st === "PAID" || $st === "WASHING") {
                        redirect("status.php");
                    } elseif ($st === "DONE") {
                        unset($_SESSION["order_id"]);
                        redirect("dashboard.php");
                    } else {
                        unset($_SESSION["order_id"]);
                        redirect("dashboard.php");
                    }
                } else {
                    unset($_SESSION["order_id"]);
                    redirect("dashboard.php");
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
    <title>Customer Login</title>
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
        <div class="wrapper">
            
            <div class="title">
                Servpro Autospa Detailing by Persada Car Wash
                </div>
                
    
        <div class="center">
        <div class="container">

        <h1>Customer Login</h1>

        <?php if ($msg !== ""): ?>
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="input" required
                   value="<?php echo htmlspecialchars($email); ?>">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" class="input" required>

            <button class="primary" type="submit">Login</button>
        </form>

        <p style="margin-top:12px;">
            No account? <a href="register.php">Register here</a> |
            <a href="forgot_password.php">Forgot Password?</a>
        </p>

    </div>
</div>
</body>
</html>