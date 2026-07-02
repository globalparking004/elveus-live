<?php
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/free2move/email_parsing.py >> /var/www/html/booking/emailparsing/free2move/log.txt

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

// Get the JSON data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if data was received
if ($data) {
    // Process the data
    $count=0;
    foreach ($data as $key => $d) 
    {
        if ($d['name']):
            $name = explode(' ',$d['name']);
            $Reference          =$d['ref'];
            $source             =$d['source'];
            // $CustomerFullName   = $d['name'];
            $CustomerFullName   = $name[0];
            $CustomerSurName   = $name[1];
            $contactNumber      = $d['phone'];

            $OrderPrice         =$d['total'];
            $product_name       ='';
            $DropOffDateTime    =$d['drop_off'];
            $ReturnDateTime     =$d['pickup'];
            $OrderDate          =$d['order_date'];
            $CarReg             = '';
            $carModel           = '';
            $carModel           = '';

            $Status = strtolower($d['status']);
            // $ProductID = "static";

            $DropOffDateTime = date('Y-m-d H:i:s', strtotime($DropOffDateTime));
            $ReturnDateTime = date('Y-m-d H:i:s', strtotime($ReturnDateTime));
            $OrderDate = date('Y-m-d H:i:s', strtotime($OrderDate));

            if (strpos("new", $Status) !== false) {
                $Status = 1;
            } elseif (strpos("cancelled", $Status) !== false) {
    
                $Status = 2;
            }elseif (strpos("booking", $Status) !== false) {
    
                $Status = 1;
            } else {
                $Status = 0;
            }

            if($d['agency'])
            {
                $product_name = $d['agency'];
            }

            if ($product_name) 
            {

                $sql_data = "select id,operator_id from tbl_products where name='$product_name'";
                $sql_data_result = $conn->query($sql_data);

                if ($sql_data_result === false) {
                    die("Error executing the query: " . $conn->error);
                }
                if ($row = $sql_data_result->fetch_assoc()) {
                    // Access the column value using $row['column_name']
                    $id = $row['id'];
                    $operator_id = $row['operator_id'];

                } else {
                    echo "No product found";
                    exit;
                }

            } else {
                echo "no product match";
                exit;
            }
            
            if ($d['vehicle']):
                $vehicle = preg_split('/[\s-]+/',$d['vehicle']);
                // $model = explode("-",$vehicle[1]);
                $CarMake = $vehicle[0];
                $CarModel = $vehicle[1];
                $CarReg = $vehicle[2];
            endif;
        
            // Build and execute the SQL query
                // (
                // reference, product_id, price, depart_at,
                // return_at, carReg, carMake, carModel, status, booked_at,
                // firstName, contactNumber, operator_id, booking_type,airport,source
                // )
            $sql = "INSERT INTO tbl_booking (
                reference, product_id, price, depart_at,
                return_at, carReg, carMake, carModel, status, booked_at,
                firstName, surname, contactNumber, operator_id, booking_type,airport,source
                ) VALUES (
                    '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                    '$ReturnDateTime', '$CarReg', '$CarMake','$CarModel', '$Status', '$OrderDate',
                    '$CustomerFullName', '$CustomerSurName', '$contactNumber', '$contactNumber', '$operator_id', 'Online','BHX','$source'
                )";

            if ($conn->query($sql) === FALSE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
                exit;
            }
        endif;
    }
    // log_message('info', 'Received data: ' . print_r($data, true));

    // Send a response back to the client
    $response = array('status' => 'success', 'message' => 'Data received successfully');
    echo json_encode($response);
} else {
    // Send an error response back to the client
    $response = array('status' => 'error', 'message' => 'No data received');
    echo json_encode($response);
}

// Close the MySQL connection
$conn->close();
?>