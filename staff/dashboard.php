<?php // Start PHP tag

require_once __DIR__ . "/../includes/db.php"; // Load DB connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions

if (!is_staff_logged_in()) { // If staff is NOT logged in
    redirect("login.php"); // Redirect to staff login page
} // End staff login check

update_staff_last_seen($conn); // Update staff last_seen on every page load (online tracking)

$search_plate = trim($_GET["plate"] ?? ""); // Read search plate from URL
$auto_refresh = ($search_plate === "");

$live_status = [
    "message" => "Waiting for vehicle...",
    "type" => "info",
    "updated_at" => ""
];

$status_file = __DIR__ . "/../live_status.json";

if (file_exists($status_file)) {
    $json = json_decode(file_get_contents($status_file), true);

    if (is_array($json)) {
        $live_status["message"] = $json["message"] ?? "Waiting for vehicle...";
        $live_status["type"] = $json["type"] ?? "info";
        $live_status["updated_at"] = $json["updated_at"] ?? "";
    }
}
// Latest captures (shows order status if order exists, otherwise capture status)
$new_captures = $conn->query("SELECT 
                                c.id, 
                                c.plate_no, 
                                c.captured_at, 
                                c.token, 
                                COALESCE(o.status, c.status) AS status
                              FROM captures c
                              LEFT JOIN orders o ON o.capture_id = c.id
                              WHERE c.plate_no IS NOT NULL
                                AND TRIM(c.plate_no) <> ''
                                AND LENGTH(TRIM(c.plate_no)) >= 4
                              ORDER BY c.captured_at DESC
                              LIMIT 3");

// Load orders by status (IMPORTANT: use total_price only, NOT total_amount)
$pending_orders = $conn->query("SELECT id, plate_no, created_at, status, total_price 
                                FROM orders 
                                WHERE status='PENDING' 
                                ORDER BY created_at DESC 
                                LIMIT 20"); // PENDING orders

$paid_orders = $conn->query("SELECT id, plate_no, created_at, status, total_price 
                             FROM orders 
                             WHERE status='PAID' 
                             ORDER BY created_at DESC 
                             LIMIT 20"); // PAID orders

$washing_orders = $conn->query("SELECT id, plate_no, created_at, status, total_price 
                                FROM orders 
                                WHERE status='WASHING' 
                                ORDER BY created_at DESC 
                                LIMIT 20"); // WASHING orders

$done_orders = $conn->query("SELECT id, plate_no, created_at, status, total_price 
                             FROM orders 
                             WHERE status='DONE' 
                             ORDER BY created_at DESC 
                             LIMIT 20"); // DONE orders

$search_results = null; // Prepare search results variable

if ($search_plate !== "") { // If staff typed a plate to search
    $like = "%" . $search_plate . "%"; // Create LIKE pattern
    $stmt = $conn->prepare("SELECT id, plate_no, created_at, status, total_price 
                            FROM orders 
                            WHERE plate_no LIKE ? 
                            ORDER BY created_at DESC 
                            LIMIT 50"); // Prepare search query
    $stmt->bind_param("s", $like); // Bind search value
    $stmt->execute(); // Execute search query
    $search_results = $stmt->get_result(); // Store results
} // End search check

?> <!-- End PHP, start HTML -->
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- Set language -->
<head> <!-- Head start -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

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
    
    background: rgba(0,0,0,0.75); /* lighter than before = nicer look */

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
</style>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const plateInput = document.getElementById("plate");
    let refreshAllowed = <?php echo $auto_refresh ? 'true' : 'false'; ?>;

    function stopRefreshIfSearching() {
        if (!plateInput) return;

        const hasText = plateInput.value.trim() !== "";
        const isFocused = document.activeElement === plateInput;

        if (hasText || isFocused) {
            refreshAllowed = false;
        } else {
            refreshAllowed = true;
        }
    }

    if (plateInput) {
        plateInput.addEventListener("input", stopRefreshIfSearching);
        plateInput.addEventListener("focus", stopRefreshIfSearching);
        plateInput.addEventListener("blur", stopRefreshIfSearching);
    }

    function updateLiveStatus() {
        fetch("../live_status.json?ts=" + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                const msgEl = document.getElementById("live-status-message");
                const timeEl = document.getElementById("live-status-time");

                if (msgEl) {
                    msgEl.textContent = data.message || "Waiting for vehicle...";
                }

                if (timeEl) {
                    timeEl.textContent = data.updated_at || "-";
                }
            })
            .catch(err => console.log("Live status fetch failed:", err));
    }

    // Live status updates every 1 second
    setInterval(updateLiveStatus, 1000);

    // Full page reload every 15 seconds, but stop if user is searching
    setInterval(function () {
        stopRefreshIfSearching();

        if (refreshAllowed) {
            location.reload();
        }
    }, 15000);
});

</script>
    <title>Staff Dashboard</title> <!-- Page title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Load CSS -->
</head> <!-- Head end -->
<body> <!-- Body start -->

<div class="center"> <!-- Center wrapper -->
    <div class="container wide"> <!-- Wide container -->

        <h1>Staff Dashboard</h1> <!-- Page heading -->

        <p class="small"> <!-- Info line -->
            Logged in as: <b><?php echo htmlspecialchars($_SESSION["staff_username"]); ?></b> <!-- Show staff username -->
            | <a href="logout.php">Logout</a> <!-- Logout link -->
        </p> <!-- End info line -->

        <div class="topbar"> <!-- Top bar -->
            <div class="badge">ANPR Simulator Mode</div> <!-- Badge -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;"> <!-- Buttons row -->
                <a href="transactions.php" class="btn-link">View Transactions</a> <!-- Transactions link -->
                <a href="capture_simulator.php" class="btn-link">Open Capture Simulator</a> <!-- Capture simulator link -->
            </div> <!-- End buttons row -->
        </div> <!-- End topbar -->

        <div class="card" style="margin-bottom:18px;">
    <h2 style="margin-top:0;">Live Vehicle Status</h2>
    <p id="live-status-message" style="font-size:16px; font-weight:700; margin-bottom:6px;">
    <?php echo htmlspecialchars($live_status["message"]); ?>
</p>
<p class="small">
    Last update: <span id="live-status-time"><?php echo htmlspecialchars($live_status["updated_at"] ?: "-"); ?></span>
</p>
</div>

        <h2>Search Order by Plate</h2> <!-- Search title -->

        <form method="GET"> <!-- Search form -->
            <label for="plate">Plate Number</label> <!-- Label -->
            <input id="plate" name="plate" type="text" class="input" placeholder="No Space. Example: ABC1234" value="<?php echo htmlspecialchars($search_plate); ?>"> <!-- Input -->
            <button type="submit">Search</button> <!-- Submit -->
        </form> <!-- End form -->

        <?php if ($search_results !== null): ?> <!-- If search ran -->
            <div class="table-wrap"> <!-- Table wrap -->
                <table> <!-- Table start -->
                    <tr> <!-- Header row -->
                        <th>ID</th> <!-- Column -->
                        <th>Plate</th> <!-- Column -->
                        <th>Time and Date</th> <!-- Column -->
                        <th>Status</th> <!-- Column -->
                        <th>Total (RM)</th> <!-- Column -->
                    </tr> <!-- End header -->

                    <?php if ($search_results->num_rows > 0): ?> <!-- If results exist -->
                        <?php while ($o = $search_results->fetch_assoc()): ?> <!-- Loop results -->
                            <tr> <!-- Row -->
                                <td><?php echo (int)$o["id"]; ?></td> <!-- Order id -->
                                <td><?php echo htmlspecialchars($o["plate_no"]); ?></td> <!-- Plate -->
                                <td><?php echo htmlspecialchars($o["created_at"]); ?></td> <!-- Created time -->
                                <td><?php echo htmlspecialchars($o["status"]); ?></td> <!-- Status -->
                                <td><?php echo number_format((float)$o["total_price"], 2); ?></td> <!-- Total -->
                            </tr> <!-- End row -->
                        <?php endwhile; ?> <!-- End loop -->
                    <?php else: ?> <!-- If none -->
                        <tr> <!-- Row -->
                            <td colspan="5">No orders found for this plate.</td> <!-- Message -->
                        </tr> <!-- End row -->
                    <?php endif; ?> <!-- End results check -->
                </table> <!-- End table -->
            </div> <!-- End table wrap -->
        <?php endif; ?> <!-- End search check -->

        <h2>Latest ANPR Captures</h2> <!-- Captures title -->

        <div class="table-wrap"> <!-- Captures wrap -->
            <table> <!-- Captures table -->
                <tr> <!-- Header row -->
                    <th>ID</th> <!-- ID -->
                    <th>Plate</th> <!-- Plate -->
                    <th>Time and Date</th> <!-- Time -->
                    <th>Token</th> <!-- Token -->
                    <th>Status</th> <!-- Status -->
                    <th>Action</th> <!-- Action -->
                </tr> <!-- End header -->

                <?php if ($new_captures && $new_captures->num_rows > 0): ?> <!-- If captures exist -->
                    <?php while ($r = $new_captures->fetch_assoc()): ?> <!-- Loop captures -->
                        <tr> <!-- Row -->
                            <td><?php echo (int)$r["id"]; ?></td> <!-- Capture id -->
                            <td><?php echo htmlspecialchars($r["plate_no"]); ?></td> <!-- Plate -->
                            <td><?php echo htmlspecialchars($r["captured_at"]); ?></td> <!-- Time -->
                            <td><?php echo htmlspecialchars($r["token"]); ?></td> <!-- Token -->
                            <td><?php echo htmlspecialchars($r["status"]); ?></td> <!-- Status -->
                            <td>
                                <a href="print_qr.php?id=<?php echo (int)$r["id"]; ?>">Print QR</a>
                            </td> <!-- Action -->
                        </tr> <!-- End row -->
                    <?php endwhile; ?> <!-- End loop -->
                <?php else: ?> <!-- No captures -->
                    <tr> <!-- Row -->
                        <td colspan="6">No captures found.</td> <!-- Message -->
                    </tr> <!-- End row -->
                <?php endif; ?> <!-- End captures check -->
            </table> <!-- End captures table -->
        </div> <!-- End captures wrap -->

        <h2>Orders</h2> <!-- Orders title -->

        <h3>PENDING</h3> <!-- PENDING section -->
        <?php $orders = $pending_orders; ?> <!-- Set $orders for partial -->
        <?php include __DIR__ . "/partials/orders_rows.php"; ?> <!-- Include partial -->

        <h3>PAID</h3> <!-- PAID section -->
        <?php $orders = $paid_orders; ?> <!-- Set $orders for partial -->
        <?php include __DIR__ . "/partials/orders_rows.php"; ?> <!-- Include partial -->

        <h3>WASHING</h3> <!-- WASHING section -->
        <?php $orders = $washing_orders; ?> <!-- Set $orders for partial -->
        <?php include __DIR__ . "/partials/orders_rows.php"; ?> <!-- Include partial -->

        <h3>DONE</h3> <!-- DONE section -->
        <?php $orders = $done_orders; ?> <!-- Set $orders for partial -->
        <?php include __DIR__ . "/partials/orders_rows.php"; ?> <!-- Include partial -->

    </div> <!-- End container -->
</div> <!-- End center -->

</body> <!-- End body -->
</html> <!-- End HTML -->