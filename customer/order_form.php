<?php // Start PHP tag

error_reporting(E_ALL); // show all PHP errors for debugging so white screen becomes visible error if something fails
ini_set('display_errors', 1); // force PHP to display errors in browser

require_once __DIR__ . "/../includes/db.php"; // include database connection file and start session automatically
require_once __DIR__ . "/../includes/functions.php"; // include helper functions such as login check and redirect

if (!is_customer_logged_in()) { // check whether current user is logged in as customer
    redirect("login.php"); // if customer is not logged in, redirect to customer login page
}

if (!isset($_SESSION["qr_token"], $_SESSION["capture_id"], $_SESSION["plate_no"])) { // check whether required QR session values exist
    redirect("dashboard.php"); // if customer did not come through QR flow, redirect back to dashboard
}

$capture_id = (int)$_SESSION["capture_id"]; // get capture ID from session and convert to integer for safety
$plate_no = $_SESSION["plate_no"]; // get detected plate number from session


// ============================
// Check if vehicle exists before
// ============================

$stmtPrev = $conn->prepare("
SELECT car_color, car_type
FROM orders
WHERE plate_no = ?
AND car_color IS NOT NULL AND TRIM(car_color) <> ''
AND car_type IS NOT NULL AND TRIM(car_type) <> ''
ORDER BY id DESC
LIMIT 1
"); // prepare query to find the latest previous order for this same plate number with saved car details

if (!$stmtPrev) { // check whether prepare failed
    die("Prepare failed (previous vehicle): " . $conn->error); // stop and show database error
}

$stmtPrev->bind_param("s", $plate_no); // bind plate number safely into query
$stmtPrev->execute(); // execute query
$previous_vehicle = $stmtPrev->get_result()->fetch_assoc(); // fetch previous vehicle record if it exists
$stmtPrev->close(); // close prepared statement

$is_returning_vehicle = ($previous_vehicle !== null); // set true if this plate number has previous saved vehicle details
$returning_car_type = $is_returning_vehicle ? trim($previous_vehicle["car_type"]) : ""; // get previous car type if available, otherwise empty string


// ============================
// Normalize car type
// ============================

$display_car_type = ""; // variable to store cleaned/normalized car type for display

if ($is_returning_vehicle) { // only normalize when returning vehicle exists

    $normalized = strtolower($returning_car_type); // convert previous car type to lowercase for easier comparison
    $normalized = str_replace([" ", "/", "-", "_"], "", $normalized); // remove spaces and common symbols so different spellings can still match

    if (strpos($normalized, "suv") !== false || strpos($normalized, "mpv") !== false) { // check whether text contains suv or mpv
        $display_car_type = "SUV/MPV"; // normalize to one standard display value
    } elseif (strpos($normalized, "bike") !== false || strpos($normalized, "motor") !== false) { // check whether text contains bike or motor
        $display_car_type = "Bike"; // normalize to Bike
    } else { // if not SUV/MPV and not Bike
        $display_car_type = "Car"; // default to Car
    }
}


// ============================
// Load services
// ============================

$services_res = $conn->query("
SELECT id, name, price, category
FROM services
ORDER BY category ASC, name ASC
"); // query all services sorted by category and then service name

if (!$services_res) { // check if query failed
    die("Query failed (services): " . $conn->error); // stop and show database error
}

$addons_res = $conn->query("
SELECT id, name, price
FROM addons
ORDER BY name ASC
"); // query all add-on services sorted by name

if (!$addons_res) { // check if add-ons query failed
    die("Query failed (addons): " . $conn->error); // stop and show database error
}

$services_by_cat = []; // array to group services under their categories

while ($s = $services_res->fetch_assoc()) { // loop through every service row returned from database
    $services_by_cat[$s["category"]][] = $s; // store each service into category-based array
}

$categories = array_keys($services_by_cat); // extract list of category names from grouped services array

$msg = ""; // variable to store message text for user
$msg_type = "error"; // default message type set to error unless changed


// ============================
// Form Submit
// ============================

if ($_SERVER["REQUEST_METHOD"] === "POST") { // check whether order form has been submitted

    $car_color = trim($_POST["car_color"] ?? ""); // read car color from form and remove extra spaces
    $car_type = trim($_POST["car_type"] ?? ""); // read car type from form and remove extra spaces
    $service_id = (int)($_POST["service_id"] ?? 0); // read selected service ID and convert to integer
    $addon_ids = $_POST["addon_ids"] ?? []; // read selected add-on IDs as array or empty array if nothing selected

    if ($is_returning_vehicle) { // if this is a returning vehicle
        $car_color = $previous_vehicle["car_color"]; // force car color to previous saved color
        $car_type = $display_car_type; // force car type to normalized saved type
    }

    if ($car_color == "" || $car_type == "" || $service_id == 0) { // validate required fields

        $msg = $is_returning_vehicle
            ? "Please select a service." // for returning vehicle, only service selection is required
            : "Please fill car color, choose car type and select service."; // for new vehicle, color, type, and service are required

    } else { // continue only if validation passed

        $check = $conn->prepare("
SELECT id
FROM orders
WHERE capture_id = ?
ORDER BY id DESC
LIMIT 1
"); // prepare query to check whether an order already exists for this QR capture

        if (!$check) { // check whether prepare failed
            die("Prepare failed (existing order check): " . $conn->error); // show error
        }

        $check->bind_param("i", $capture_id); // bind capture ID
        $check->execute(); // execute query
        $existing = $check->get_result()->fetch_assoc(); // fetch existing order if there is one
        $check->close(); // close statement

        if ($existing) {

    // Check status of existing order
    $stmtChk = $conn->prepare("
        SELECT status
        FROM orders
        WHERE id = ?
        LIMIT 1
    ");
    $stmtChk->bind_param("i", $existing["id"]);
    $stmtChk->execute();
    $rowChk = $stmtChk->get_result()->fetch_assoc();
    $stmtChk->close();

    $st = strtoupper(trim($rowChk["status"] ?? ""));

    // nly reuse if order is still ACTIVE
    if ($st === "PENDING" || $st === "PAID" || $st === "WASHING") {
        $_SESSION["order_id"] = (int)$existing["id"];
        redirect("payment.php");
    }

    //if status = DONE → DO NOTHING
    // Let system continue and CREATE NEW ORDER
}


        // ============================
        // Calculate price
        // ============================

        $stmtS = $conn->prepare("SELECT price FROM services WHERE id = ?"); // prepare query to get selected service price
        if (!$stmtS) { // check whether prepare failed
            die("Prepare failed (service price): " . $conn->error); // show database error
        }

        $stmtS->bind_param("i", $service_id); // bind selected service ID
        $stmtS->execute(); // execute query
        $service_row = $stmtS->get_result()->fetch_assoc(); // fetch selected service row
        $stmtS->close(); // close statement

        $total = (float)($service_row["price"] ?? 0); // start total price with base service price

        $clean_addons = []; // array to store cleaned integer add-on IDs

        foreach ($addon_ids as $aid) { // loop through each selected add-on ID from form
            $clean_addons[] = (int)$aid; // convert each add-on ID to integer for safety
        }

        if (count($clean_addons) > 0) { // if customer selected at least one add-on

            $in = implode(",", array_fill(0, count($clean_addons), "?")); // create placeholders like ?,?,? depending on number of add-ons
            $types = str_repeat("i", count($clean_addons)); // create bind types string like "iii" for integer values

            $sql = "SELECT price FROM addons WHERE id IN ($in)"; // build SQL query to get prices of selected add-ons
            $stmtA = $conn->prepare($sql); // prepare add-ons price query

            if (!$stmtA) { // check whether prepare failed
                die("Prepare failed (addon prices): " . $conn->error); // show database error
            }

            $stmtA->bind_param($types, ...$clean_addons); // bind all selected add-on IDs dynamically
            $stmtA->execute(); // execute query
            $resA = $stmtA->get_result(); // get add-ons result set

            while ($a = $resA->fetch_assoc()) { // loop through each selected add-on row
                $total += (float)$a["price"]; // add add-on price into total
            }

            $stmtA->close(); // close statement
        }


        // ============================
        // Insert order
        // ============================

        $customer_id = (int)$_SESSION["customer_id"]; // get logged-in customer ID from session

        $stmtO = $conn->prepare("
INSERT INTO orders
(capture_id, customer_id, service_id, plate_no, car_color, car_type, status, total_price)
VALUES (?, ?, ?, ?, ?, ?, 'PENDING', ?)
"); // prepare query to insert new order with default status PENDING

        if (!$stmtO) { // check whether prepare failed
            die("Prepare failed (insert order): " . $conn->error); // show database error
        }

        $stmtO->bind_param(
            "iiisssd",
            $capture_id,
            $customer_id,
            $service_id,
            $plate_no,
            $car_color,
            $car_type,
            $total
        ); // bind all order values into insert query

        $ok = $stmtO->execute(); // execute order insert

        if ($ok) { // if order insert successful

            $order_id = $conn->insert_id; // get newly created order ID from database
            $stmtO->close(); // close insert statement

            foreach ($clean_addons as $aid) { // loop through each selected add-on ID

                $stmt = $conn->prepare("
INSERT INTO order_addons (order_id, addon_id)
VALUES (?, ?)
"); // prepare query to insert selected add-on into order_addons table

                if (!$stmt) { // check whether prepare failed
                    die("Prepare failed (insert order_addons): " . $conn->error); // show database error
                }

                $stmt->bind_param("ii", $order_id, $aid); // bind order ID and add-on ID
                $stmt->execute(); // execute insert
                $stmt->close(); // close statement
            }

            $stmtC = $conn->prepare("UPDATE captures SET status='PENDING' WHERE id = ?"); // prepare query to update capture status after order created
            if (!$stmtC) { // check whether prepare failed
                die("Prepare failed (update capture status): " . $conn->error); // show database error
            }

            $stmtC->bind_param("i", $capture_id); // bind capture ID
            $stmtC->execute(); // execute update
            $stmtC->close(); // close statement

            $_SESSION["order_id"] = $order_id; // save newly created order ID into session

            redirect("payment.php"); // redirect customer to payment page

        } else { // if order insert failed

            $msg = "Failed to save order."; // show error message to user
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- define UTF-8 character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- responsive design for phones and desktops -->
    <title>Order Form</title> <!-- page title shown in browser tab -->
    <link rel="stylesheet" href="../assets/style.css"> <!-- link external CSS stylesheet -->
    
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

<div class="center"> <!-- wrapper used to center content -->
    <div class="container wide"> <!-- wide container to hold form layout -->

        <h1>Car Details & Service</h1> <!-- main page title -->

        <p class="small"> <!-- small helper text -->
            Plate Number (auto): <b><?php echo htmlspecialchars($plate_no); ?></b> <!-- safely display detected plate number -->
        </p>

        <?php if ($is_returning_vehicle): ?> <!-- show message only if returning vehicle was detected -->
            <div class="msg success"> <!-- success style box -->
                Returning vehicle detected. Car details filled automatically. <!-- message -->
            </div>
        <?php endif; ?>

        <?php if ($msg != ""): ?> <!-- show validation or database message if exists -->
            <div class="msg <?php echo htmlspecialchars($msg_type); ?>"> <!-- message container with dynamic type -->
                <?php echo htmlspecialchars($msg); ?> <!-- safely display message -->
            </div>
        <?php endif; ?>

        <form method="POST"> <!-- order form starts here -->

            <label for="car_color">Car Color</label> <!-- label for car color input -->
            <input
                id="car_color"
                name="car_color"
                type="text"
                class="input"
                value="<?php echo htmlspecialchars($is_returning_vehicle ? $previous_vehicle["car_color"] : ""); ?>"
                <?php echo $is_returning_vehicle ? "readonly" : "required"; ?>
            > <!-- car color input: auto-filled and read-only for returning vehicle, required for new vehicle -->

            <label>Car Type</label> <!-- label for car type -->

            <?php if ($is_returning_vehicle): ?> <!-- if returning vehicle, show read-only type options -->

                <div class="radio-group" style="margin-bottom:4px;"> <!-- wrapper for read-only radio display -->

                    <label class="radio-item" style="<?php echo $display_car_type == 'Car' ? 'border:2px solid #22c55e;background:rgba(34,197,94,0.12);' : ''; ?>">
                        <input type="radio" disabled <?php echo $display_car_type == 'Car' ? 'checked' : ''; ?> style="opacity:1;margin-right:6px;"> <!-- disabled radio just for visual display -->
                        Car
                    </label>

                    <label class="radio-item" style="<?php echo $display_car_type == 'SUV/MPV' ? 'border:2px solid #22c55e;background:rgba(34,197,94,0.12);' : ''; ?>">
                        <input type="radio" disabled <?php echo $display_car_type == 'SUV/MPV' ? 'checked' : ''; ?> style="opacity:1;margin-right:6px;"> <!-- disabled radio just for visual display -->
                        SUV/MPV
                    </label>

                    <label class="radio-item" style="<?php echo $display_car_type == 'Bike' ? 'border:2px solid #22c55e;background:rgba(34,197,94,0.12);' : ''; ?>">
                        <input type="radio" disabled <?php echo $display_car_type == 'Bike' ? 'checked' : ''; ?> style="opacity:1;margin-right:6px;"> <!-- disabled radio just for visual display -->
                        Bike
                    </label>

                </div>

                <input type="hidden" name="car_type" value="<?php echo htmlspecialchars($display_car_type); ?>"> <!-- hidden input sends saved car type in form submit -->

                <p class="small" style="margin-top:0;margin-bottom:8px;line-height:1.2;"> <!-- helper text -->
                    Saved type: <b><?php echo htmlspecialchars($display_car_type); ?></b> <!-- show saved normalized type -->
                </p>

            <?php else: ?> <!-- if new vehicle, allow customer to choose car type -->

                <div class="radio-group"> <!-- wrapper for selectable radio buttons -->
                    <label class="radio-item">
                        <input type="radio" name="car_type" value="Car" required> <!-- selectable car radio -->
                        Car
                    </label>

                    <label class="radio-item">
                        <input type="radio" name="car_type" value="SUV/MPV" required> <!-- selectable SUV/MPV radio -->
                        SUV/MPV
                    </label>

                    <label class="radio-item">
                        <input type="radio" name="car_type" value="Bike" required> <!-- selectable bike radio -->
                        Bike
                    </label>
                </div>

            <?php endif; ?>

            <label for="service_cat">Service Category</label> <!-- label for service category dropdown -->
            <select id="service_cat" class="select" required> <!-- dropdown for service category -->
                <option value="">-- Select Category --</option> <!-- default category option -->

                <?php foreach ($categories as $cat): ?> <!-- loop through all category names -->
                    <option value="<?php echo htmlspecialchars($cat); ?>"> <!-- category option -->
                        <?php echo htmlspecialchars($cat); ?> <!-- display category safely -->
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="service_id">Service</label> <!-- label for actual service dropdown -->
            <select id="service_id" name="service_id" class="select" required> <!-- dropdown to be rebuilt by JavaScript based on category -->
                <option value="">-- Select Service --</option> <!-- default service option -->
            </select>

            <label for="service_price">Service Price (RM)</label> <!-- label for service price display -->
            <input id="service_price" class="input readonly" readonly value="0.00"> <!-- read-only input showing selected service price -->

            <label>Add-on Services</label> <!-- label for add-ons -->
            <div class="addon-list"> <!-- wrapper for add-on checkboxes -->

                <?php while ($a = $addons_res->fetch_assoc()): ?> <!-- loop through all add-on rows -->
                    <label class="addon-item"> <!-- single add-on item -->
                        <input
                            type="checkbox"
                            name="addon_ids[]"
                            value="<?php echo (int)$a["id"]; ?>"
                            data-price="<?php echo $a["price"]; ?>"
                        > <!-- checkbox storing add-on ID and price in data-price attribute -->

                        <?php echo htmlspecialchars($a["name"]); ?> <!-- display add-on name safely -->
                        (RM <?php echo $a["price"]; ?>) <!-- display add-on price -->
                    </label>
                <?php endwhile; ?>

            </div>

            <div class="price-box"> <!-- summary price box -->

                <div class="price-row"> <!-- service price row -->
                    <span>Service</span>
                    <b>RM <span id="servicePriceView">0.00</span></b> <!-- dynamic service price text -->
                </div>

                <div class="price-row"> <!-- add-ons total row -->
                    <span>Add-ons</span>
                    <b>RM <span id="addonPriceView">0.00</span></b> <!-- dynamic add-on total text -->
                </div>

                <div class="price-row"> <!-- grand total row -->
                    <span>Total</span>
                    <b>RM <span id="totalPriceView">0.00</span></b> <!-- dynamic total price text -->
                </div>

            </div>

            <button type="submit">Continue to Payment</button> <!-- submit button to proceed to payment -->

        </form>

    </div>
</div>

<script>
const serviceData = <?php echo json_encode($services_by_cat); ?>; // convert PHP grouped services array into JavaScript object for dynamic dropdown building

const catSelect = document.getElementById("service_cat"); // get category dropdown element
const serviceSelect = document.getElementById("service_id"); // get service dropdown element

const servicePriceInput = document.getElementById("service_price"); // get read-only service price input
const servicePriceView = document.getElementById("servicePriceView"); // get service price text in summary box
const addonPriceView = document.getElementById("addonPriceView"); // get add-on total text in summary box
const totalPriceView = document.getElementById("totalPriceView"); // get grand total text in summary box

function toMoney(n) { // helper function to format numbers into 2 decimal places
    return (Math.round(n * 100) / 100).toFixed(2); // round safely and force 2 decimal display
}

function rebuildService() { // function to rebuild services dropdown whenever category changes
    let cat = catSelect.value; // get selected category value

    serviceSelect.innerHTML = '<option value="">-- Select Service --</option>'; // reset service dropdown to default option

    if (!serviceData[cat]) { // if selected category has no services
        calculate(); // recalculate prices as zero
        return; // stop function
    }

    serviceData[cat].forEach(s => { // loop through each service under selected category
        let opt = document.createElement("option"); // create new option element
        opt.value = s.id; // set option value as service ID
        opt.textContent = s.name + " (RM " + toMoney(parseFloat(s.price)) + ")"; // set option text with service name and price
        opt.dataset.price = s.price; // store service price in data-price attribute
        serviceSelect.appendChild(opt); // add option into service dropdown
    });

    calculate(); // recalculate prices after rebuilding services
}

function calculate() { // function to calculate current selected service + add-on total
    let s = serviceSelect.options[serviceSelect.selectedIndex]; // get currently selected service option
    let servicePrice = parseFloat(s?.dataset?.price || 0); // read selected service price safely or use 0 if nothing selected

    let addonTotal = 0; // start add-on total at zero

    document.querySelectorAll('input[name="addon_ids[]"]:checked').forEach(cb => { // loop through all checked add-on checkboxes
        addonTotal += parseFloat(cb.dataset.price || 0); // add each selected add-on price into add-on total
    });

    let total = servicePrice + addonTotal; // calculate grand total

    servicePriceInput.value = toMoney(servicePrice); // update read-only service price input
    servicePriceView.textContent = toMoney(servicePrice); // update service price in summary box
    addonPriceView.textContent = toMoney(addonTotal); // update add-on total in summary box
    totalPriceView.textContent = toMoney(total); // update grand total in summary box
}

catSelect.addEventListener("change", rebuildService); // rebuild services whenever category changes
serviceSelect.addEventListener("change", calculate); // recalculate whenever service changes

document.querySelectorAll('input[name="addon_ids[]"]').forEach(cb => { // loop through all add-on checkboxes
    cb.addEventListener("change", calculate); // recalculate whenever checkbox selection changes
});

rebuildService(); // run once on page load so service dropdown and prices initialize properly
</script>

</body>
</html>