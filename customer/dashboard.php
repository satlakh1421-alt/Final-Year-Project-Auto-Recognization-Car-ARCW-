<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

if (!is_customer_logged_in()) {
    redirect("login.php");
}

$dashboard_msg = $_SESSION["dashboard_msg"] ?? "You have to scan a new QR code to make an order.";
unset($_SESSION["dashboard_msg"]);

// clear QR flow when dashboard is opened directly
unset($_SESSION["qr_token"], $_SESSION["capture_id"], $_SESSION["plate_no"], $_SESSION["order_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
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

        <h1>Customer Dashboard</h1>

        <p>You are logged in as: <?php echo htmlspecialchars($_SESSION["customer_email"] ?? ""); ?></p>

        <p style="margin-top:12px;">
            <?php echo htmlspecialchars($dashboard_msg); ?>
        </p>

        <p style="margin-top:16px;">
            <a href="logout.php">Logout</a>
        </p>

    </div>
</div>
</body>
</html>