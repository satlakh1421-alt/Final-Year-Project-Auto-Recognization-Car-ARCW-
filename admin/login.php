<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions (redirect + login check)

// If admin is already logged in, go dashboard
if (isset($_SESSION["admin_id"])) { // Check if admin session exists
    redirect("dashboard.php"); // Redirect to admin dashboard
} // End session check

$msg = ""; // Store message text
$msg_type = ""; // Store message type (success/error)

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") { // Check POST request
    $username = trim($_POST["username"] ?? ""); // Read username safely
    $password = $_POST["password"] ?? ""; // Read password safely

    // Validate fields
    if ($username === "" || $password === "") { // If any field empty
        $msg = "Please enter username and password."; // Set error message
        $msg_type = "error"; // Set error type
    } else { // If fields filled
        // Find admin by username
        $stmt = $conn->prepare("SELECT id, password_hash FROM admin_users WHERE username = ?"); // Prepare query
        $stmt->bind_param("s", $username); // Bind username
        $stmt->execute(); // Run query
        $res = $stmt->get_result(); // Get results

        // If found one admin
        if ($res && $res->num_rows === 1) { // Check admin exists
            $row = $res->fetch_assoc(); // Fetch row
            $hash = $row["password_hash"]; // Get stored hash

            // Verify password
            if (password_verify($password, $hash)) { // If password correct
                $_SESSION["admin_id"] = (int)$row["id"]; // Save admin id in session
                $_SESSION["admin_username"] = $username; // Save admin username in session
                redirect("dashboard.php"); // Go dashboard
            } else { // Wrong password
                $msg = "Wrong password."; // Set message
                $msg_type = "error"; // Set type
            } // End password verify
        } else { // Admin not found
            $msg = "Admin account not found."; // Set message
            $msg_type = "error"; // Set type
        } // End admin found check
    } // End validation
} // End POST check

?>
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- Start HTML -->
<head> <!-- Head start -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Admin Login</title> <!-- Title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Use same CSS -->

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
        
        .title {
            text-align: center;
            color: #cdbdff;
            font-size: 50px;
            font-weight: bold;
            margin-bottom: 20px; /* THIS controls distance */
            text-shadow: 
            0 15px 0 #000,
            0 20px 10px rgba(0,0,0,0.6);
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
    </style>
    
</head> <!-- Head end -->
</head>
<body>
        <div class="wrapper">
            
            <div class="title">
                Servpro Autospa Detailing by Persada Car Wash
                </div>

<div class="center"> <!-- Center wrapper -->
    <div class="container"> <!-- Container -->
        <h1>Admin Login</h1> <!-- Heading -->

        <?php if ($msg !== ""): ?> <!-- If message exists -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- Message box -->
                <?php echo htmlspecialchars($msg); ?> <!-- Print message -->
            </div> <!-- End message box -->
        <?php endif; ?> <!-- End message check -->

        <form method="POST"> <!-- Login form -->
            <label for="username">Username</label> <!-- Username label -->
            <input id="username" name="username" type="text" class="input" required> <!-- Username input -->

            <label for="password">Password</label> <!-- Password label -->
            <input id="password" name="password" type="password" class="input" required> <!-- Password input -->

            <button type="submit">Login</button> <!-- Submit button -->
        </form> <!-- End form -->

        <p style="margin-top:12px;">
            <a href="forgot_password.php">Forgot Password?</a>
        </p>


        <div class="outro-container"> <!-- Footer -->
            <p>Boss / Admin access only.</p> <!-- Note -->
        </div> <!-- End footer -->
    </div> <!-- End container -->
</div> <!-- End center -->

</body> <!-- End body -->
</html> <!-- End html -->