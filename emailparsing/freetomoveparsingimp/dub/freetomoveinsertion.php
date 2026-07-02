<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
// $csvDirectory = '/var/www/html/email_files/bhx/CompareParkingDealsnoreply@compareparkingdeals.co.uk/';
$array = ['/var/www/html/email_files/freeeetomove/dub/'];

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle CSV file upload and data insertion
function uploadAndInsertCSV($csvFilePath, $tableName, $conn)
{
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
            // exit;

            $Reference = $conn->real_escape_string($data[0]);
            $Reference = str_replace(['£', ':'], '', $Reference); // Remove £ and :
          echo  $Reference = trim($Reference); // Remove spaces from start and end

            $CustomerFullName = $conn->real_escape_string($data[1]);
            $CustomerFullName = str_replace(['£', ':'], '', $CustomerFullName); // Remove £ and :
            $CustomerFullName = trim($CustomerFullName); // Remove spaces from start and end

            $ContactNumber = $conn->real_escape_string($data[2]);
            $ContactNumber = str_replace(['£', ':'], '', $ContactNumber); // Remove £ and :
            $ContactNumber = trim($ContactNumber); // Remove spaces from start and end


            $carvalues = $conn->real_escape_string($data[3]);
            $carvalues = str_replace(['£', ':','(',')'], '', $carvalues); // Remove £ and :
            $carvalues = trim($carvalues); // Remove spaces from start and end

            $array = explode(" ", $carvalues);


            // print_r($array);
            // exit;

            // $decided_value = isset($array[0]) && !empty($array[0]) ? $array[0] : '';
            // $CarReg = isset($array[1]) && !empty($array[1]) ? $array[1] : '';

            // $array1 = explode(" ", $decided_value);
            $Make = isset($array[0]) && !empty($array[0]) ? $array[0] : '';
            $Model = isset($array[1]) && !empty($array[1]) ? $array[1] : '';
            $CarReg = isset($array[2]) && !empty($array[2]) ? $array[2] : '';
            $CarReg = isset($array[3]) && !empty($array[3]) ? $array[3] : $array[2];




            $OrderPrice = $conn->real_escape_string($data[4]);
            $OrderPrice = str_replace([':','£'], '', $OrderPrice); // Remove £ and :
            $OrderPrice = trim($OrderPrice); // Remove spaces from start and end

            $DropOffDateTime = $data[5];
            $DropOffDateTime = str_replace(['£', ','], '', $DropOffDateTime); // Remove £ and :
            $DropOffDateTime = trim($DropOffDateTime); // Remove spaces from start and end
            $DropOffDateTime = date("Y-m-d H:i:s", strtotime($DropOffDateTime));


            $DepartureTerminal = $data[6];
            $DepartureTerminal = str_replace([','], '', $DepartureTerminal); // Remove £ and :
            $DepartureTerminal = trim($DepartureTerminal); // Remove spaces from start and end
            $DepartureTerminal = date("Y-m-d H:i:s", strtotime($DepartureTerminal));

            $Status = $conn->real_escape_string($data[7]);
            $Status = trim($Status); // Remove spaces from start and end


            // $ProductName = $conn->real_escape_string($data[2]);         
            // $SupplierCost = $conn->real_escape_string($data[19]);


            $OrderDate = date("Y-m-d H:i:s");
         
            $Status = strtolower($Status);

            if (strpos("active", $Status) !== false) {
                $Status = 1;
            } elseif (strpos("cancell", $Status) !== false) {
                $Status = 2;
            } else {
                $Status = 0;
            }

            $producr_code = 'DUBPR';
            $sql_data = "select id,operator_id from tbl_products where product_code='$producr_code'";
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


            if (!empty($Reference) and isset($Reference)) {

                $sql_data = "select * from tbl_booking where reference='$Reference'";
                $sql_data_re = $conn->query($sql_data);

                if ($sql_data_re->num_rows > 0) {

                    if ($row2 = $sql_data_re->fetch_assoc()) {

                        $OrderPrice = $row2['price'];
                    }

                    if (strpos("cancell", $Status) !== false){

                        
                        $sql_data = "update tbl_booking set status=2 where reference='$Reference'";
                        $sql_data_re = $conn->query($sql_data);
                        continue;

                    }else{

                        $sql_data = "delete from tbl_booking where reference='$Reference'";
                        $sql_data_re = $conn->query($sql_data);
                    }
                  
                }


            }

            // Build and execute the SQL query
            $sql = "INSERT INTO tbl_booking (
                reference, product_id, price, depart_at, return_at, 
                carReg, status, booked_at, carMake, carModel, 
                contactNumber, firstName, operator_id, booking_type, airport, source
            ) VALUES (
                '$Reference', '$id', '$OrderPrice', '$DropOffDateTime', 
                '$DepartureTerminal', '$CarReg', '$Status', '$OrderDate', 
                '$Make', '$Model', '$ContactNumber', 
                '$CustomerFullName', '$operator_id', 'Online', 'DUB', 'FreeToMove'
            )";
            if ($conn->query($sql) === FALSE) {
                echo "Error: " . $sql . "<br>" . $conn->error;
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

foreach ($array as $gohar) {

    // Process all CSV files in the directory
    $csvFiles = glob($gohar . '*.csv');
    //print_r($csvFiles);
    if (empty($csvFiles[0])) {
        echo "no file Found";
        //exit;
    }

    foreach ($csvFiles as $csvFilePath) {
        // Extract file name
        $csvFileName = basename($csvFilePath, '.csv');

        // Create a table name based on the file name (you may need to customize this)
        $tableName = 'bookings';

        // Call the function to upload and insert data
        uploadAndInsertCSV($csvFilePath, $tableName, $conn);
    }

    // Close the MySQL connection

}

// Close the MySQL connection
$conn->close();
?>