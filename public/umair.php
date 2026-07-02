<?php

$url = 'https://dublinairportparkandfly.com/test4.php'; // Note: Make sure to include http:// or https://

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if($response === false) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
    echo "succ";
}

curl_close($ch);

