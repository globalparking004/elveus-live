<?php 
// Get the JSON data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);
print_r($data);
echo'testing php cron file /n';