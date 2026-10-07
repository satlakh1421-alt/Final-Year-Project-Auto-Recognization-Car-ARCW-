<?php
error_reporting(E_ALL); // enable reporting of all types of PHP errors (warnings, notices, etc.)
ini_set('display_errors', 1); // make sure errors are displayed on the screen (useful for debugging)

header("Content-Type: application/json"); // tell the browser that this script will return JSON data

$message = trim($_POST["message"] ?? ""); // get 'message' from POST request, remove extra spaces, default to empty string if not set
$type = trim($_POST["type"] ?? "info"); // get 'type' from POST request, remove spaces, default value is "info"

if ($message === "") { // check if message is empty
    echo json_encode(["ok" => false, "error" => "No message provided"]); // return JSON error response
    exit; // stop execution of the script
}

$data = [ // create an associative array to store data
    "message" => $message, // store the message text
    "type" => $type, // store the type (info, warning, etc.)
    "updated_at" => date("Y-m-d H:i:s") // store current date and time in format YYYY-MM-DD HH:MM:SS
];

$file = __DIR__ . "/live_status.json"; // define file path (same folder as this PHP file)

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)); // convert array to JSON and save into file (formatted nicely)

echo json_encode(["ok" => true]); // return success response in JSON format