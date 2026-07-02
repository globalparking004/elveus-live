<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';
// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_data = "select * from park_via_api";
$sql_data_re = $conn->query($sql_data);
if ($sql_data_re->num_rows > 0) {

    while ($row = mysqli_fetch_assoc($sql_data_re)) {

        $api_key = $row['api_key'];
        $subscription_key = $row['subscription_key'];
        $operator_id = $row['operator_id'];
        $producr_code = $row['product_code'];
        $airport = $row['airport'];


        // Initialize a cURL session
        $ch = curl_init();

        // Set the URL for the cURL session

        $url = "https://parkcloud.azure-api.net/rest/operator/v1.svc/operator/$operator_id/bookings/events/age/1?key=$api_key";
        // $url = "https://parkcloud.azure-api.net/rest/operator/v1.svc/operators?key=$api_key";

        // Set the HTTP headers
        $headers = [
            'Cache-Control: no-cache', // No caching
            'Ocp-Apim-Subscription-Key: ' . $subscription_key, // Subscription key
        ];

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add custom headers
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if necessary
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for simplicity

        // Execute the cURL request and get the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            // Output the response
            // echo 'Response: ' . $response;
        }

        // Close the cURL session
        curl_close($ch);


        $xml = simplexml_load_string($response);

        // Initialize an empty array to store the event data
        $eventsArray = [];

        // Loop through the events in the XML and convert each to an associative array

        if (isset($xml->Event) and !empty($xml->Event)) {

            foreach ($xml->Event as $event) {
                //     $eventsArray[] = [
                //         'Id' => (string) $event->Id,
                //         'Date' => (string) $event->Date,
                //         'Type' => (string) $event->Type,
                //         'BookingReference' => (string) $event->BookingReference
                //     ];


                $BookingReference = $event->BookingReference;

                $Type = $event->Type;

                $result = parkviaparsing($BookingReference, $Type, $api_key, $subscription_key, $operator_id, $BookingReference, $conn, $producr_code, $airport);

                print_r($result);


            }

        }
    }
}

function parkviaparsing($BookingReference, $Type, $api_key, $subscription_key, $operator_id, $booking_ref, $conn, $producr_code, $airport)
{

    // URL for the API endpoint
    $api_url = "https://parkcloud.azure-api.net/rest/operator/v1.svc/operator/$operator_id/booking/$booking_ref?key=$api_key";

    // Initialize a cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url, // Set the API endpoint URL
        CURLOPT_RETURNTRANSFER => true, // Return the response as a string
        CURLOPT_HTTPHEADER => [
            "Cache-Control: no-cache", // Set the Cache-Control header
            "Ocp-Apim-Subscription-Key: $subscription_key", // Set the subscription key
        ],
    ]);

    // Execute the cURL request
    $response = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        // If an error occurs, display it
        echo "cURL Error: " . curl_error($curl);
    } else {
        // If successful, display the response
        echo "Response from API: " . $response;
    }

    // Close the cURL session
    curl_close($curl);

    if(empty($response))
    {
        
    }else{


    $xml = simplexml_load_string($response);

    // Convert the SimpleXML object to an array
    $booking_info = json_decode(json_encode((array) $xml), true);


    // Register namespaces
    $namespaces = $xml->getNamespaces(true);


    // Get the Vehicle section using the namespace
    $vehicle = $xml->children($namespaces['d2p1']);


    print_r($booking_info);
    // Extract the Model and Colour from the Vehicle section
    echo $model = (string) $vehicle->Model;
    echo $colour = (string) $vehicle->Colour;
    echo $Registration = (string) $vehicle->Registration;


    // Output the array
    // print_r($booking_info);


    // Fetch booking information with default values PC87003409
    echo $Reference = $booking_info["Reference"] ?? '';
    $status = $booking_info["Status"] ?? '';
    $OrderPrice = $booking_info["AmountPaid"] ?? 0;
    $currency = $booking_info["Currency"] ?? '';

    // Fetch customer information with default values
    $customer = $booking_info["Customer"] ?? [];
    $first_name = $customer["FirstName"] ?? '';
    $surname = $customer["Surname"] ?? '';
    $email = $customer["Email"] ?? '';
    $Mobile = $customer["Mobile"] ?? '';

    // Fetch booking dates with default values
    $booking_date = $booking_info["BookingDate"] ?? '';
    $booking_date = str_replace("T", " ", $booking_date);


    $DropOffDateTime = $booking_info["ArrivalDate"] ?? '';
    $DropOffDateTime = str_replace("T", " ", $DropOffDateTime);

    $ReturnDateTime = $booking_info["DepartureDate"] ?? '';
    $ReturnDateTime = str_replace("T", " ", $ReturnDateTime);

    $Passengers = $booking_info["Passengers"] ?? NULL;


    $CustomerFullName = "$first_name  $surname ";


    if (strpos("NEW", $Type) !== false) {
        $status = 1;
    } elseif (strpos("CANCEL", $Type) !== false) {

        $status = 2;
    } elseif (strpos("AMEND", $Type) !== false) {

        $status = 1;
    }
    // else {
    //     $Status = 0;
    // }

    // $producr_code = 'DUBMG';



    $sql_data = "select id,operator_id from tbl_products where product_code='$producr_code'";
    $sql_data_result = $conn->query($sql_data);

    if ($sql_data_result === false) {
        die("Error executing the query: " . $conn->error);
    }
    if ($row = $sql_data_result->fetch_assoc()) {
        // Access the column value using $row['column_name']
        $product_id = $row['id'];
        $operator_id = $row['operator_id'];

    } else {
        echo "No product found";
        exit;
    }



    if (!empty($Reference) and isset($Reference)) {

        $sql_data = "select * from tbl_booking where reference='$Reference'";
        $sql_data_re = $conn->query($sql_data);
        if ($sql_data_re->num_rows > 0) {

            $sql_data = "delete from tbl_booking where reference='$Reference'";
            $sql_data_re = $conn->query($sql_data);
        }

        $OrderDate = date('Y-m-d');

        //    }

        // Build and execute the SQL query
        $sql = "INSERT INTO `tbl_booking` (
            `reference`, `product_id`, `price`, `depart_at`, 
            `return_at`, `email`, `status`, `booked_at`,
            `firstName`, `operator_id`, `booking_type`, `airport`, `contactNumber`, `passenger`, `carModel`, `carColour`, `carReg`,`source`
        ) VALUES (
            '$Reference', '$product_id', '$OrderPrice', '$DropOffDateTime',
            '$ReturnDateTime', '$email', '$status', '$booking_date',
            '$first_name', '$operator_id', 'Online', '$airport', '$Mobile', '$Passengers', '$model', '$colour', '$Registration','ParkVia'
        )";
        print_r($sql);
        if ($conn->query($sql) === FALSE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
            exit;
        }
    }
}//not empty response

}










?>