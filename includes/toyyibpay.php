<?php
require_once __DIR__ . "/config.php";

function toyyibpay_post($endpoint, $data)
{
    $url = rtrim(TOYYIBPAY_BASE_URL, "/") . $endpoint;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        "url" => $url,
        "http_code" => $http_code,
        "curl_error" => $curl_error,
        "raw_response" => $response,
        "json" => json_decode($response, true)
    ];
}

function create_bill($data)
{
    $result = toyyibpay_post("/index.php/api/createBill", $data);

    if (!isset($result["json"][0]["BillCode"])) {
        echo "<pre>";
        echo "FAILED TO CREATE BILL\n\n";
        echo "URL:\n";
        print_r($result["url"]);
        echo "\n\nHTTP CODE:\n";
        print_r($result["http_code"]);
        echo "\n\nCURL ERROR:\n";
        print_r($result["curl_error"]);
        echo "\n\nRAW RESPONSE:\n";
        print_r($result["raw_response"]);
        echo "\n\nDECODED JSON:\n";
        print_r($result["json"]);
        echo "\n\nPAYLOAD SENT:\n";
        print_r($data);
        echo "</pre>";
        exit;
    }

    return $result["json"][0]["BillCode"];
}

function verify_bill($billcode)
{
    return toyyibpay_post("/index.php/api/getBillTransactions", [
        "userSecretKey" => TOYYIBPAY_SECRET_KEY,
        "billCode" => $billcode
    ]);
}
?>