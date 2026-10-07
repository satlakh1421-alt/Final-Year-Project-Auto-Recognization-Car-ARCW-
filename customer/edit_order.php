<?php // Start PHP
require_once __DIR__ . "/../includes/db.php"; // Load database connection + start session
require_once __DIR__ . "/../includes/functions.php"; // Load helper functions (redirect, login check)

if (!is_customer_logged_in()) { // If customer not logged in
    redirect("login.php"); // Send to login page
} // End login check

$customer_id = (int)$_SESSION["customer_id"]; // Get customer id from session

if (!isset($_SESSION["order_id"])) { // If customer has no active order id in session
    redirect("dashboard.php"); // Go back to dashboard
} // End session order check

$order_id = (int)$_SESSION["order_id"]; // Get order id from session

$stmtO = $conn->prepare( // Prepare SQL to load the order
    "SELECT id, customer_id, capture_id, plate_no, car_color, car_type, service_id, status, total_price 
     FROM orders 
     WHERE id = ? AND customer_id = ? 
     LIMIT 1"
); // End prepare

$stmtO->bind_param("ii", $order_id, $customer_id); // Bind order id + customer id
$stmtO->execute(); // Execute the query
$order = $stmtO->get_result()->fetch_assoc(); // Fetch order row as array

if (!$order) { // If order not found
    redirect("dashboard.php"); // Go back dashboard
} // End not found check

if ($order["status"] === "WASHING" || $order["status"] === "DONE") { // If washing already started OR done
    redirect("status.php"); // Block editing and show status page
} // End block edit

$services_res = $conn->query("SELECT id, name, price, category FROM services ORDER BY category ASC, name ASC"); // Load services
$addons_res = $conn->query("SELECT id, name, price FROM addons ORDER BY name ASC"); // Load addons

$services_by_cat = []; // Create array for category grouping

while ($s = $services_res->fetch_assoc()) { // Loop each service row
    $cat = $s["category"]; // Read category
    if (!isset($services_by_cat[$cat])) { // If category key not created yet
        $services_by_cat[$cat] = []; // Create empty list for category
    } // End category create check
    $services_by_cat[$cat][] = $s; // Add service into category list
} // End service loop

$categories = array_keys($services_by_cat); // Get all category names

$selected_addons = []; // Store addon ids already chosen for this order

$stmtSel = $conn->prepare("SELECT addon_id FROM order_addons WHERE order_id = ?"); // Query addon mapping
$stmtSel->bind_param("i", $order_id); // Bind order id
$stmtSel->execute(); // Execute mapping query
$resSel = $stmtSel->get_result(); // Get mapping results

while ($r = $resSel->fetch_assoc()) { // Loop rows
    $selected_addons[] = (int)$r["addon_id"]; // Add addon id
} // End loop

$msg = ""; // Message text
$msg_type = ""; // Message type (success/error)

if ($_SERVER["REQUEST_METHOD"] === "POST") { // If form submitted
    $car_color = trim($_POST["car_color"] ?? ""); // Read car color
    $car_type = trim($_POST["car_type"] ?? ""); // Read car type
    $service_id = (int)($_POST["service_id"] ?? 0); // Read service id
    $addon_ids = $_POST["addon_ids"] ?? []; // Read addon ids list

    if ($car_color === "" || $car_type === "" || $service_id === 0) { // Validate required fields
        $msg = "Please fill car color, choose car type, and select a service."; // Set error message
        $msg_type = "error"; // Set message type
    } else { // If validation passed

        $stmtS = $conn->prepare("SELECT price FROM services WHERE id = ?"); // Get service price
        $stmtS->bind_param("i", $service_id); // Bind service id
        $stmtS->execute(); // Execute query
        $service_row = $stmtS->get_result()->fetch_assoc(); // Fetch row

        $total = (float)($service_row["price"] ?? 0); // Start total with service price

        $clean_addon_ids = []; // Clean array of addon ids
        foreach ($addon_ids as $aid) { // Loop addon ids from form
            $clean_addon_ids[] = (int)$aid; // Convert each to integer
        } // End addon loop

        if (count($clean_addon_ids) > 0) { // If addons were selected
            $in = implode(",", array_fill(0, count($clean_addon_ids), "?")); // Build placeholders
            $types = str_repeat("i", count($clean_addon_ids)); // Create binding types
            $sqlA = "SELECT price FROM addons WHERE id IN ($in)"; // SQL for addon prices

            $stmtA = $conn->prepare($sqlA); // Prepare addon query
            $stmtA->bind_param($types, ...$clean_addon_ids); // Bind all ids
            $stmtA->execute(); // Execute
            $resA = $stmtA->get_result(); // Get results

            while ($ar = $resA->fetch_assoc()) { // Loop addon prices
                $total += (float)$ar["price"]; // Add addon price to total
            } // End addon prices loop
        } // End addon selected check

        $stmtU = $conn->prepare( // Prepare update query for orders table
            "UPDATE orders 
             SET car_color = ?, car_type = ?, service_id = ?, total_price = ? 
             WHERE id = ? AND customer_id = ?"
        ); // End prepare

        $stmtU->bind_param("ssidii", $car_color, $car_type, $service_id, $total, $order_id, $customer_id); // Bind update values
        $okU = $stmtU->execute(); // Execute update

        if ($okU) { // If update success

            $stmtD = $conn->prepare("DELETE FROM order_addons WHERE order_id = ?"); // Delete old addon mapping
            $stmtD->bind_param("i", $order_id); // Bind order id
            $stmtD->execute(); // Execute delete

            foreach ($clean_addon_ids as $aid) { // Loop new addons
                $stmtI = $conn->prepare("INSERT INTO order_addons (order_id, addon_id) VALUES (?, ?)"); // Insert mapping
                $stmtI->bind_param("ii", $order_id, $aid); // Bind ids
                $stmtI->execute(); // Execute insert
            } // End insert loop

            $msg = "Order updated successfully."; // Success message
            $msg_type = "success"; // Success type

            $order["car_color"] = $car_color; // Update local variable for showing on screen
            $order["car_type"] = $car_type; // Update local variable
            $order["service_id"] = $service_id; // Update local variable
            $order["total_price"] = $total; // Update local variable
            $selected_addons = $clean_addon_ids; // Update addon list
        } else { // If update failed
            $msg = "Failed to update order. Try again."; // Error
            $msg_type = "error"; // Error type
        } // End update result check
    } // End validation check
} // End POST handler
?>
<!DOCTYPE html> <!-- HTML5 -->
<html lang="en"> <!-- Start HTML -->
<head> <!-- Head start -->
    <meta charset="UTF-8"> <!-- Encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->
    <title>Edit Order</title> <!-- Title -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- Your CSS -->
    
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
    
    
</head> <!-- End head -->
<body> <!-- Start body -->

<div class="center"> <!-- Center wrapper -->
    <div class="container wide"> <!-- Container -->

        <h1>Edit Order</h1> <!-- Page title -->

        <p class="small"> <!-- Small line -->
            Plate (cannot edit): <b><?php echo htmlspecialchars($order["plate_no"]); ?></b> <!-- Show plate -->
            | <a href="status.php">Back to Status</a> <!-- Back link -->
        </p> <!-- End small -->

        <?php if ($msg !== ""): ?> <!-- If message exists -->
            <div class="msg <?php echo $msg_type; ?>"> <!-- Message box -->
                <?php echo htmlspecialchars($msg); ?> <!-- Show message -->
            </div> <!-- End message -->
        <?php endif; ?> <!-- End message check -->

        <form method="POST" id="editForm"> <!-- Start form -->

            <label for="car_color">Car Color</label> <!-- Label -->
            <input id="car_color" name="car_color" type="text" class="input" value="<?php echo htmlspecialchars($order["car_color"] ?? ""); ?>" required> <!-- Input -->

            <label>Car Type</label> <!-- Label -->
            <div class="radio-group"> <!-- Radio group -->
                <label class="radio-item"> <!-- Item -->
                    <input type="radio" name="car_type" value="Car" <?php echo ($order["car_type"] === "Car") ? "checked" : ""; ?> required> <!-- Radio -->
                    Car <!-- Text -->
                </label> <!-- End item -->
                <label class="radio-item"> <!-- Item -->
                    <input type="radio" name="car_type" value="SUV/MPV" <?php echo ($order["car_type"] === "SUV/MPV") ? "checked" : ""; ?> required> <!-- Radio -->
                    SUV/MPV <!-- Text -->
                </label> <!-- End item -->
                <label class="radio-item"> <!-- Item -->
                    <input type="radio" name="car_type" value="Bike" <?php echo ($order["car_type"] === "Bike") ? "checked" : ""; ?> required> <!-- Radio -->
                    Bike <!-- Text -->
                </label> <!-- End item -->
            </div> <!-- End group -->

            <label for="service_cat">Service Category</label> <!-- Label -->
            <select id="service_cat" class="select" required> <!-- Category select -->
                <option value="">-- Select Category --</option> <!-- Placeholder -->
                <?php foreach ($categories as $cat): ?> <!-- Loop categories -->
                    <option value="<?php echo htmlspecialchars($cat); ?>"> <!-- Option -->
                        <?php echo htmlspecialchars($cat); ?> <!-- Text -->
                    </option> <!-- End option -->
                <?php endforeach; ?> <!-- End loop -->
            </select> <!-- End category select -->

            <label for="service_id">Service</label> <!-- Label -->
            <select id="service_id" name="service_id" class="select" required> <!-- Service select -->
                <option value="">-- Select Service --</option> <!-- Placeholder -->
            </select> <!-- End service select -->

            <label for="service_price">Service Price (RM)</label> <!-- Label -->
            <input id="service_price" type="text" class="input readonly" value="0.00" readonly> <!-- Readonly -->

            <label>Add-on Services</label> <!-- Label -->
            <div class="addon-list"> <!-- Addon list -->
                <?php while($a = $addons_res->fetch_assoc()): ?> <!-- Loop addons -->
                    <label class="addon-item"> <!-- Addon item -->
                        <input type="checkbox"
                               name="addon_ids[]"
                               value="<?php echo (int)$a["id"]; ?>"
                               data-price="<?php echo htmlspecialchars($a["price"]); ?>"
                               <?php echo in_array((int)$a["id"], $selected_addons, true) ? "checked" : ""; ?>
                        > <!-- Checkbox -->
                        <?php echo htmlspecialchars($a["name"]); ?> (RM <?php echo htmlspecialchars($a["price"]); ?>) <!-- Text -->
                    </label> <!-- End item -->
                <?php endwhile; ?> <!-- End loop -->
            </div> <!-- End addon list -->

            <div class="price-box"> <!-- Price box -->
                <div class="price-row"> <!-- Row -->
                    <span>Service</span> <!-- Label -->
                    <b>RM <span id="servicePriceView">0.00</span></b> <!-- Value -->
                </div> <!-- End row -->
                <div class="price-row"> <!-- Row -->
                    <span>Add-ons</span> <!-- Label -->
                    <b>RM <span id="addonPriceView">0.00</span></b> <!-- Value -->
                </div> <!-- End row -->
                <div class="price-row"> <!-- Row -->
                    <span>Total</span> <!-- Label -->
                    <b>RM <span id="totalPriceView">0.00</span></b> <!-- Value -->
                </div> <!-- End row -->
            </div> <!-- End price box -->

            <button type="submit">Save Changes</button> <!-- Save button -->

        </form> <!-- End form -->

    </div> <!-- End container -->
</div> <!-- End center -->

<script> // Start JS
    const serviceData = <?php echo json_encode($services_by_cat); ?>; // Services grouped by category
    const currentServiceId = <?php echo (int)$order["service_id"]; ?>; // Current service id from DB

    const catSelect = document.getElementById("service_cat"); // Category dropdown
    const serviceSelect = document.getElementById("service_id"); // Service dropdown

    const servicePriceInput = document.getElementById("service_price"); // Price input
    const servicePriceView = document.getElementById("servicePriceView"); // Price display
    const addonPriceView = document.getElementById("addonPriceView"); // Addon total display
    const totalPriceView = document.getElementById("totalPriceView"); // Total display

    function toMoney(n) { // Format to 2 decimals
        return (Math.round(n * 100) / 100).toFixed(2); // Return formatted
    } // End function

    function rebuildServiceList() { // Build service list when category changes
        const selectedCat = catSelect.value; // Read selected category
        serviceSelect.innerHTML = '<option value="">-- Select Service --</option>'; // Reset services

        if (!selectedCat || !serviceData[selectedCat]) { // If no category selected
            calculateTotal(); // Recalculate to show 0
            return; // Stop
        } // End check

        serviceData[selectedCat].forEach(s => { // Loop services in that category
            const opt = document.createElement("option"); // Create option
            opt.value = s.id; // Set id
            opt.textContent = `${s.name} (RM ${toMoney(parseFloat(s.price))})`; // Set text
            opt.dataset.price = s.price; // Save price in dataset

            if (parseInt(s.id) === parseInt(currentServiceId)) { // If this is current service
                opt.selected = true; // Auto select it
            } // End check

            serviceSelect.appendChild(opt); // Add to dropdown
        }); // End loop

        calculateTotal(); // Recalculate
    } // End function

    function calculateTotal() { // Calculate totals
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex]; // Get selected service option
        const servicePrice = parseFloat(selectedOption?.dataset?.price || "0"); // Get service price

        let addonTotal = 0; // Addon total
        document.querySelectorAll('input[name="addon_ids[]"]:checked').forEach(cb => { // Loop checked addons
            addonTotal += parseFloat(cb.dataset.price || "0"); // Add addon price
        }); // End loop

        const total = servicePrice + addonTotal; // Total

        servicePriceInput.value = toMoney(servicePrice); // Set input
        servicePriceView.textContent = toMoney(servicePrice); // Show service price
        addonPriceView.textContent = toMoney(addonTotal); // Show addons price
        totalPriceView.textContent = toMoney(total); // Show total
    } // End function

    function autoPickCategory() { // Auto select correct category based on current service id
        for (const cat in serviceData) { // Loop each category
            const list = serviceData[cat]; // Get list
            const found = list.find(x => parseInt(x.id) === parseInt(currentServiceId)); // Find service
            if (found) { // If found
                catSelect.value = cat; // Select category
                rebuildServiceList(); // Build list
                return; // Stop
            } // End found check
        } // End loop
    } // End function

    catSelect.addEventListener("change", rebuildServiceList); // Rebuild when category changes
    serviceSelect.addEventListener("change", calculateTotal); // Recalc when service changes

    document.querySelectorAll('input[name="addon_ids[]"]').forEach(cb => { // Loop addon checkboxes
        cb.addEventListener("change", calculateTotal); // Recalc when addon changes
    }); // End loop

    autoPickCategory(); // Run on page load
</script> <!-- End JS -->

</body> <!-- End body -->
</html> <!-- End HTML -->
