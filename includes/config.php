<?php
// Always start session in one place
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kuala_Lumpur');

// Database configuration
define("DB_HOST", "localhost");
define("DB_USER", "u764947793_carwash_user");
define("DB_PASS", "CarWash@4321");
define("DB_NAME", "u764947793_carwash_db");

// Base URL (your current project URL)
define("BASE_URL", "https://servproautospa.com");
// ==========================
// ToyyibPay (SANDBOX) Config
// ==========================
define("TOYYIBPAY_BASE_URL", "https://toyyibpay.com");

// Paste your SANDBOX userSecretKey here (from dev.toyyibpay.com)
define("TOYYIBPAY_SECRET_KEY", "pcb3ugse-gnbe-9yai-chq7-ysb6mugkch1l");

// Your sandbox category code you gave me 
define("TOYYIBPAY_CATEGORY_CODE", "6bnv2kek");

// Return URL (browser redirect)
define("TOYYIBPAY_RETURN_URL", BASE_URL . "/customer/payment_return.php");

// Callback URL (server-to-server) — will only work when your system is hosted publicly
define("TOYYIBPAY_CALLBACK_URL", BASE_URL . "/api/toyyibpay_callback.php");

define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_PORT", 587);
define("MAIL_USERNAME", "servproautospa.support@gmail.com");
define("MAIL_PASSWORD", "mzzdvntzcuhjxxyl");
define("MAIL_FROM", "servproautospa.support@gmail.com");
define("MAIL_FROM_NAME", "Servpro Autospa");

?>