<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
// $csvDirectory = '/var/www/html/email_files/bhx/CTAPBookingsbookings@comparetheairportparking.com/';
$array = ['/var/www/html/email_files/sop/CompareYourParkingnoreply@compareyourparking.co.uk/','/var/www/html/email_files/sop/CompareTheParkingnoreply@comparetheparking.co.uk/'];

// Connect to MySQL 
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Function to handle CSV file upload and data insertion
function uploadAndInsertCSV($csvFilePath, $tableName, $conn) {
    // Read CSV file
    $csvFile = fopen($csvFilePath, 'r');

    // Check if the file was opened successfully
    if ($csvFile !== FALSE) {
        // Skip the header row
        fgetcsv($csvFile);

        // Loop through the remaining rows and insert data into MySQL
        while (($data = fgetcsv($csvFile)) !== FALSE) {
            // Sanitize data before insertion (you may need to customize this based on your requirements)
            // Assuming $data is an array containing the CSV data
            // print_r($data);
            $Reference = $conn->real_escape_string($data[4]);
            $afterDash = preg_replace('/^.*-/', '', $Reference);
            $Reference2 = preg_replace('/^[A-Za-z]+/', '', $afterDash);
            $ProductName = $conn->real_escape_string($data[2]);
            $CustomerFullName = $conn->real_escape_string($data[6]);
            $ContactNumber = $conn->real_escape_string($data[18]);
            $OrderPrice = $conn->real_escape_string($data[20]);
            $DepartureDate = $conn->real_escape_string($data[7]);
            $ReturnDate = $conn->real_escape_string($data[8]);

            $OrderDate =date("Y-m-d H:i:s");

            $CarReg = $conn->real_escape_string($data[13]);
            $Make = $conn->real_escape_string($data[14]);
            $Model = $conn->real_escape_string($data[15]);
            $Colour = $conn->real_escape_string($data[16]);
           
            $DepartureTerminal = (isset($data[9]))?$conn->real_escape_string($data[9]):'';
            $ReturnTerminal = (isset($data[10]))?$conn->real_escape_string($data[10]):'';
            $DepartureFlightNumber = (isset($data[11]))?$conn->real_escape_string($data[11]):'';
            $ReturnFlightNumber = (isset($data[12]))?$conn->real_escape_string($data[12]):'';

            // $Make = $conn->real_escape_string($data[14]);

            $NoOfPassengers = $conn->real_escape_string($data[17]);

            $DropOffDateTime= date('Y-m-d H:i:s',strtotime($DepartureDate));
            $ReturnDateTime= date('Y-m-d H:i:s',strtotime($ReturnDate));

            $Status = "completed";

            $Status = strtolower($Status);

            if (strpos("completed", $Status) !== false) {
                $Status=1;
            } elseif(strpos("cancelled", $Status) !== false) {
                $Status=2;
            } elseif(strpos("amended", $Status) !== false) {
                $Status=1;
            } else{
                $Status=0;
            }
            
           // echo 'CYP-Order_date: '.$OrderDate;
            if($ProductName)
            {
                $producr_code='';
                if($ProductName)
                {
                    $producr_code='SOUTHPR'; 

                }
                else{
                    echo $ProductName." CYP no product match. Order Date:".$OrderDate;
                    // exit;
                }
                if ($producr_code) 
                {
                   
                    $sql_data="select id,operator_id from tbl_products where product_code='$producr_code'";
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
                }

            }else{
                echo "no product match";
                exit;
            }

            if(!empty($Reference) and isset($Reference)){

                $sql_data="select * from tbl_booking where reference='$Reference'";
                $sql_data_re = $conn->query($sql_data);
                if ($sql_data_re->num_rows > 0) {

                    $sql_data="delete from tbl_booking where reference='$Reference'";
                    $sql_data_re = $conn->query($sql_data);
                }    
            }
            if ($producr_code) {
                // Build and execute the SQL query
                $sql = "INSERT INTO tbl_booking (
                    reference, reference2, product_id, price, depart_at,
                    return_at, carReg, status, booked_at, InFltNo,
                    OutTerminal, OutFltNo, RetTerminal, carMake,
                    carModel, carColour, contactNumber, passenger, firstName,operator_id,booking_type,airport,source
                ) VALUES (
                    '$Reference', '$Reference2', '$id', '$OrderPrice', '$DropOffDateTime',
                    '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate', '$DepartureFlightNumber',
                    '$DepartureTerminal', '$ReturnFlightNumber', '$ReturnTerminal', '',
                    '$Model', '$Colour', '$ContactNumber', '$NoOfPassengers', '$CustomerFullName', '$operator_id','Online','SOP','CYP'
                )";
                print_r($sql);
                if ($conn->query($sql) === FALSE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    exit;
                }
            }else{
                echo "Error: productName: " .  $ProductName;
                exit;
            }
                
            
        }

        // Close the CSV file
        fclose($csvFile);
        unlink($csvFilePath);
    } else {
        echo "Error opening CSV file";
    }
}

foreach($array as $ar)
{

    // Process all CSV files in the directory
    $csvFiles = glob($ar . '*.csv');
   
    if(empty($csvFiles[0]))
    {
        echo "***CYP no file Found***";
        //exit;
    }

    foreach ($csvFiles as $csvFilePath) {
        // Extract file name
        $csvFileName = basename($csvFilePath, '.csv');

        // Create a table name based on the file name (you may need to customize this)
        $tableName = 'bookings';
         // print_r($csvFilePath);
        // Call the function to upload and insert data
        uploadAndInsertCSV($csvFilePath, $tableName, $conn);
    }
}

// Close the MySQL connection
$conn->close();
?>
