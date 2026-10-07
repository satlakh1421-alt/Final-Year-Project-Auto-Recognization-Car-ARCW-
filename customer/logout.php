<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/functions.php";

unset($_SESSION["customer_id"]);
unset($_SESSION["customer_email"]);
unset($_SESSION["order_id"]);
unset($_SESSION["qr_token"]);
unset($_SESSION["capture_id"]);
unset($_SESSION["plate_no"]);

redirect("login.php");