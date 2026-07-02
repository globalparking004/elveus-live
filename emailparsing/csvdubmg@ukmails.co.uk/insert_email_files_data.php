<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = 'N3S3QKCc4vRUKWQV';
$database = 'bookings';

// Directory containing CSV files
$csvDirectory = '/var/www/html/email_files/dubmg@ukmails.co.uk/SalesAdministrationdonotreply@holidayextras.com/';

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
        // $dataa = fgetcsv($csvFile);

        while (($dataa = fgetcsv($csvFile)) !== FALSE) {

            $data = preg_split('/\s+/', $dataa[0]);


            // print_r($data);

            // print_r($data);
            // Loop through the remaining rows and insert data into MySQL
            // while (($data = fgetcsv($csvFile)) !== FALSE) {

            $check1 = $conn->real_escape_string($data[11]);

            $check1 = str_replace("*", "", $check1);
            $check1 = strtolower($check1);

            $check2 = $conn->real_escape_string($data[9]);

            $check2 = str_replace("*", "", $check2);
            $check2 = strtolower($check2);


            $array = array('firm', 'canx', 'amnd');

            if (in_array($check1, $array)) {


                $Reference = $conn->real_escape_string($data[2]);
                $title = $conn->real_escape_string($data[5]);


                $CustomerFullName = $conn->real_escape_string($data[6]);

                $CustomerFullName = "$title $CustomerFullName";

                $droptime = $conn->real_escape_string($data[8]);
                $dropdate = $conn->real_escape_string($data[9]);

                $dropdate = str_replace("\\", "", $dropdate); // Remove backslashes
                $dropdate = str_replace("\"", "", $dropdate); // Remove quotes
                $day = substr($dropdate, 0, 2);
                $month = substr($dropdate, 2, 2);
                $year = substr($dropdate, 4, 2);

                // Convert 2-digit year to 4-digit year
                $year = ($year < 70) ? "20$year" : "19$year";

                // Format the date
                $DropOffDateTime = "$year-$month-$day $droptime";


                $returntime = $conn->real_escape_string($data[15]);
                $returndate = $conn->real_escape_string($data[14]);

                $returndate = str_replace("\\", "", $returndate); // Remove backslashes
                $returndate = str_replace("\"", "", $returndate); // Remove quotes
                $day1 = substr($returndate, 0, 2);
                $month1 = substr($returndate, 2, 2);
                $year1 = substr($returndate, 4, 2);

                // Convert 2-digit year to 4-digit year
                $year1 = ($year1 < 70) ? "20$year1" : "19$year1";

                // Format the date
                $ReturnDateTime = "$year1-$month1-$day1 $returntime";



                $Status = $conn->real_escape_string($data[11]);

                $Status = str_replace("*", "", $Status);


                $OrderPrice = $conn->real_escape_string($data[12]);

                $CarReg = $conn->real_escape_string($data[16]);
                $ProductID = "static";
                $Flight Number = $conn->real_escape_string($data[18]);







                $Status = strtolower($Status);

                if (strpos("firm", $Status) !== false) {
                    $Status = 1;
                } elseif (strpos("canx", $Status) !== false) {
        
                    $Status = 2;
                }elseif (strpos("amnd", $Status) !== false) {
        
                    $Status = 1;
                } else {
                    $Status = 0;
                }




                if ($ProductID) {

                    // if ($ProductID == "APU53") {
                    //     $producr_code = 'BHXMGValet';

                    // } elseif ($ProductID == "APU54") {
                    //     $producr_code = 'BHXPR';
                    // } else {
                    //     echo "no product match";
                    //     exit;

                    // }
                    $producr_code = 'DUBMG';



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

                } else {
                    echo "no product match";
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
                    $sql = "INSERT INTO tbl_booking (
                        reference, product_id, price, depart_at,
                        return_at, carReg, status, booked_at,
                         firstName, operator_id, booking_type,airport,RetTerminal
                    ) VALUES (
                        '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                        '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate',
                        '$CustomerFullName', '$operator_id', 'Online','DUB','$Flight Number'
                    )";

                    if ($conn->query($sql) === FALSE) {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit;
                    }
                }


            } elseif (in_array($check2, $array)) {

                $Reference = $conn->real_escape_string($data[2]);
                $title = $conn->real_escape_string($data[4]);
    
    
                $CustomerFullName = $conn->real_escape_string($data[3]);
    
                $CustomerFullName = "$title $CustomerFullName";
    
                $droptime = $conn->real_escape_string($data[6]);
                $dropdate = $conn->real_escape_string($data[7]);
    
                $dropdate = str_replace("\\", "", $dropdate); // Remove backslashes
                $dropdate = str_replace("\"", "", $dropdate); // Remove quotes
                $day = substr($dropdate, 0, 2);
                $month = substr($dropdate, 2, 2);
                $year = substr($dropdate, 4, 2);
    
                // Convert 2-digit year to 4-digit year
                $year = ($year < 70) ? "20$year" : "19$year";
    
                // Format the date
                $DropOffDateTime = "$year-$month-$day $droptime";
    
    
                $returntime = $conn->real_escape_string($data[13]);
                $returndate = $conn->real_escape_string($data[12]);
    
                $returndate = str_replace("\\", "", $returndate); // Remove backslashes
                $returndate = str_replace("\"", "", $returndate); // Remove quotes
                $day1 = substr($returndate, 0, 2);
                $month1 = substr($returndate, 2, 2);
                $year1 = substr($returndate, 4, 2);
    
                // Convert 2-digit year to 4-digit year
                $year1 = ($year1 < 70) ? "20$year1" : "19$year1";
    
                // Format the date
                $ReturnDateTime = "$year1-$month1-$day1 $returntime";
    
    
    
                $Status = $conn->real_escape_string($data[9]);
    
                $Status = str_replace("*", "", $Status);
    
    
                $OrderPrice = $conn->real_escape_string($data[10]);
                $CarReg = $conn->real_escape_string($data[14]);

                $Flight Number = $conn->real_escape_string($data[16]);

             
                $ProductID = "static";
    
    
    
    
    
    
    
                $Status = strtolower($Status);
    
                if (strpos("firm", $Status) !== false) {
                    $Status = 1;
                } elseif (strpos("canx", $Status) !== false) {
        
                    $Status = 2;
                }elseif (strpos("amnd", $Status) !== false) {
        
                    $Status = 1;
                } else {
                    $Status = 0;
                }
    
    
    
                if ($ProductID) {
    
                    // if ($ProductID == "APU53") {
                    //     $producr_code = 'BHXMGValet';
    
                    // } elseif ($ProductID == "APU54") {
                    //     $producr_code = 'BHXPR';
                    // } else {
                    //     echo "no product match";
                    //     exit;
    
                    // }
                    $producr_code = 'DUBMG';
    
    
    
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
    
                } else {
                    echo "no product match";
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
                    $sql = "INSERT INTO tbl_booking (
                    reference, product_id, price, depart_at,
                    return_at, carReg, status, booked_at,
                    firstName, operator_id, booking_type,airport,RetTerminal
                ) VALUES (
                    '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                    '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate',
                     '$CustomerFullName', '$operator_id', 'Online','DUB','$Flight Number'
                )";
    
                    if ($conn->query($sql) === FALSE) {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit;
                    }
                }






            } else {
               

            $Reference = $conn->real_escape_string($data[2]);
            $title = $conn->real_escape_string($data[4]);


            $CustomerFullName = $conn->real_escape_string($data[3]);

            $CustomerFullName = "$title $CustomerFullName";

            $droptime = $conn->real_escape_string($data[7]);
            $dropdate = $conn->real_escape_string($data[8]);

            $dropdate = str_replace("\\", "", $dropdate); // Remove backslashes
            $dropdate = str_replace("\"", "", $dropdate); // Remove quotes
            $day = substr($dropdate, 0, 2);
            $month = substr($dropdate, 2, 2);
            $year = substr($dropdate, 4, 2);

            // Convert 2-digit year to 4-digit year
            $year = ($year < 70) ? "20$year" : "19$year";

            // Format the date
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



            $Status = $conn->real_escape_string($data[10]);

            $Status = str_replace("*", "", $Status);


            $OrderPrice = $conn->real_escape_string($data[12]);


            $Make = $conn->real_escape_string($data[18]);
            $Model = $conn->real_escape_string($data[19]);
            $Colour = $conn->real_escape_string($data[17]);

            $CarReg = $conn->real_escape_string($data[15]);
            $ProductID = "static";







            $Status = strtolower($Status);

            if (strpos("firm", $Status) !== false) {
                $Status = 1;
            } elseif (strpos("canx", $Status) !== false) {
    
                $Status = 2;
            }elseif (strpos("amnd", $Status) !== false) {
    
                $Status = 1;
            } else {
                $Status = 0;
            }




            if ($ProductID) {

                // if ($ProductID == "APU53") {
                //     $producr_code = 'BHXMGValet';

                // } elseif ($ProductID == "APU54") {
                //     $producr_code = 'BHXPR';
                // } else {
                //     echo "no product match";
                //     exit;

                // }
                $producr_code = 'DUBMG';



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

            } else {
                echo "no product match";
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
                $sql = "INSERT INTO tbl_booking (
                reference, product_id, price, depart_at,
                return_at, carReg, status, booked_at,
                carMake,
                carModel, carColour, firstName, operator_id, booking_type,airport
            ) VALUES (
                '$Reference', '$id', '$OrderPrice', '$DropOffDateTime',
                '$ReturnDateTime', '$CarReg', '$Status', '$OrderDate',
                '$Make',
                '$Model', '$Colour', '$CustomerFullName', '$operator_id', 'Online','DUB'
            )";

                if ($conn->query($sql) === FALSE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    exit;
                }
            }


        }//else of checks of multiformat

        }//forloop
        // Close the CSV file
        fclose($csvFile);
        unlink($csvFilePath);
//     } else {
//         echo "Error opening CSV file";
    }
}

// Process all CSV files in the directory
$csvFiles = glob($csvDirectory . '*.csv');
// print_r($csvFiles);
if (empty($csvFiles[0])) {
    echo "no file Found";
    exit;
}

foreach ($csvFiles as $csvFilePath) {
    // Extract file name
    $csvFileName = basename($csvFilePath, '.csv');

    // Create a table name based on the file name (you may need to customize this)
    $tableName = 'bookings';
    // print_r($csvFilePath);
    // Call the function to upload and insert data
    $dara = uploadAndInsertCSV($csvFilePath, $tableName, $conn);
    print_r($dara);
}

// Close the MySQL connection
$conn->close();
?>