<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
// $csvDirectory = '/var/www/html/email_files/dub@ukmails.co.uk/SalesAdministrationdonotreply@holidayextras.com/';
$array = ['/var/www/html/email_files/dub@ukmails.co.uk/SalesAdministrationdonotreply@holidayextras.com/','/var/www/html/email_files/dub@ukmails.co.uk/donotreply@holidayextras.com/'];

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
        // $header = fgetcsv($csvFile);

        // Loop through the remaining rows and insert data into MySQL
        while (($dataa = fgetcsv($csvFile)) !== FALSE) {

            
            $data = preg_split('/\s+/', $dataa[0]);
            print_r($data);
            // Sanitize data before insertion (you may need to customize this based on your requirements)
            $Reference = $conn->real_escape_string($data[2]);
            $title = $conn->real_escape_string($data[4]);
            $CustomerFullName = $conn->real_escape_string($data[3]);
            $CustomerFullName = "$title $CustomerFullName";
            $droptime = $conn->real_escape_string($data[7]);
            $dropdate = $conn->real_escape_string($data[8]);

            $dropdate = str_replace("\\", "", $dropdate); // Remove backslashes
            $dropdate = str_replace("\"", "", $dropdate); // Remove quotes
        
            $day = substr($dropdate, 0, 2);   // "06"
            $month = substr($dropdate, 2, 2); // "07"
            $year = substr($dropdate, 4, 2);  // "25" → 2025;
          
            // Convert 2-digit year to 4-digit year
            $year = ($year < 70) ? "20$year" : "19$year";
            $fullDateStr = "$year-$month-$day";
            // echo'<br>date: ';print_r($fullDateStr);
            
            // $DropOffDateTime = $fullDateStr;
            $DropOffDateTime = "$year-$month-$day $droptime";

            $returntime = $conn->real_escape_string($data[14]);
            $returndate = $conn->real_escape_string($data[13]);

            $returndate = str_replace("\\", "", $returndate); // Remove backslashes
            $returndate = str_replace("\"", "", $returndate); // Remove quotes
            $day1 = substr($returndate, 0, 2);
            $month1 = substr($returndate, 2, 2);
            $year1 = substr($returndate, 4, 2);

            // Convert 2-digit year to 4-digit year
            $year1 = ($year1 < 70) ? "20$year1" : "19$year1";

            // Format the date
            $ReturnDateTime = "$year1-$month1-$day1 $returntime";
            // echo'<br>date: ';print_r($ReturnDateTime);die;

            $Status = $conn->real_escape_string($data[10]);
            $Status = str_replace("*", "", $Status);
            $OrderPrice = $conn->real_escape_string($data[12]);
            $Make = (isset($data[17]))?? $conn->real_escape_string($data[17]);
            $Model = (isset($data[18]))??$conn->real_escape_string($data[18]);
            $Colour = (isset($data[16]))??$conn->real_escape_string($data[16]);
            $CarReg = $conn->real_escape_string($data[14]);
            $ProductID = $conn->real_escape_string($data[1]);

            $Status = strtolower($Status);

            if (strpos("firm", $Status) !== false) {
                $Status = 1;
            } elseif (strpos("canx", $Status) !== false) {
                $Status = 2;
            } elseif (strpos("amnd", $Status) !== false) {
                $Status = 1;
            } else {
                $Status = 0;
            }

            if ($ProductID) {
                $producr_code = 'Dubpr';

                $sql_data = "select id,operator_id from tbl_products where product_code='$producr_code'";
                $sql_data_result = $conn->query($sql_data);

                if ($sql_data_result === false) {
                    die("Error executing the query: " . $conn->error);
                }
                if ($row = $sql_data_result->fetch_assoc()) {
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

            if (!empty($Reference) && isset($Reference)) {
                $sql_data = "select * from tbl_booking where reference='$Reference'";
                $sql_data_re = $conn->query($sql_data);
                if ($sql_data_re->num_rows > 0) {
                    $sql_data = "delete from tbl_booking where reference='$Reference'";
                    $sql_data_re = $conn->query($sql_data);
                }

                $OrderDate = date('Y-m-d h-i-s');

                // Build and execute the SQL query
                $sql = "INSERT INTO tbl_booking (
                    reference, product_id, price, depart_at,
                    return_at, carReg, status, booked_at,
                    carMake, carModel, carColour, firstName, operator_id, booking_type, airport, source
                ) VALUES (
                    '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                    '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate',
                    '$Make', '$Model', '$Colour', '$CustomerFullName', '$operator_id', 'Online', 'DUB','Holiday Extras'
                )";
                print_r($sql);
                if ($conn->query($sql) === FALSE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    exit;
                }
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
        echo "***no file Found***";
        //exit;
    }
    // if (empty($csvFiles)) {
    //     echo "No file found";
    //     exit;
    // }

    foreach ($csvFiles as $csvFilePath) {
        // Create a table name based on the file name (you may need to customize this)
        $tableName = 'bookings';
        // Call the function to upload and insert data
        uploadAndInsertCSV($csvFilePath, $tableName, $conn);
    }
}

// Close the MySQL connection
$conn->close();
?>
