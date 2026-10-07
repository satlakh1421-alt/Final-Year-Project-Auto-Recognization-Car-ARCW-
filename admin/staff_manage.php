<?php // Start PHP tag

error_reporting(E_ALL); // show all PHP errors for debugging
ini_set('display_errors', 1); // force PHP errors to display in browser

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session automatically
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as login check, password validation, and redirect

if (!is_admin_logged_in()) { // check whether current user is logged in as admin
    redirect("login.php"); // if not admin, redirect to admin login page
} // end admin login check

$msg = ""; // variable to store success or error message text
$msg_type = ""; // variable to store message type for CSS styling (success or error)

$username = ""; // variable to keep username input value when create form fails

// --------- HANDLE CREATE STAFF ---------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "create") { // run this block only if create form is submitted

    $username = trim($_POST["username"] ?? ""); // read username from form and remove extra spaces
    $password = $_POST["password"] ?? ""; // read password from form

    if ($username === "" || $password === "") { // validate that both username and password are filled
        $msg = "Please enter username and password."; // set error message
        $msg_type = "error"; // set message type as error
    } else { // continue if inputs are not empty

        $pw_error = ""; // variable to receive password validation error message
        if (!is_strong_password($password, $pw_error)) { // check whether password is strong enough
            $msg = $pw_error; // show password rule error
            $msg_type = "error"; // set message type as error
        } else { // continue if password is strong

            $username = strtolower($username); // convert username to lowercase to avoid duplicates like Staff1 and staff1

            $stmtC = $conn->prepare("SELECT id FROM staff WHERE username = ?"); // prepare query to check whether username already exists
            if (!$stmtC) { // check if prepare failed
                die("Prepare failed (check staff exists): " . $conn->error); // show database error
            }

            $stmtC->bind_param("s", $username); // bind username value safely
            $stmtC->execute(); // run query
            $exists = $stmtC->get_result()->fetch_assoc(); // fetch one matching row if exists
            $stmtC->close(); // close statement

            if ($exists) { // if username already exists in database
                $msg = "Username already exists. Choose another one."; // set error message
                $msg_type = "error"; // set type
            } else { // if username is new

                $hash = password_hash($password, PASSWORD_DEFAULT); // securely hash password before saving

                $stmtI = $conn->prepare("INSERT INTO staff (username, password_hash) VALUES (?, ?)"); // prepare insert query for new staff
                if (!$stmtI) { // check if prepare failed
                    die("Prepare failed (insert staff): " . $conn->error); // show database error
                }

                $stmtI->bind_param("ss", $username, $hash); // bind username and password hash
                $ok = $stmtI->execute(); // execute insert query
                $stmtI->close(); // close statement

                if ($ok) { // if insert successful
                    $msg = "Staff account created successfully."; // success message
                    $msg_type = "success"; // set type as success
                    $username = ""; // clear username field after successful creation
                } else { // if insert failed
                    $msg = "Failed to create staff. Please try again."; // error message
                    $msg_type = "error"; // set type as error
                } // end insert result check

            } // end username exists check

        } // end strong password check

    } // end empty validation

} // end create handler


// --------- HANDLE RESET PASSWORD ---------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "reset") { // run this block only if reset form is submitted

    $staff_id = (int)($_POST["staff_id"] ?? 0); // read selected staff ID safely as integer
    $new_password = $_POST["new_password"] ?? ""; // read new password from form

    if ($staff_id <= 0 || $new_password === "") { // validate that a staff is selected and password is entered
        $msg = "Please choose a staff and enter a new password."; // set error message
        $msg_type = "error"; // set type
    } else { // continue if inputs are valid

        $pw_error = ""; // variable to receive password validation error
        if (!is_strong_password($new_password, $pw_error)) { // validate strong password
            $msg = $pw_error; // show password rule message
            $msg_type = "error"; // set type
        } else { // continue if password is strong

            $hash = password_hash($new_password, PASSWORD_DEFAULT); // hash new password before saving

            $stmtU = $conn->prepare("UPDATE staff SET password_hash = ? WHERE id = ?"); // prepare update query for staff password
            if (!$stmtU) { // check if prepare failed
                die("Prepare failed (update password): " . $conn->error); // show database error
            }

            $stmtU->bind_param("si", $hash, $staff_id); // bind password hash and staff ID
            $ok = $stmtU->execute(); // execute update query
            $stmtU->close(); // close statement

            if ($ok) { // if password update successful
                $msg = "Password updated successfully."; // success message
                $msg_type = "success"; // set type
            } else { // if update failed
                $msg = "Failed to update password. Please try again."; // error message
                $msg_type = "error"; // set type
            } // end update result check

        } // end strong password check

    } // end reset validation

} // end reset handler


// --------- HANDLE DELETE STAFF (KEEP LOGS) ---------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete") { // run this block only if delete form is submitted

    $delete_staff_id = (int)($_POST["delete_staff_id"] ?? 0); // read selected staff ID safely as integer

    if ($delete_staff_id <= 0) { // validate selected ID
        $msg = "Invalid staff selected for deletion."; // error message
        $msg_type = "error"; // type
    } else { // continue if ID is valid

        $stmtS = $conn->prepare("SELECT username FROM staff WHERE id = ?"); // prepare query to get username before deleting staff
        if (!$stmtS) { // check if prepare failed
            die("Prepare failed (select staff before delete): " . $conn->error); // show database error
        }

        $stmtS->bind_param("i", $delete_staff_id); // bind staff ID
        $stmtS->execute(); // run query
        $staff_row = $stmtS->get_result()->fetch_assoc(); // fetch staff row
        $stmtS->close(); // close statement

        if (!$staff_row) { // if staff does not exist
            $msg = "Staff not found (maybe already deleted)."; // error message
            $msg_type = "error"; // type
        } else { // if staff exists

            $staff_username = $staff_row["username"]; // store username before deleting staff row

            $conn->begin_transaction(); // start transaction so all delete-related operations succeed together

            try { // begin try block

                $stmtL = $conn->prepare("UPDATE staff_logs SET staff_username_snapshot = ?, staff_id = NULL WHERE staff_id = ?"); // prepare query to keep old logs by saving username snapshot and removing foreign key
                if (!$stmtL) { // check if prepare failed
                    throw new Exception("Prepare failed (update logs): " . $conn->error); // throw exception to rollback transaction
                }

                $stmtL->bind_param("si", $staff_username, $delete_staff_id); // bind snapshot username and staff ID
                $stmtL->execute(); // execute log update
                $stmtL->close(); // close statement

                $stmtD = $conn->prepare("DELETE FROM staff WHERE id = ?"); // prepare query to delete staff record
                if (!$stmtD) { // check if prepare failed
                    throw new Exception("Prepare failed (delete staff): " . $conn->error); // throw exception
                }

                $stmtD->bind_param("i", $delete_staff_id); // bind staff ID
                $stmtD->execute(); // execute delete
                $stmtD->close(); // close statement

                $conn->commit(); // save all transaction changes permanently

                $msg = "Staff deleted successfully. Old logs are kept using snapshot username."; // success message
                $msg_type = "success"; // type

            } catch (Exception $e) { // catch any exception during transaction

                $conn->rollback(); // undo all transaction changes if something fails

                $msg = "Delete failed. Reason: " . $e->getMessage(); // show detailed error message
                $msg_type = "error"; // type

            } // end try/catch

        } // end staff found check

    } // end delete ID validation

} // end delete handler


$staff_list = $conn->query("SELECT id, username FROM staff ORDER BY id DESC"); // load staff list for dropdowns and display table
if (!$staff_list) { // check if query failed
    die("Query failed (staff list): " . $conn->error); // show database error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define UTF-8 character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- make page responsive on all screen sizes -->
    <title>Manage Staff</title> <!-- browser tab title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS file -->
    
    <style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;

    /* better background control */
    background-image: url('../assets/Admin_Work_Page.jpg?v=2');
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
    
    background: rgba(0,0,0,0.75); /* lighter than before = nicer look */

    z-index: 0;
}

/* ensures ALL content stays above overlay */
body * {
    position: relative;
    z-index: 1;
}

</style>
    
</head>
<body>

<div class="center"> <!-- center wrapper for page -->
    <div class="container wide"> <!-- wide container for all manage staff content -->

        <h1>Manage Staff</h1> <!-- main page heading -->

        <p class="small"> <!-- small info text -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?></b> <!-- safely display admin username -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- link back to dashboard -->
            | <a href="logout.php">Logout</a> <!-- logout link -->
        </p>

        <?php if ($msg !== ""): ?> <!-- show message box only if there is a message -->
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>"> <!-- message container with dynamic CSS class -->
                <?php echo htmlspecialchars($msg); ?> <!-- safely display message text -->
            </div>
        <?php endif; ?>

        <div class="card"> <!-- section for creating staff -->
            <h2>1) Add New Staff</h2> <!-- section title -->

            <form method="POST"> <!-- create staff form -->
                <input type="hidden" name="action" value="create"> <!-- hidden field tells PHP this is create action -->

                <label for="username">Username</label> <!-- username label -->
                <input id="username" name="username" type="text" class="input" value="<?php echo htmlspecialchars($username); ?>" required> <!-- username input -->

                <label for="password">Password</label> <!-- password label -->
                <input id="password" name="password" type="password" class="input" required> <!-- password input -->

                <p class="small" style="margin-top:8px;"> <!-- helper text -->
                    Password must be at least 8 characters and include a letter, number, and special character (example: !@#).
                </p>

                <button type="submit">Create Staff</button> <!-- submit button -->
            </form>
        </div>

        <div class="card"> <!-- section for resetting staff password -->
            <h2>2) Reset Staff Password</h2> <!-- section title -->

            <form method="POST"> <!-- reset password form -->
                <input type="hidden" name="action" value="reset"> <!-- hidden field tells PHP this is reset action -->

                <label for="staff_id">Select Staff</label> <!-- dropdown label -->
                <select id="staff_id" name="staff_id" class="select" required> <!-- dropdown list of staff -->
                    <option value="">-- Select Staff --</option> <!-- default option -->

                    <?php if ($staff_list && $staff_list->num_rows > 0): ?> <!-- check whether staff list has records -->
                        <?php $staff_list->data_seek(0); ?> <!-- move result pointer back to first row -->
                        <?php while ($s = $staff_list->fetch_assoc()): ?> <!-- loop through staff records -->
                            <option value="<?php echo (int)$s["id"]; ?>"> <!-- option value is staff ID -->
                                <?php echo htmlspecialchars($s["username"]); ?> <!-- show username safely -->
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>

                <label for="new_password">New Password</label> <!-- new password label -->
                <input id="new_password" name="new_password" type="password" class="input" required> <!-- new password input -->

                <p class="small" style="margin-top:8px;"> <!-- helper text -->
                    Password must be at least 8 characters and include a letter, number, and special character (example: !@#).
                </p>

                <button type="submit">Update Password</button> <!-- submit button -->
            </form>
        </div>

        <div class="card"> <!-- section for deleting staff -->
            <h2>3) Delete Staff (Keep Logs)</h2> <!-- section title -->

            <form method="POST" onsubmit="return confirm('Are you sure? This will delete the staff account but keep logs.');"> <!-- delete form with confirmation popup -->
                <input type="hidden" name="action" value="delete"> <!-- hidden field tells PHP this is delete action -->

                <label for="delete_staff_id">Select Staff to Delete</label> <!-- label for delete dropdown -->
                <select id="delete_staff_id" name="delete_staff_id" class="select" required> <!-- dropdown to choose staff -->
                    <option value="">-- Select Staff --</option> <!-- default option -->

                    <?php $staff_list_del = $conn->query("SELECT id, username FROM staff ORDER BY id DESC"); ?> <!-- separate query for delete dropdown -->
                    <?php if ($staff_list_del && $staff_list_del->num_rows > 0): ?> <!-- check whether dropdown has data -->
                        <?php while ($s = $staff_list_del->fetch_assoc()): ?> <!-- loop through staff rows -->
                            <option value="<?php echo (int)$s["id"]; ?>"> <!-- option value is staff ID -->
                                <?php echo htmlspecialchars($s["username"]); ?> <!-- display username safely -->
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>

                <button type="submit">Delete Staff</button> <!-- delete submit button -->
            </form>

            <p class="small" style="margin-top:10px;"> <!-- explanation -->
                Deleting staff will keep staff logs by saving the username into <b>staff_username_snapshot</b> and removing the old staff ID link.
            </p>
        </div>

        <div class="card"> <!-- section for existing staff list -->
            <h2>4) Existing Staff List</h2> <!-- section title -->

            <?php $staff_list2 = $conn->query("SELECT id, username FROM staff ORDER BY id DESC"); ?> <!-- query all staff again for display table -->

            <div class="table-wrap"> <!-- table wrapper -->
                <table> <!-- start table -->
                    <tr> <!-- header row -->
                        <th>No</th> <!-- running number -->
                        <th>ID</th> <!-- staff ID -->
                        <th>Username</th> <!-- staff username -->
                    </tr>

                    <?php if ($staff_list2 && $staff_list2->num_rows > 0): ?> <!-- check if table has data -->
                        <?php $no = 1; ?> <!-- initialize running number -->
                        <?php while ($s = $staff_list2->fetch_assoc()): ?> <!-- loop through staff rows -->
                            <tr> <!-- data row -->
                                <td><?php echo (int)$no; ?></td> <!-- display running number -->
                                <td><?php echo (int)$s["id"]; ?></td> <!-- display staff ID -->
                                <td><?php echo htmlspecialchars($s["username"]); ?></td> <!-- display username safely -->
                            </tr>
                            <?php $no++; ?> <!-- increment running number -->
                        <?php endwhile; ?>
                    <?php else: ?> <!-- if no staff found -->
                        <tr>
                            <td colspan="3">No staff found.</td> <!-- show empty message -->
                        </tr>
                    <?php endif; ?>
                </table> <!-- end table -->
            </div>
        </div>

    </div>
</div>

</body>
</html>