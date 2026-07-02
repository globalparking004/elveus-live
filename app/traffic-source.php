<?php
// index under form
<input hidden name="traffic_source" id="traffic_source" value="<?= isset($_GET['source']) ? $_GET['source'] : '';?>" />
// selectparking
$traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';
$url = "";
// Create the query string
$queryString = http_build_query([
    'airport' => $airport,
    'selectedDate' => $selectedDate,
    'changedDate' => $changedDate,
    'arrivalTime' => $arrivalTime,
    'departureTime' => $departureTime,
    'code' => $promoCode,
    'website' => $website,
    'access_token'=>'5MEsB9lLwVqu4qndXvEUE428bqGZY',
    'cur'=>$cur,
    'webtype'=>$webtype,
    'traffic_source'=>$traffic_source
]);
// provide detail
$traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';
<input type="hidden" name="traffic_source" value="<?= $traffic_source; ?>">
// create_booking_api
$traffic_source=isset($_POST['traffic_source']) ? $_POST['traffic_source'] : '';

'webtype'=>$webtype,
  'traffic_source'=> $traffic_source