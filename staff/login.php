<?php // Start PHP

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session
require_once __DIR__ . "/../includes/functions.php"; // include helper functions (login, redirect, etc.)

$msg = ""; // variable to store message (error or success)
$msg_type = ""; // variable to store message type (used for styling)

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check if form is submitted using POST method

    $username = trim($_POST["username"] ?? ""); // get username from form, remove extra spaces, default empty if not set
    $password = $_POST["password"] ?? ""; // get password from form, default empty if not set

    if ($username === "" || $password === "") { // check if username or password is empty
        $msg = "Please enter username and password."; // set error message
        $msg_type = "error"; // set message type as error
    } else { // if inputs are valid, continue login process

        $stmt = $conn->prepare("SELECT id, username, password_hash FROM staff WHERE username = ? LIMIT 1"); // prepare SQL query to find staff by username
        $stmt->bind_param("s", $username); // bind username parameter to prevent SQL injection
        $stmt->execute(); // execute query
        $result = $stmt->get_result(); // get query result

        if ($result->num_rows === 1) { // check if exactly one staff record found
            $row = $result->fetch_assoc(); // fetch the row as associative array

            if (password_verify($password, $row["password_hash"])) { // verify entered password with hashed password from database

                $_SESSION["staff_id"] = (int)$row["id"]; // store staff id in session (cast to integer)
                $_SESSION["staff_username"] = $row["username"]; // store username in session

                // Create staff log entry
                $login_time = date("Y-m-d H:i:s"); // get current date and time
                $stmt2 = $conn->prepare("INSERT INTO staff_logs (staff_id, login_time, last_seen) VALUES (?, ?, ?)"); // prepare insert query for staff log
                $stmt2->bind_param("iss", $_SESSION["staff_id"], $login_time, $login_time); // bind staff id and timestamps
                $stmt2->execute(); // execute insert query
                $_SESSION["staff_log_id"] = $stmt2->insert_id; // save inserted log id in session for tracking logout later
                $stmt2->close(); // close second statement

                redirect("dashboard.php"); // redirect to staff dashboard after successful login

            } else { // if password is incorrect
                $msg = "Wrong password."; // set error message
                $msg_type = "error"; // set message type
            }

        } else { // if no staff record found with that username
            $msg = "Staff account not found."; // set error message
            $msg_type = "error"; // set message type
        }

        $stmt->close(); // close main prepared statement
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design for mobile -->
    <title>Staff Login</title> <!-- page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS file -->
    
<!-- BACKGROUND STYLE (ADDED) -->
<style>
        body {
            margin: 0;
            padding: 0;
            background: url('../assets/Staff_Background.jpg') no-repeat center center/cover;
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
            0 15px 0 #000,
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
    </style>
    
</head>
<body>
        <div class="wrapper">
            
            <div class="title">
                Servpro Autospa Detailing by Persada Car Wash
                </div>

<div class="center"> <!-- wrapper to center content -->
    <div class="container"> <!-- main container -->

        <h1>Staff Login</h1> <!-- page heading -->

        <?php if ($msg !== ""): ?> <!-- check if message exists -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- message box with dynamic class -->
                <?php echo htmlspecialchars($msg); ?> <!-- display message safely (prevent XSS) -->
            </div>
        <?php endif; ?> <!-- end message check -->

        <form method="POST"> <!-- login form using POST method -->
            <label for="username">Username</label> <!-- label for username -->
            <input id="username" name="username" type="text" class="input" required
                   value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"> <!-- input field with previous value retained -->

            <label for="password">Password</label> <!-- label for password -->
            <input id="password" name="password" type="password" class="input" required> <!-- password input field -->

            <button class="primary" type="submit">Login</button> <!-- submit button -->
        </form>

    </div>
</div>

</body>
</html>