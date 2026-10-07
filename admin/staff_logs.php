<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session automatically
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as login checking and redirect

// If admin not logged in
if (!is_admin_logged_in()) { // check whether current session belongs to admin
    redirect("login.php"); // if not admin, redirect to login page
} // End check

// Get staff logs with username and snapshot username
$logs = $conn->query("SELECT 
                        sl.id,
                        sl.staff_id,
                        sl.staff_username_snapshot,
                        sl.login_time,
                        sl.logout_time,
                        sl.last_seen,
                        s.username
                      FROM staff_logs sl
                      LEFT JOIN staff s ON s.id = sl.staff_id
                      ORDER BY sl.id DESC"); // query latest staff logs first

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive layout -->
    <meta http-equiv="refresh" content="5"> <!-- auto refresh every 5 seconds -->
    <title>Staff Login / Logout Logs</title> <!-- browser tab title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link CSS -->
    
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

<div class="center"> <!-- center wrapper -->
    <div class="container wide"> <!-- wide container -->

        <h1>Staff Logs</h1> <!-- page heading -->

        <p class="small"> <!-- small info line -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["admin_username"] ?? "Admin"); ?></b> <!-- show admin username safely -->
            | <a href="dashboard.php">Back to Dashboard</a> <!-- dashboard link -->
            | <a href="logout.php">Logout</a> <!-- logout link -->
        </p>

        <div class="card"> <!-- explanation box -->
            <p class="small">
                Online rule: staff is ONLINE only if <b>logout time is empty</b> AND <b>last seen</b> is within <b>60 seconds</b>.
            </p>
        </div>

        <div class="table-wrap"> <!-- table wrapper -->
            <table>
                <tr>
                    <th>#</th> <!-- log id -->
                    <th>Staff</th> <!-- username -->
                    <th>Login Time</th> <!-- login time -->
                    <th>Logout Time</th> <!-- logout time -->
                    <th>Last Seen</th> <!-- last seen -->
                    <th>Status</th> <!-- online/offline -->
                </tr>

                <?php if ($logs && $logs->num_rows > 0): ?> <!-- check whether logs exist -->
                    <?php while ($r = $logs->fetch_assoc()): ?> <!-- loop each log row -->

                        <?php
                        $logout_time = $r["logout_time"]; // current row logout time
                        $last_seen = $r["last_seen"]; // current row last seen time
                        $is_online = false; // default status is offline

                        if ($logout_time) { // if logout time exists
                            $is_online = false; // definitely offline
                        } else { // if logout time is empty
                            if ($last_seen) { // if last seen exists
                                $last_seen_ts = strtotime($last_seen); // convert last seen to timestamp
                                $is_online = ($last_seen_ts >= (time() - 60)); // online only if active within last 60 seconds
                            }
                        }

                        $logout_display = "-"; // default display
                        if ($logout_time) { // if logout time exists
                            $logout_display = $logout_time; // show actual logout time
                        } else {
                            if ($last_seen && strtotime($last_seen) < (time() - 60)) { // if timed out
                                $logout_display = "AUTO: " . $last_seen; // show auto logout fallback
                            }
                        }
                        ?>

                        <tr>
                            <td><?php echo (int)$r["id"]; ?></td> <!-- show log id -->
                            <td><?php echo htmlspecialchars($r["username"] ?? $r["staff_username_snapshot"] ?? ("Staff ID " . (int)$r["staff_id"])); ?></td> <!-- show username, else snapshot, else staff id -->
                            <td><?php echo htmlspecialchars($r["login_time"] ?? "-"); ?></td> <!-- show login time -->
                            <td><?php echo htmlspecialchars($logout_display); ?></td> <!-- show logout display -->
                            <td><?php echo htmlspecialchars($last_seen ?? "-"); ?></td> <!-- show last seen -->
                            <td>
                                <?php if ($is_online): ?> <!-- if online -->
                                    <span class="badge" style="border-color: rgba(34, 197, 94, 0.35); background: rgba(34, 197, 94, 0.12); color: #bbf7d0;">
                                        ONLINE
                                    </span>
                                <?php else: ?> <!-- if offline -->
                                    <span class="badge">OFFLINE</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?> <!-- end loop -->
                <?php else: ?> <!-- if no logs -->
                    <tr>
                        <td colspan="6">No staff logs found.</td>
                    </tr>
                <?php endif; ?>

            </table>
        </div>

        <div class="outro-container">
            <p>This works only if staff pages update <b>last seen</b> on every visit.</p>
        </div>

    </div>
</div>

</body>
</html>