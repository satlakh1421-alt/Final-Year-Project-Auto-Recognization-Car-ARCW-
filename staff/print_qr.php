<?php

require_once __DIR__ . "/../includes/db.php"; // include database connection and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (login check, redirect, etc.)

if (!is_staff_logged_in()) { // check if staff is logged in
    redirect("login.php"); // if not logged in, redirect to login page
}

$id = (int)($_GET["id"] ?? 0); // get capture ID from URL (GET), convert to integer, default 0

if ($id <= 0) { // check if ID is invalid (0 or negative)
    die("Invalid capture ID."); // stop execution and show error
}

$stmt = $conn->prepare("SELECT id, plate_no, token, captured_at, status FROM captures WHERE id = ? LIMIT 1"); // prepare SQL query to fetch capture data
$stmt->bind_param("i", $id); // bind ID parameter (integer)
$stmt->execute(); // execute query
$row = $stmt->get_result()->fetch_assoc(); // fetch result as associative array
$stmt->close(); // close statement

if (!$row) { // check if no record found
    die("Capture not found."); // stop execution and show error
}

$qr_url = BASE_URL . "/customer/start.php?t=" . urlencode($row["token"]); // generate customer QR URL with token
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- make page responsive -->
    <title>Print QR</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script> <!-- load QR code generator library -->

    <style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;

    /* better background control */
    background-image: url('../assets/Staff_Background_Money.jpg?v=2');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

/* DARK OVERLAY (smoothed for better look) */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    
    background: rgba(0,0,0,0.35); /* lighter than before = nicer look */

    z-index: 0;
}

/* ensures ALL content stays above overlay */
body * {
    position: relative;
    z-index: 1;
}

/* GLASS CONTAINER (stronger + cleaner) */
.container {
    background: rgba(255, 255, 255, 0.10);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.18);

    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.45);

    padding: 20px;
}
        
        .qr-print-wrap { /* wrapper for QR display */
            display: flex; /* use flexbox layout */
            justify-content: center; /* center horizontally */
            margin: 20px 0; /* spacing top and bottom */
        }

        .qr-print-box { /* box around QR */
            display: inline-block; /* inline block for sizing */
            padding: 16px; /* inner spacing */
            background: #ffffff; /* white background */
            border-radius: 14px; /* rounded corners */
        }

        #qrcode { /* QR container */
            width: 260px; /* fixed width */
            min-height: 260px; /* minimum height */
        }

        #qrcode canvas,
        #qrcode img { /* QR image or canvas */
            display: block; /* block element */
            margin: 0 auto; /* center horizontally */
        }

        @media print { /* styling for print mode */
            body {
                background: #ffffff !important; /* force white background */
                color: #000000 !important; /* force black text */
                padding: 0 !important; /* remove padding */
            }

            .center {
                min-height: auto !important; /* remove min height */
                padding: 0 !important; /* remove padding */
                display: block !important; /* display block */
            }

            .container {
                background: #ffffff !important; /* white background */
                color: #000000 !important; /* black text */
                box-shadow: none !important; /* remove shadow */
                border: none !important; /* remove border */
                backdrop-filter: none !important; /* remove blur */
                -webkit-backdrop-filter: none !important; /* remove blur for safari */
                max-width: 100% !important; /* full width */
                border-radius: 0 !important; /* remove rounded corners */
                padding: 20px !important; /* padding for print */
            }

            h1, p, b, div, span {
                color: #000000 !important; /* force all text black */
            }

            .small {
                color: #000000 !important; /* ensure small text visible */
            }

            .qr-print-wrap {
                justify-content: flex-start !important; /* align left when printing */
            }

            .qr-print-box {
                background: #ffffff !important; /* white background */
                border: 2px solid #000000 !important; /* black border */
                border-radius: 0 !important; /* remove rounded corners */
                padding: 14px !important; /* spacing */
                box-shadow: none !important; /* remove shadow */
            }

            #qrcode,
            #qrcode canvas,
            #qrcode img {
                background: #ffffff !important; /* ensure QR background white */
            }

            button {
                display: none !important; /* hide buttons when printing */
            }

            a {
                color: #000000 !important; /* black link text */
                text-decoration: none !important; /* remove underline */
            }
        }
    </style>
</head>
<body>

<div class="center"> <!-- center wrapper -->
    <div class="container"> <!-- main container -->

        <h1>Print QR Code</h1> <!-- page title -->

        <p><b>Capture ID:</b> <?php echo (int)$row["id"]; ?></p> <!-- display capture ID -->
        <p><b>Plate No:</b> <?php echo htmlspecialchars($row["plate_no"]); ?></p> <!-- display plate number safely -->
        <p><b>Status:</b> <?php echo htmlspecialchars($row["status"]); ?></p> <!-- display status -->
        <p><b>Captured At:</b> <?php echo htmlspecialchars($row["captured_at"]); ?></p> <!-- display capture time -->

        <div class="qr-print-wrap"> <!-- QR wrapper -->
            <div class="qr-print-box"> <!-- QR box -->
                <div id="qrcode"></div> <!-- container where QR code will be generated -->
            </div>
        </div>

        <p class="small"> <!-- small text -->
            Customer QR URL:<br> <!-- label -->
            <?php echo htmlspecialchars($qr_url); ?> <!-- display QR link -->
        </p>

        <button onclick="window.print()">Print QR</button> <!-- button to trigger print -->
        
        <p style="margin-top:12px;"> <!-- spacing -->
            <a href="dashboard.php">Back to Dashboard</a> <!-- link back -->
        </p>

    </div>
</div>

<script>
new QRCode(document.getElementById("qrcode"), { // create new QR code inside element with id "qrcode"
    text: "<?php echo $qr_url; ?>", // content encoded in QR (customer link)
    width: 260, // QR width
    height: 260 // QR height
});
</script>

</body>
</html>