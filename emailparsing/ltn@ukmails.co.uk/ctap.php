<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
$array = ['/var/www/html/email_files/ltn/bookings@comparetheairportparking.com/','/var/www/html/email_files/ltn/CTAPBookingsbookings@comparetheairportparking.com/'];

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
            print_r($data);
            $Reference = $conn->real_escape_string($data[0]);
            $ProductName = $conn->real_escape_string($data[2]);
            $CustomerFirstName = $conn->real_escape_string($data[3]);
            $CustomerLastName = $conn->real_escape_string($data[4]);
            $ContactNumber = $conn->real_escape_string($data[5]);
            $OrderPrice = $conn->real_escape_string($data[6]);
            $DepartureDate = $conn->real_escape_string($data[7]);
            $DropTime = $conn->real_escape_string($data[8]);
            $ReturnDate = $conn->real_escape_string($data[9]);
            $ReturnTime = $conn->real_escape_string($data[10]);

            $OrderDate1 = $conn->real_escape_string($data[11]);
            $OrderDate = formatDate($OrderDate1);

            // $ProductID = $conn->real_escape_string($data[3]);
            // $Status = $conn->real_escape_string($data[5]); 
            

            $CustomerFullName = $CustomerFirstName.' '.$CustomerLastName;

            $Model = $conn->real_escape_string($data[12]);
            $CarReg = $conn->real_escape_string($data[13]);
            $Colour = $conn->real_escape_string($data[14]);
            $DepartureFlightNumber = (isset($data[15]))?$conn->real_escape_string($data[15]):'';
            $DepartureTerminal = (isset($data[16]))?$conn->real_escape_string($data[16]):'';
            $ReturnTerminal = (isset($data[17]))?$conn->real_escape_string($data[17]):'';
            $ReturnFlightNumber = '';

            // $Make = $conn->real_escape_string($data[14]);

            // $NoOfPassengers = $conn->real_escape_string($data[17]);
            // $ContactNumber = $conn->real_escape_string($data[18]);
            // $OrderPrice = $conn->real_escape_string($data[20]);

        
            // $SupplierCost = $conn->real_escape_string($data[19]);
            // $OrderDate = date("Y-m-d H:i:s");
            $departDate= $DepartureDate.' '.$DropTime;
            $returnDate= $ReturnDate.' '.$ReturnTime;

            $DropOffDateTime = formatDate($departDate);
            $ReturnDateTime = formatDate($returnDate);

            // list($day, $month, $year) = sscanf($DepartureDate, '%d/%d/%d');
            // $DropOffDateTime = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$DropTime;

            // list($day, $month, $year) = sscanf($ReturnDate, '%d/%d/%d');
            // $ReturnDateTime = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$ReturnTime;

            // list($month, $day, $year, $hour, $minute, $second) = sscanf($OrderDate1, '%d/%d/%d %d:%d:%d');
            // $OrderDate = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$hour.':'.$minute.':'.$second;

            // echo '**********Daprt: '.$DropOffDateTime.' ReturnDate: '.$ReturnDateTime;
            // echo '****************order: '.$OrderDate;die;

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
            
           if($ProductName)
           {
                $producr_code='';
                if(isMeetAndGreet($ProductName) 
                    || $ProductName == 'Luton Valet Parking' 
                    || $ProductName=='luton Valet Parking'
                    || $ProductName=='Luton Airport Covered Parking')
                {
                    $producr_code='LTNMG';

                }elseif(isParkAndRide($ProductName) || $ProductName == 'Greenway Park n ride')
                {
                    $producr_code='LTNPR';
                }else{
                    echo $ProductName." no product match.".$Reference;
                    // exit;
                }
                // echo 'producr_code: '.$producr_code;
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
                    reference, product_id, price, depart_at,
                    return_at, carReg, status, booked_at, InFltNo,
                    OutTerminal, OutFltNo, RetTerminal, carMake,
                    carModel, carColour, contactNumber, passenger, firstName,operator_id,booking_type,airport,source
                ) VALUES (
                    '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                    '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate', '$DepartureFlightNumber',
                    '$DepartureTerminal', '$ReturnFlightNumber', '$ReturnTerminal', '',
                    '$Model', '$Colour', '$ContactNumber', 1, '$CustomerFullName', '$operator_id','Online','LTN','CTAP'
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

function isMeetAndGreet($productName) {
    $normalized = strtolower(trim($productName));
    $normalized = str_replace('&', 'and', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized); // Replace multiple spaces

    // Check for "meet and greet" anywhere in string
    return preg_match('/\bmeet\s+and\s+greet\b/', $normalized);
}

function isParkAndRide($productName) {
    $normalized = strtolower(trim($productName));
    $normalized = str_replace('&', 'and', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized); // Replace multiple spaces

    // Check for "park and ride" anywhere in string
    return preg_match('/\bpark\s+and\s+ride\b/', $normalized);
}

function formatDate($dateString)
{
     // Try to parse the date in "day/month/year" format
    $date = DateTime::createFromFormat('d/m/Y H:i', $dateString);
    
    // If parsing fails, try to parse the date in "month/day/year" format
    if (!$date) {
        $date = DateTime::createFromFormat('m/d/Y H:i', $dateString);
        if (!$date) {
            $date = DateTime::createFromFormat('m/d/Y H:i A', $dateString);
        }
    }

    // If the date is successfully parsed, format it to "day/month/year h:i:s"
    if ($date) {
        return $date->format('Y-m-d H:i');
    } else{
        return formatDate1($dateString);
    }
}
function formatDate1($dateString) {
    // Try to parse the date in "day/month/year" format
    $date = DateTime::createFromFormat('d/m/Y H:i:s', $dateString);
    // $formattedDate = date("Y-m-d H:i:s", strtotime($dateString));
    // if ($formattedDate) {
    //     return $formattedDate;
    // }
    
    // If parsing fails, try to parse the date in "month/day/year" format
    if (!$date) {
        $date = DateTime::createFromFormat('m/d/Y H:i:s', $dateString);
        if (!$date) {
            $date = DateTime::createFromFormat('m/d/Y H:i:s A', $dateString);
        }
    }

    // If the date is successfully parsed, format it to "day/month/year h:i:s"
    if ($date) {
        return $date->format('Y-m-d H:i:s');
    } else{
        return "Invalid date format";
    }
}
// function detectDateFormat($dateString) {
//     // Split date and time
//     [$datePart, $timePart] = explode(' ', $dateString);
//     [$part1, $part2, $year] = explode('/', $datePart);

//     // Check if first or second part is greater than 12
//     if ((int)$part1 > 12) {
//         $format = 'd/m/Y H:i:s'; // day/month/year
//         list($day, $month, $year, $hour, $minute, $second) = sscanf($dateString, '%d/%d/%d %d:%d:%d');
//         $OrderDate = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$hour.':'.$minute.':'.$second;
//     } elseif ((int)$part2 > 12) {
//         $format = 'm/d/Y H:i:s'; // month/day/year
//         list($month, $day, $year, $hour, $minute, $second) = sscanf($dateString, '%d/%d/%d %d:%d:%d');
//         $OrderDate = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$hour.':'.$minute.':'.$second;
//     } else {
//         // Ambiguous — default to d/m/Y or handle as needed
//         $format = 'd/m/Y H:i:s';
//         list($day, $month, $year, $hour, $minute, $second) = sscanf($dateString, '%d/%d/%d %d:%d:%d');
//         $OrderDate = sprintf('%04d-%s-%02d', $year, date('m', mktime(0, 0, 0, $month, 10)), $day).' '.$hour.':'.$minute.':'.$second;
//     }

//     return $OrderDate;
// }


foreach($array as $arr)
{

    // Process all CSV files in the directory
    $csvFiles = glob($arr . '*.csv');
    // print_r($csvFiles);
    if(empty($csvFiles[0]))
    {
        echo "***CTAP no file Found***";
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
