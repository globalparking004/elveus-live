<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
$csvDirectory = '/var/www/html/email_files/mgman@ukmails.co.uk/Parking4Younoreply@parking4you.co.uk/';

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
            $ProductID = $conn->real_escape_string($data[3]);
            $Reference = $conn->real_escape_string($data[4]);
            $Status = $conn->real_escape_string($data[5]);
            $CustomerFullName = $conn->real_escape_string($data[6]);
            $DropOffDateTime = $conn->real_escape_string($data[7]);
            $ReturnDateTime = $conn->real_escape_string($data[8]);
            $DepartureTerminal = $conn->real_escape_string($data[10]);
            $ReturnTerminal = $conn->real_escape_string($data[9]);
            $DepartureFlightNumber = $conn->real_escape_string($data[11]);
            $ReturnFlightNumber = $conn->real_escape_string($data[12]);
            $CarReg = $conn->real_escape_string($data[13]);
            $Make = $conn->real_escape_string($data[14]);
            $Model = $conn->real_escape_string($data[15]);
            $Colour = $conn->real_escape_string($data[16]);
            $NoOfPassengers = $conn->real_escape_string($data[17]);
            $ContactNumber = $conn->real_escape_string($data[18]);
            $OrderPrice = $conn->real_escape_string($data[20]);



            // $ProductName = $conn->real_escape_string($data[2]);         
            // $SupplierCost = $conn->real_escape_string($data[19]);


            $OrderDate = date("Y-m-d H:i:s");
            $DropOffDateTime = date("Y-m-d H:i:s", strtotime($DropOffDateTime));
            $ReturnDateTime = date("Y-m-d H:i:s", strtotime($ReturnDateTime));

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
            

           
           if($ProductID)
           {

            if($ProductID=="180")
            {
                $producr_code='STNMG';

            }elseif($ProductID=="60")
            {
                $producr_code='MANMG';
            }
            // elseif($ProductID=="APU54")
            // {
            //     $producr_code='BHXPR';
            // }
            else{
                echo "no product match";
                exit;

            }

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
        
            // Build and execute the SQL query
            $sql = "INSERT INTO tbl_booking (
                reference, product_id, price, depart_at,
                return_at, carReg, status, booked_at, InFltNo,
                OutTerminal, OutFltNo, RetTerminal, carMake,
                carModel, carColour, contactNumber, passenger, firstName,operator_id,booking_type,airport,source
            ) VALUES (
                '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate', '$DepartureFlightNumber',
                '$DepartureTerminal', '$ReturnFlightNumber', '$ReturnTerminal', '$Make',
                '$Model', '$Colour', '$ContactNumber', '$NoOfPassengers', '$CustomerFullName', '$operator_id','Online','MAN','P4U'
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

// Process all CSV files in the directory
$csvFiles = glob($csvDirectory . '*.csv');
// print_r($csvFiles);
if(empty($csvFiles[0]))
{
    echo "no file Found";
    exit;
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
$conn->close();
?>
