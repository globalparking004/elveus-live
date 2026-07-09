<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel; 
use CodeIgniter\API\ResponseTrait;
use ValueError;
use DateTime; 

// header("Access-Control-Allow-Origin: https://admin.goairportparking.com/");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");

class ApiController extends BaseController
{
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    public function __construct()
    {
        $this->Users = new UsersModel;
        $this->Roles = new RolesModel;
    }


    public function test_limit_date()
    {
        $inputString = "0330";
        $timeFormat = $this->convertToTimeFormat($inputString);


        $providedDateTime = "2023-12-14 $timeFormat";
        $timeDifference = $this->getTimeDifference($providedDateTime);


        $timeDifference = $timeDifference['hours'];
        print_r($timeDifference);

    }

    // function convertToTimeFormat($input)
    // {
    //     // Extract hours and minutes from the input string
    //     $hours = substr($input, 0, 2);
    //     $minutes = substr($input, 2, 2);

    //     // Create a time string in "HH:MM" format
    //     $timeString = $hours . ":" . $minutes . ":00";

    //     return $timeString;
    // }

    function convertToTimeFormat($input) {
        $input = trim((string)$input);
        if (!preg_match('/^\d{4}$/', $input)) {
            throw new Exception("Invalid time input (expected HHMM): $input");
        }
        $hours = substr($input, 0, 2);
        $minutes = substr($input, 2, 2);
        return sprintf('%02d:%02d:00', (int)$hours, (int)$minutes);
    }


    public function getTimeDifference($providedDateTime)
    {
        // Get the timestamp for the provided date and time
        $providedTimestamp = strtotime($providedDateTime);

        // Get the current timestamp
        $currentTimestamp = time();

        // Calculate the time difference in seconds
        $differenceInSeconds = $providedTimestamp - $currentTimestamp;

        // Calculate hours and minutes
        $hours = floor($differenceInSeconds / 3600);
        $minutes = floor(($differenceInSeconds % 3600) / 60);

        // Return the difference in hours and minutes
        return ["hours" => $hours, "minutes" => $minutes];
    }

    public function getTimeDifference2($startDateTime, $endDateTime)
    {
        $startTimestamp = strtotime($startDateTime);
        $endTimestamp   = strtotime($endDateTime);

        $differenceInSeconds = $endTimestamp - $startTimestamp;

        $abs = abs($differenceInSeconds);
        $hours = intdiv($abs, 3600);
        $minutes = intdiv($abs % 3600, 60);

        return ["hours" => $hours, "minutes" => $minutes];
    }

    public function sagepay()
    {
        $sql_email_data = "select * from tbl_booking where transaction_id='8F310C32-A0C1-B683-2B72-0BB53CE8BA9F'";
        $sql_email_data_r = $this->db->query($sql_email_data)->getRow();
        echo $book_id = $sql_email_data_r->id;

        echo $email = $sql_email_data_r->email;
        $from = "no_reply@parkingmanagment.com";

        $webtype = $_GET['webtype'];

        // $res=send_email($email, "Your Parking Booking Confirmation", $book_id,$from,$webtype);
    }

    public function get_single_booking()
    {
        $accessToken = $this->request->getGet('access_token');
        
        // Validate access token
        if ($accessToken !== '5MEsB9lLwVqu4qndXvEUE428bqGZY') {
            return $this->response->setJSON([
                'status' => false, 
                'message' => "Access token is invalid"
            ]);
        }
        $search = $this->request->getGet('search');
        // Validate search
        if (empty($search)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => "Search params is required"
            ]);
        }
        $cleanSearch = str_replace(['-', ' '], '', $search);

        $sql = "
            SELECT 
                *,
                REPLACE(REPLACE(carReg, '-', ''), ' ', '') AS carReg,
                REPLACE(REPLACE(carMake, '-', ''), ' ', '') AS carMake
            FROM tbl_booking
            WHERE status = 1
            AND (
                reference LIKE '%$search%'
                OR REPLACE(REPLACE(carReg, '-', ''), ' ', '') LIKE '%$cleanSearch%'
                OR contactNumber LIKE '%$search%'
            )
            ORDER BY id DESC
            ";

        $result = $this->db->query($sql)->getResult();
        if ($result) {
           // if ($result->status == 1) {
                $response = ['status' => true, "data" => $result];
            // }else{
            //     $status = ($result->status == 0)? 'Pending': 'Cancelled';
            //     $response = ['status' => true, 'message'=> 'Booking status is '.$status, "data" => $result];
            // }
        }else{
             $response = ['status' => false, "message" => "Record not found"];
        }
                
       
        
        // echo json_encode($response);
        return $this->response->setJSON($response);
    }

    public function get_product()
    {
        $airport = $this->request->getGet('airport');
        $product_code = $this->request->getGet('product_code');
        if ($product_code) 
        {
            $sql = "SELECT id,product_code, name, operator_id FROM tbl_products WHERE product_code='$product_code'";
            $result = $this->db->query($sql)->getRow();

            $response = ['status' => true, "data" => $result];
        }else{
            $response = ['status' => false, "message" => "Product not found"];
        }
        
        // echo json_encode($response);
        return $this->response->setJSON($response);
    }

    public function get_reviews()
    {
        $website_code = $this->request->getGet('code');
        if ($website_code) 
        {
            $sql = "SELECT * FROM tbl_booking_reviews WHERE website_code='$website_code'";
            $result = $this->db->query($sql)->getRow();

            $response = ['status' => true, "data" => $result];
        }else{
            $response = ['status' => false, "message" => "Reviews not found"];
        }
        
        // echo json_encode($response);
        return $this->response->setJSON($response);
    }

    public function add_review()
    {
        $website_code = $this->request->getGet('code');
        $reference = $this->request->getGet('ref');
        $rating = $this->request->getGet('rating');
        $description = $this->request->getGet('description');
        if ($reference) 
        {
            $sql = "SELECT * FROM tbl_booking_reviews WHERE reference='$reference'";
            $result = $this->db->query($sql)->getRow();
            if ($result) {
                    $sql = "UPDATE tbl_booking_reviews SET website_code='$website_code', rating='$rating', description='$description' WHERE reference='$reference'";
            }else{
                $sql = "INSERT INTO `tbl_booking_reviews`(`website_code`, `reference`, `rating`, `description`) VALUES ('$website_code','$reference', '$rating','$description')";
            }
            $this->db->query($sql);
            $response = ['status' => true, "message" => "Review submit successfully, thank you!!"];
        }else{
            $response = ['status' => false, "message" => "Unable to submit your review, please email us the information at support@dublinairportparkandfly.com and we will add it manually, thank you."];
        }
        
        
        
            // echo json_encode($response);
        return $this->response->setJSON($response);
    }

    public function update_passenger()
    {
        $reference = $this->request->getGet('reference');
        $carReg = $this->request->getGet('carReg');
        $passenger = $this->request->getGet('passenger');
        $ref = ($reference) ? $reference: $carReg;
     
        $sql = "SELECT id,reference,airport,firstName,carReg FROM tbl_booking WHERE reference='$ref' OR carReg='$ref'";
        $result = $this->db->query($sql)->getRow();
        if ($result) {
            if ($reference) {
                $sql = "UPDATE tbl_booking SET passenger='$passenger' WHERE reference='$reference'";
            }elseif ($carReg) {
                $sql = "UPDATE tbl_booking SET passenger='$passenger' WHERE carReg='$carReg'";
            }
            $this->db->query($sql);
            $response = ['status' => true, "message" => "Passenger updated successfully."];
        }else{
            $response = ['status' => false, "message" => "Unable to update your passenger details, please email us the information at support@dublinairportparkandfly.com and we will update it manually, thank you."];
        }
        
            // echo json_encode($response);
        return $this->response->setJSON($response);
    }

    public function insert_random_strings($length_of_string = "")
    {
        $sql = "SELECT reference FROM tbl_reference";
        $result = $this->db->query($sql)->getRow();
        $reference = $result->reference;
        $reference = strval($reference) + 1;
        $sql = "UPDATE tbl_reference SET reference='$reference'";
        $this->db->query($sql);
        return $reference;

        $response = ['status' => true, "reference" => $reference];
        return $this->response->setJSON($response);

        // // String of all alphanumeric character
        // $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // // Shuffle the $str_result and returns substring
        // // of specified length
        // $string = substr(str_shuffle($str_result), 0, $length_of_string);

        // $isThereNumber = false;
        // for ($i = 0; $i < strlen($string); $i++) {
        //     if (ctype_digit($string[$i])) {
        //         $isThereNumber = true;
        //         break;
        //     }
        // }

        // if ($isThereNumber) {

        //     // $data = $this->didModel->buyUnique($string);
        //     // if ($data) {
        //         return $string;
        //     // } else {
        //     //     return $this->insert_random_strings($length_of_string);
        //     // }

        // } else {
        //     return $this->insert_random_strings($length_of_string);
        // }
    }

    public function get_random_strings()
    {
        $sql = "SELECT reference FROM tbl_reference";
        $result = $this->db->query($sql)->getRow();
        $reference = $result->reference;
        $reference = strval($reference) + 1;
        $sql = "UPDATE tbl_reference SET reference='$reference'";
        $this->db->query($sql);

        $response = ['status' => true, "reference" => $reference];
        return $this->response->setJSON($response);
    }

    public function index()
    {
        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];
        return view('booking/view', $data);
    }

    public function send_contact_us()
    {
        extract($_GET);
        $longDateFormat = date('l, F j, Y \a\t g:i A');
        $message = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>New Enquiry</title>
                </head>
                <body>
                    <h1>New Enquiry</h1>
                    <table>
                        <tr>
                            <td><strong>Enquiry Type:</strong></td>
                            <td>' . $enquiry_type . '</td>
                        </tr>
                        <tr>
                            <td><strong>Sent On:</strong></td>
                            <td>' . $longDateFormat . '</td>
                        </tr>
                        <tr>
                            <td><strong>Website:</strong></td>
                            <td>' . $website . '</td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>' . $title . ' ' . $first_name . ' ' . $surname . '</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><a href="mailto:' . $email . '">' . $email . '</a></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Number:</strong></td> 
                            <td>' . $number . '</td>
                        </tr>
                        <tr>
                            <td><strong>Booking Reference:</strong></td>
                            <td>' . $ref . '</td>
                        </tr>
                        <tr>
                            <td><strong>Enquiry:</strong></td>
                            <td>' . $enquiry . '</td>
                        </tr>
                    </table>
                </body>
                </html>';
        $to = "support@goairportparking.com";
        $subject = "Contact Request From " . $website;
        $result = send_single_email($to, $subject, $message); 
        // print_r($result);die;
        if ($result) {
            $response = ['status' => true, "message" => "email successfully sent"];
        } else {
            $response = ['status' => false, "message" => "unable to send contact us email"];
        }
        echo json_encode($response);
        exit();
    }

    private function generateUniqueTimestamp()
    {
        // Get the current timestamp in microseconds
        $timestamp = microtime(true) * 1000000;

        // Generate a random 2-character string
        $random = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 2);

        // Combine the timestamp and random string
        $uniqueTimestamp = $timestamp . $random;

        // Take the last 4 characters to ensure it's 4 characters long
        $uniqueTimestamp = substr($uniqueTimestamp, -4);

        return $uniqueTimestamp;
    }

    public function create_booking3()
    {
        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $id = isset($_GET['p_id']) ? $_GET['p_id'] : '';
            $carReg = isset($_GET['Car_Registration']) ? $_GET['Car_Registration'] : '';
            $carMake = isset($_GET['Car_Manufacturer']) ? $_GET['Car_Manufacturer'] : '';
            $carModel = isset($_GET['Car_Model']) ? $_GET['Car_Model'] : '';
            $carColour = isset($_GET['Car_Colour']) ? $_GET['Car_Colour'] : '';
            $airport = isset($_GET['airport']) ? $_GET['airport'] : '';
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $passenger = isset($_GET['passenger']) ? $_GET['passenger'] : '';


            $agent = isset($_GET['opitech_agent']) ? $_GET['opitech_agent'] : '';

            $new_price = isset($_GET['price']) ? $_GET['price'] : '';
            $new_reference = isset($_GET['new_reference']) ? $_GET['new_reference'] : "";
            $required_OutTerminal = isset($_GET['Departure_Terminal']) ? $_GET['Departure_Terminal'] : '';
            $required_RetTerminal = isset($_GET['Return_Terminal']) ? $_GET['Return_Terminal'] : '';
            $firstName = isset($_GET['First_Name']) ? $_GET['First_Name'] : '';
            $surname = isset($_GET['Surname']) ? $_GET['Surname'] : '';
            $email = isset($_GET['Email']) ? $_GET['Email'] : '';
            $contactNumber = isset($_GET['Contact_Number']) ? $_GET['Contact_Number'] : '';
            $Departure_Flight_Number = isset($_GET['Departure_Flight_Number']) ? $_GET['Departure_Flight_Number'] : '';
            $Return_Flight_Number = isset($_GET['Return_Flight_Number']) ? $_GET['Return_Flight_Number'] : '';

            $rdate = time();
            $arrival_date = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';
            $departure_date = isset($_GET['changedDate']) ? $_GET['changedDate'] : '';
            $formattedTimearrivalTime = isset($_GET['arrivalTime']) ? $_GET['arrivalTime'] : '';
            $formattedTimedepartureTime = isset($_GET['departureTime']) ? $_GET['departureTime'] : '';
            $promoCode = isset($_GET['promoCode']) ? $_GET['promoCode'] : '';

            $addons = isset($_GET['addons']) ? $_GET['addons']: '';
            $traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source']: '';
            $status = isset($_GET['status']) ? $_GET['status']: '';

            $created_at = isset($_GET['created_at']) ? $_GET['created_at']: '';

            if (strpos("completed", $status) !== false) {
                $status=1;
            } elseif(strpos("cancelled", $status) !== false) {
                $status=2;
            } else{
                $status=0;
            }

            $promo_price = isset($_GET['promo_price']) ? $_GET['promo_price'] : '';
            $arrival_date = strtotime($arrival_date);
            $arrival_date = date('Y-m-d', $arrival_date);
            $departure_date = strtotime($departure_date);
            $departure_date = date('Y-m-d', $departure_date);
            $arrival_date = "$arrival_date $formattedTimearrivalTime:00";
            $departure_date = "$departure_date $formattedTimedepartureTime:00";

            // exit("testing");


            $operatorid = isset($_GET['operator_id']) ? $_GET['operator_id'] : '';

            if (empty($new_reference)) {

                $new_reference = "GL-$airport-" . $this->insert_random_strings(10);
            }else{
                if($airport == 'LBA') {
                    $parts = explode('-', $new_reference);
                    $lastIndex = count($parts) - 1;
                    if (!is_numeric($parts[$lastIndex])) {
                        $parts[$lastIndex] = $this->insert_random_strings(10);
                    }
                    $new_reference = implode('-', $parts);

                }
            }

            $currentTimestamp = time();
            $formattedDate = date("Y-m-d H:i:s", $currentTimestamp);


            $result_check = 0;
            if (!empty($promoCode)) {

                $query = $this->db->query("SELECT * FROM tbl_promotion_code WHERE code = '$promoCode' ");
                $result_check = $query->getRow();
                if ($result_check) {
                } else {
                    $promoCode = "";

                }

            }

            $validationRules = [
                'Car_Registration' => 'required',
                'Car_Manufacturer' => 'required',
                'Car_Model' => 'required',
                'Car_Colour' => 'required',
                'First_Name' => 'required',
                'Surname' => 'required',
                'Contact_Number' => 'required',
                'Email' => 'required|valid_email'
            ];

            // Apply the validation rules to the incoming data.
            $this->validation->setRules($validationRules);

            // Run the validation against the data.
            if (!$this->validation->withRequest($this->request)->run()) {
                // Validation failed, get the validation errors.
                $errors = $this->validation->getErrors();
                echo json_encode(['code' => 0, 'error' => $errors]);
            } else {
                // Validation passed, continue with your logic.
                if ($created_at) {
                    $sql_data = "INSERT INTO tbl_booking 
                        (price, reference, carReg, carMake, carModel, carColour, OutTerminal, RetTerminal, agent_id,
                        firstName, surname, email, contactNumber ,product_id,airport,booked_at,depart_at,source,return_at,status,operator_id,promocode,promo_price,InFltNo,OutFltNo, passenger, show_status,traffic_source,created_at) 
                        VALUES 
                        ('$new_price', '$new_reference', '$carReg', '$carMake', '$carModel', '$carColour', '$required_OutTerminal', '$required_RetTerminal', '$agent',
                        '$firstName', '$surname', '$email', '$contactNumber',$id,'$airport','$created_at','$arrival_date','$website','$departure_date','$status','$operatorid','$promoCode','$promo_price','$Departure_Flight_Number','$Return_Flight_Number', '$passenger', '1','$traffic_source', '$created_at');
                        ";
                }else{
                $sql_data = "INSERT INTO tbl_booking 
                        (price, reference, carReg, carMake, carModel, carColour, OutTerminal, RetTerminal, agent_id,
                        firstName, surname, email, contactNumber ,product_id,airport,booked_at,depart_at,source,return_at,status,operator_id,promocode,promo_price,InFltNo,OutFltNo, passenger, show_status,traffic_source) 
                        VALUES 
                        ('$new_price', '$new_reference', '$carReg', '$carMake', '$carModel', '$carColour', '$required_OutTerminal', '$required_RetTerminal', '$agent',
                        '$firstName', '$surname', '$email', '$contactNumber',$id,'$airport','$formattedDate','$arrival_date','$website','$departure_date','0','$operatorid','$promoCode','$promo_price','$Departure_Flight_Number','$Return_Flight_Number', '$passenger', '1','$traffic_source');
                        ";
                }
                $result = $this->db->query($sql_data);
                $booking_last_inserted_id = $this->db->insertID();
                if ($addons):
                    // foreach($addons as $key => $addon_id)
                    // {
                        $sql_query = "INSERT INTO tbl_booking_addons(booking_id, addon_id) VALUES ('$booking_last_inserted_id','$addons')";
                        $this->db->query($sql_query);
                    // }
                endif;

                $arrival_date = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';
                $departure_date = isset($_GET['changedDate']) ? $_GET['changedDate'] : '';


                if ($result) {
                    echo json_encode(['code' => 1, 'msg' => "Data updated successfully", 'ref_id' => "$new_reference", 'booking_last_inserted_id' => "$booking_last_inserted_id"]);
                    //header("Location: https://skyparkluton.co.uk/payment.php?price=$new_price&ref_id=$new_reference&email=$email&arrival_date=$arrival_date&departure_date=$departure_date&booking_last_inserted_id=$booking_last_inserted_id&arrival_time=$formattedTimearrivalTime&departure_time=$formattedTimedepartureTime");
                    exit();
                } else {

                    echo json_encode(['code' => 0, 'error' => "not saved"]);
                }
            }
        }
    }

    public function get_product_addons()
    {
        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') 
        {
            $product_id = isset($_GET['p_id']) ? $_GET['p_id'] : '';
            $cur = isset($_GET['cur']) ? $_GET['cur'] : '';
            if ($product_id) {
                $query = $this->db->query("SELECT * FROM tbl_product_addons WHERE product_id = '$product_id' ");
                $result = $query->getResult();

                $html='';
                if ($result) {
                    foreach ($result as $key => $r) {
                        $html.='<div class="form-group mb-3"><div class="form-check">
                          <input class="form-check-input" type="radio" id="check'.$r->id.'" name="addons" value="'.$r->id.'" data-price="'.$r->addon_price.'" >
                          <label class="form-check-label"><b>'.$r->addon_name.' , Price: '.$cur.$r->addon_price.'</b><br>'.$r->addon_desc.'</label>
                        </div></div>';
                    }
                     // echo json_encode(['error' => false, 'data' => $html]);
                    echo $html;
                }
                // else{

                //     return json_encode(['error' => true, 'error' => "Addons not available"]);
                    
                // }
            }else{
                $json['error'] = true;
                $json['msg'] = 'Product id is missing';
                echo json_encode($json);
            }
        }
    }

    public function get_addon_price()
    {
        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') 
        {
            // $addon_id = isset($_GET['addons']) ? explode(',',$_GET['addons']) : '';
            $addon_id = isset($_GET['addons']) ? $_GET['addons'] : '';
            $price = 0;
            if ($addon_id) {
                // foreach ($addons as $key => $addon_id) 
                // {
                    $query = $this->db->query("SELECT id,addon_price FROM tbl_product_addons WHERE id = '$addon_id' ");
                    $result = $query->getRow();
                    if ($result) {
                        $price += $result->addon_price;
                        // return json_encode($result);
                    }
                // }
                echo $price;  
                    
            }else{
                $json['error'] = true;
                $json['message'] = 'Addon id is missing';
                // echo json_encode($json);
                echo $price;
            }
        }
    }

    public function checkStatus()
    {
        $transaction_Id = isset($_GET['transaction_Id']) ? $_GET['transaction_Id'] : '';

        $sql_data = "select status from  tbl_booking where transaction_id ='$transaction_Id'";
        $result = $this->db->query($sql_data)->getRow();
        // echo json_encode($result);

        // print_r($result);
        if (isset($result->status) && ($result->status) == 1) {

            echo json_encode($result->status);
            exit;

        } else {

            echo 0;
            exit;
        }


        // return $result->status;



    }
    public function update_booking_status_api()
    {
        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $booking_id = $_GET['booking_id'];
            $booking_type = isset($_GET['booking_type']) ? $_GET['booking_type'] : 'Arrival';
            $transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';

            $status = isset($_GET['status']) ? $_GET['status'] : '1';

            if (isset($_GET['trans_id']) && !empty($_GET['trans_id'])) {

                $sql_data = "update tbl_booking set status='$status' where transaction_id ='$transaction_id' ";


                $sql_email_data = "select * from tbl_booking where transaction_id='$transaction_id'";
                $sql_email_data_r = $this->db->query($sql_email_data)->getRow();
                $book_id = $sql_email_data_r->id;

                $email = $sql_email_data_r->email;

                $sql_data="SELECT * FROM tbl_settings";
                $settings=$this->db->query($sql_data)->getRow();

                $from = $settings->smtpuser;//no_reply@parkingmanagment.com

                $webtype = $_GET['webtype'];

                $res = send_email($email, "Your Parking Booking Confirmation", $book_id, $from, $webtype);

            } else {

                $sql_data = "update tbl_booking set status='$status',booking_type='$booking_type',transaction_id='$transaction_id' where id ='$booking_id' ";

            }

            //echo $sql_data;
            $result = $this->db->query($sql_data);

            print_r($result);
        }

        // echo "umsir;";
    }

    function send_mail_api()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $email = $_GET['email'];
            if (isset($_GET['from'])) {
                $from = $_GET['from'];
            } else {
                $sql_data="SELECT * FROM tbl_settings";
                $settings=$this->db->query($sql_data)->getRow();
                $from = $settings->smtpuser;
            }
            if (isset($_GET['webtype'])) {
                $webtype = $_GET['webtype']; 
            } else {
                $webtype = "Cruise Ports";
            }
            
            $booking_id = $_GET['booking_last_inserted_id'];
           //////////////////////////////////////////////////////////////// 

           $sql = "SELECT * FROM tbl_booking WHERE `id`='$booking_id'";
           $booking = $this->db->query($sql)->getRow();

           $sql_res = "SELECT * FROM tbl_websites WHERE `short_code`='$booking->airport'";
           $booking_airport = $this->db->query($sql_res)->getRow();

           $booking_airport_webtype=$booking_airport->type;
           $booking_airport_webtype = strtolower($booking_airport_webtype);
            
            
            ////////////////////////////////////////////////////////////////////////////
            $sql_data="SELECT * FROM tbl_settings";
            $settings=$this->db->query($sql_data)->getRow();

            $from = $settings->smtpuser;

            $res = send_email($email, "Your Parking Booking Confirmation", $booking_id, $from, $booking_airport_webtype);
            print_r($res);
        }
    }


    public function formatTime($timeString)
    {
        // Ensure the string is 4 characters long
        if (strlen($timeString) !== 4) {
            return false; // Invalid input
        }

        // Insert a colon between the hour and minute parts
        $formattedTime = substr($timeString, 0, 2) . ':' . substr($timeString, 2, 2);
        
        return $formattedTime;
    }


    public function create_booking2()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $code = $_GET['code'];
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            // $date1 = strtotime($selectedDate);
            // $date2 = strtotime($changedDate);
            // $number_of_days = floor(($date2 - $date1) / (60 * 60 * 24));
            // $number_of_days = $number_of_days + 1;
            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));
            $formated_arrive_date = date('Y-m-d', $date);

            $changedDate = strtotime($changedDate);
            $changedDate = date('Y-m-d', $changedDate);

            // $changedDate = str_replace('/', '-', $changedDate); // Convert to dd-mm-YYYY
            // $changedDate = date('Y-m-d', strtotime($changedDate));

            $selectedDate = strtotime($selectedDate);
            $selectedDate = date('Y-m-d', $selectedDate);

            // $selectedDate = str_replace('/', '-', $selectedDate); // Convert to dd-mm-YYYY
            // $selectedDate = date('Y-m-d', strtotime($selectedDate));

            // print_r($selectedDate); pre($changedDate);

            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';
            $traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////


            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);
            $timeFormat2 = $this->convertToTimeFormat($departureTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $departureDateTime = "$selectedDate $timeFormat2";

            // echo "arrival being parsed: $providedDateTime\n";
            // echo "departure being parsed: $departureDateTime\n";

            $timeDifference = $this->getTimeDifference($providedDateTime);
            
            // echo'time: ';print_r($timeDifference);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if($airport=='DUB'){

                if($arrivalTime < $departureTime){
                 
                    $number_of_days=$number_of_days+1;

                }
            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time="00:00";
            
            }


            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $result = $this->db->query($sql_data)->getResult();

            $data = [
                "page_title" => "Booking",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
            ];

            $html = "";
            $array = [];

            foreach ($result as $r) {

                $u = 0;

                if (isset($r->notice_period) && !empty($r->notice_period)) {

                    if ($timeDifference < ($r->notice_period)) {
                        $u = 1;
                        // continue;
                    }
                }
                // if ($airport == 'DUB') {
                //     print_r($u);
                // }

                if (($r->capacity) != 0) {
                    $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                    $cal_capacity_r = $this->db->query($cal_capacity)->getRow();
                    $cal_capacity_result = $cal_capacity_r->count;

                    $product_capacity = ($r->capacity) - ($cal_capacity_result);


                    if ($product_capacity <= 0) {
                        $u = 1;
                        // continue;
                    }
                }
                



                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";
                $result = $this->db->query($sql_data)->getRow();

                $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id ORDER BY id desc";
                $resultCO = $this->db->query($sql_data)->getRow();
                $resultCloseout= '';

                if ($resultCO) {
                    // print_r($resultCO);echo'<br>';
                    $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                    AND 
                    (
                        (`close_out_from` <= '$formated_arrive_date' AND `close_out_to` >= '$formated_arrive_date') 
                        OR 
                        (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$changedDate')
                    ) limit 1"; 
                    $resultCloseout = $this->db->query($sql_data)->getRow();
                    // print_r($sql_data);
                    
                    if ($resultCO->close_out_type_id == 2) {
                        $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                        AND (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$formated_arrive_date') limit 1"; 
                        $resultCloseout = $this->db->query($sql_data)->getRow();
                        // print_r($resultCloseout);
                    }
                }
                if ($resultCloseout) {
                    // echo'<br>closeout';print_r($resultCloseout);
                    $u=1;
                }
                
                
                // && empty($resultCloseout)
                if (isset($result->$dayName) && !empty($result->$dayName)) 
                {
                    $range = $result->$dayName;

                    $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                    $result = $this->db->query($sql_data)->getRow();

                    if (isset($result->price) && !empty($result->price)) {
                        $price = $result->price;
                        $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $product_code_r = $this->db->query($product_code)->getRow();
                        //$link_price = $product_code_r->linked_price;
                        if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                            $price = 0;
                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                            $result = $this->db->query($sql_data)->getRow();
                            if (isset($result->$dayName) && !empty($result->$dayName)) {
                                $range = $result->$dayName;

                                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                                $result = $this->db->query($sql_data)->getRow();

                                if (isset($result->price) && !empty($result->price)) {
                                    $price = $result->price;

                                    $link_price = $r->linked_price;


                                    // $link_amount_to_add = $price + $link_price;


                                    $price = $price + $link_price;
                                }
                            }
                        }
                        $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                        $adjust_prices_by__product_capacity = 1;
                        $adjust_prices_by__operator_capacity = 2;


                        // if(($r->capacity) != 0)
                        // {
                        $amount_to_add = 0;
                        if (($r->capacity) != 0) {
                            if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                                $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                                $result = $this->db->query($sql_data)->getRow();
                                $capacity_full = $result->capacity_full;
                                $capacity = $r->capacity;

                                $percentage_of_capacity = $capacity_full / $capacity * 100;

                                $capacity_threshold_one = $r->capacity_threshold_one;

                                $capacity_threshold_two = $r->capacity_threshold_two;
                                if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                    $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                    $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                                }
                                if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                    $amount_to_add = 0;
                                    $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                    $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                                }

                                $price = $price + $amount_to_add;
                            } //by product
                        } //capity check

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacitys;



                            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                            $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                            $capacity = $sql_data_operator_res->capacity;


                            $limiter = $capacity - $operator_capacity;


                            if ($limiter <= 0) {
                                $u = 1;
                                // continue;
                            }


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $percentage_of_capacity = intval($percentage_of_capacity);

                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;



                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            // if (!empty($capacity_threshold_two)) {
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator

                        //  }
                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'"><button type="submit"
                                    name="Check-Availability"
                                    class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                        class="glyphicon glyphicon-circle-arrow-right"
                                        aria-hidden="true"></span>Book Now</button></a>';

                        $booknow = strip_tags($booknow);

                        if (empty($website)) {
                            $website = "skyparkluton.co.uk";
                        }
                        $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                        //    exit;     
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                        //    print_r($get_promotion_code_data_r);

                        //    exit;
                        $change_price = "";
                        $promo_price = "";

                        if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                            $promo_price = $get_promotion_code_data_r->amount;

                            $price = $price - $promo_price;

                            // $price = (round($price,2));
                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";


                        }

                        $price = (round($price, 2));

                        $array[] = array(
                            "u" => "$u",
                            "price" => "$price",
                            "id" => "$r->id"


                        );
                        // exit;
                    }
                } //price check
            }


            // Sort the array by price in ascending order
            usort($array, function ($a, $b) {
                return $a['price'] - $b['price'];
            });

            // print_r($array);
            $desc_html = "";
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html .= $this->get_sorted_price_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a);

            }

            if (empty($desc_html)) {

                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }

    public function create_oldNewUI_booking()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $code = $_GET['code'];
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            // $date1 = strtotime($selectedDate);
            // $date2 = strtotime($changedDate);
            // $number_of_days = floor(($date2 - $date1) / (60 * 60 * 24));
            // $number_of_days = $number_of_days + 1;
            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));
            $formated_arrive_date = date('Y-m-d', $date);

            $changedDate = strtotime($changedDate);
            $changedDate = date('Y-m-d', $changedDate);

            // $changedDate = str_replace('/', '-', $changedDate); // Convert to dd-mm-YYYY
            // $changedDate = date('Y-m-d', strtotime($changedDate));

            $selectedDate = strtotime($selectedDate);
            $selectedDate = date('Y-m-d', $selectedDate);

            // $selectedDate = str_replace('/', '-', $selectedDate); // Convert to dd-mm-YYYY
            // $selectedDate = date('Y-m-d', strtotime($selectedDate));

            // print_r($selectedDate); pre($changedDate);

            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';
            $traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////


            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if($airport=='DUB'){

                if($arrivalTime < $departureTime){
                 
                    $number_of_days=$number_of_days+1;

                }
            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time="00:00";
            
            }


            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $result = $this->db->query($sql_data)->getResult();

            $data = [
                "page_title" => "Booking",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
            ];

            $html = "";
            $array = [];

            foreach ($result as $r) {

                $u = 0;

                if (isset($r->notice_period) && !empty($r->notice_period)) {

                    if ($timeDifference < ($r->notice_period)) {

                        $u = 1;
                        // continue;
                    }
                }


                if (($r->capacity) != 0) {
                    $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                    $cal_capacity_r = $this->db->query($cal_capacity)->getRow();
                    $cal_capacity_result = $cal_capacity_r->count;

                    $product_capacity = ($r->capacity) - ($cal_capacity_result);

                    if ($product_capacity <= 0) {

                        $u = 1;
                        // continue;
                    }
                }


                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";
                $result = $this->db->query($sql_data)->getRow();

                $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id ORDER BY id desc";
                $resultCO = $this->db->query($sql_data)->getRow();
                $resultCloseout= '';
                if ($resultCO) {
                    $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                    AND 
                    (
                        (`close_out_from` <= '$formated_arrive_date' AND `close_out_to` >= '$formated_arrive_date') 
                        OR 
                        (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$changedDate')
                    ) limit 1"; 
                    $resultCloseout = $this->db->query($sql_data)->getRow();

                    
                    if ($resultCO->close_out_type_id == 2) {
                        $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                        AND (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$formated_arrive_date') limit 1"; 
                        $resultCloseout = $this->db->query($sql_data)->getRow();
                        // print_r($resultCloseout);
                    }
                }
                if ($resultCloseout) {
                    $u=1;
                }
                
               
                // && empty($resultCloseout)
                if (isset($result->$dayName) && !empty($result->$dayName)) 
                {
                    $range = $result->$dayName;

                    $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                    $result = $this->db->query($sql_data)->getRow();

                    if (isset($result->price) && !empty($result->price)) {
                        $price = $result->price;
                        $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $product_code_r = $this->db->query($product_code)->getRow();
                        //$link_price = $product_code_r->linked_price;
                        if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                            $price = 0;
                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                            $result = $this->db->query($sql_data)->getRow();
                            if (isset($result->$dayName) && !empty($result->$dayName)) {
                                $range = $result->$dayName;

                                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                                $result = $this->db->query($sql_data)->getRow();

                                if (isset($result->price) && !empty($result->price)) {
                                    $price = $result->price;

                                    $link_price = $r->linked_price;


                                    // $link_amount_to_add = $price + $link_price;


                                    $price = $price + $link_price;
                                }
                            }
                        }
                        $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                        $adjust_prices_by__product_capacity = 1;
                        $adjust_prices_by__operator_capacity = 2;


                        // if(($r->capacity) != 0)
                        // {
                        $amount_to_add = 0;
                        if (($r->capacity) != 0) {
                            if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                                $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                                $result = $this->db->query($sql_data)->getRow();
                                $capacity_full = $result->capacity_full;
                                $capacity = $r->capacity;

                                $percentage_of_capacity = $capacity_full / $capacity * 100;

                                $capacity_threshold_one = $r->capacity_threshold_one;

                                $capacity_threshold_two = $r->capacity_threshold_two;
                                if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                    $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                    $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                                }
                                if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                    $amount_to_add = 0;
                                    $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                    $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                                }

                                $price = $price + $amount_to_add;
                            } //by product
                        } //capity check

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacitys;



                            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                            $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                            $capacity = $sql_data_operator_res->capacity;


                            $limiter = $capacity - $operator_capacity;


                            if ($limiter <= 0) {
                                $u = 1;
                                // continue;
                            }


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $percentage_of_capacity = intval($percentage_of_capacity);

                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;



                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            // if (!empty($capacity_threshold_two)) {
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator

                        //  }
                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'"><button type="submit"
                                    name="Check-Availability"
                                    class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                        class="glyphicon glyphicon-circle-arrow-right"
                                        aria-hidden="true"></span>Book Now</button></a>';

                        $booknow = strip_tags($booknow);

                        if (empty($website)) {
                            $website = "skyparkluton.co.uk";
                        }
                        $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                        //    exit;     
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                        //    print_r($get_promotion_code_data_r);

                        //    exit;
                        $change_price = "";
                        $promo_price = "";

                        if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                            $promo_price = $get_promotion_code_data_r->amount;

                            $price = $price - $promo_price;

                            // $price = (round($price,2));
                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";


                        }

                        $price = (round($price, 2));

                        $array[] = array(
                            "u" => "$u",
                            "price" => "$price",
                            "id" => "$r->id"


                        );
                        // exit;
                    }
                } //price check
            }

            // Sort the array by price in ascending order
            usort($array, function ($a, $b) {
                return $a['price'] - $b['price'];
            });

            // pre($array);
            $desc_html = "";

            $desc_html .='<div class="services-grid">';
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html .= $this->get_sorted_price_newhtml($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a);
                // print_r($desc_html);die;
            }
            $desc_html .='</div>';

            if (empty($desc_html)) {
                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }

    public function create_newUI_booking()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $code = $_GET['code'];
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            $passenger = $_GET['passenger'];
            // $date1 = strtotime($selectedDate);
            // $date2 = strtotime($changedDate);
            // $number_of_days = floor(($date2 - $date1) / (60 * 60 * 24));
            // $number_of_days = $number_of_days + 1;
            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));
            $formated_arrive_date = date('Y-m-d', $date);
            $changedDate = strtotime($changedDate);
            $changedDate = date('Y-m-d', $changedDate);

            $selectedDate = strtotime($selectedDate);
            $selectedDate = date('Y-m-d', $selectedDate);

            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';
            $traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////


            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if($airport=='DUB'){

                if($arrivalTime < $departureTime){
                 
                    $number_of_days=$number_of_days+1;

                }
            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time="00:00";
            
            }


            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $result = $this->db->query($sql_data)->getResult();

            $data = [
                "page_title" => "Booking",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
            ];

            $html = "";
            $array = [];

            foreach ($result as $r) {

                $u = 0;

                if (isset($r->notice_period) && !empty($r->notice_period)) {

                    if ($timeDifference < ($r->notice_period)) {

                        $u = 1;
                        // continue;
                    }

                }


                if (($r->capacity) != 0) {
                    $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                    $cal_capacity_r = $this->db->query($cal_capacity)->getRow();
                    $cal_capacity_result = $cal_capacity_r->count;

                    $product_capacity = ($r->capacity) - ($cal_capacity_result);

                    if ($product_capacity <= 0) {

                        $u = 1;
                        // continue;
                    }
                }


                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";
                $result = $this->db->query($sql_data)->getRow();

                $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id ORDER BY id desc";
                $resultCO = $this->db->query($sql_data)->getRow();
                $resultCloseout= '';
                if ($resultCO) {
                    $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                    AND 
                    (
                        (`close_out_from` <= '$formated_arrive_date' AND `close_out_to` >= '$formated_arrive_date') 
                        OR 
                        (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$changedDate')
                    ) limit 1"; 
                    $resultCloseout = $this->db->query($sql_data)->getRow();

                    
                    if ($resultCO->close_out_type_id == 2) {
                        $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                        AND (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$formated_arrive_date') limit 1"; 
                        $resultCloseout = $this->db->query($sql_data)->getRow();
                        // print_r($resultCloseout);
                    }
                }
                

                if (isset($result->$dayName) && !empty($result->$dayName) && empty($resultCloseout)) {
                    $range = $result->$dayName;

                    $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                    $result = $this->db->query($sql_data)->getRow();

                    if (isset($result->price) && !empty($result->price)) {
                        $price = $result->price;
                        $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $product_code_r = $this->db->query($product_code)->getRow();
                        //$link_price = $product_code_r->linked_price;
                        if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                            $price = 0;
                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                            $result = $this->db->query($sql_data)->getRow();
                            if (isset($result->$dayName) && !empty($result->$dayName)) {
                                $range = $result->$dayName;

                                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                                $result = $this->db->query($sql_data)->getRow();

                                if (isset($result->price) && !empty($result->price)) {
                                    $price = $result->price;

                                    $link_price = $r->linked_price;


                                    // $link_amount_to_add = $price + $link_price;


                                    $price = $price + $link_price;
                                }
                            }
                        }
                        $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                        $adjust_prices_by__product_capacity = 1;
                        $adjust_prices_by__operator_capacity = 2;


                        // if(($r->capacity) != 0)
                        // {
                        $amount_to_add = 0;
                        if (($r->capacity) != 0) {
                            if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                                $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                                $result = $this->db->query($sql_data)->getRow();
                                $capacity_full = $result->capacity_full;
                                $capacity = $r->capacity;

                                $percentage_of_capacity = $capacity_full / $capacity * 100;

                                $capacity_threshold_one = $r->capacity_threshold_one;

                                $capacity_threshold_two = $r->capacity_threshold_two;
                                if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                    $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                    $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                                }
                                if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                    $amount_to_add = 0;
                                    $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                    $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                                }

                                $price = $price + $amount_to_add;
                            } //by product
                        } //capity check

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacitys;



                            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                            $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                            $capacity = $sql_data_operator_res->capacity;


                            $limiter = $capacity - $operator_capacity;


                            if ($limiter <= 0) {
                                $u = 1;
                                // continue;
                            }


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $percentage_of_capacity = intval($percentage_of_capacity);

                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;



                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            // if (!empty($capacity_threshold_two)) {
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator

                        //  }
                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&passenger='. $passenger .'&traffic_source='.$traffic_source.'" class="btn"><button type="submit"
                                    name="Check-Availability"
                                    class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                        class="glyphicon glyphicon-circle-arrow-right"
                                        aria-hidden="true"></span>Book Now</button></a>';

                        $booknow = strip_tags($booknow);

                        if (empty($website)) {
                            $website = "skyparkluton.co.uk";
                        }
                        $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                        //    exit;     
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                        //    print_r($get_promotion_code_data_r);

                        //    exit;
                        $change_price = "";
                        $promo_price = "";

                        if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                            $promo_price = $get_promotion_code_data_r->amount;

                            $price = $price - $promo_price;

                            // $price = (round($price,2));
                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";


                        }

                        $price = (round($price, 2));

                        $array[] = array(
                            "u" => "$u",
                            "price" => "$price",
                            "id" => "$r->id"


                        );



                        // exit;

                    }
                } //price check
            }



            // Sort the array by price in ascending order
            usort($array, function ($a, $b) {
                return $a['price'] - $b['price'];
            });

            // print_r($array);
            $desc_html = "";
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html .= $this->get_sorted_price_newUI_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a,$passenger);

            }

            if (empty($desc_html)) {

                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }

    public function create_goAirport_booking()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $code = $_GET['code'];
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            $lang = isset($_GET['lang']) ? $_GET['lang'] : '';

            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));

            

            $dateC = DateTime::createFromFormat('d/m/Y', $changedDate);
            $changedDate = $dateC->format('Y-m-d');

            $dateS = DateTime::createFromFormat('d/m/Y', $selectedDate);
            $selectedDate = $dateS->format('Y-m-d');

            $formated_arrive_date = $selectedDate;

            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';
            $traffic_source = isset($_GET['traffic_source']) ? $_GET['traffic_source'] : '';

            // print_r($dayName);echo'<br>FromDate: ';
            // print_r($selectedDate);echo'<br>toDate: ';
            // print_r($changedDate);echo'<br>Days: ';

            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;
            // print_r($number_of_days);die;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////


            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if($airport=='DUB'){

                if($arrivalTime < $departureTime){
                 
                    $number_of_days=$number_of_days+1;

                }
            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time="00:00";
            
            }


            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $result = $this->db->query($sql_data)->getResult();

            $data = [
                "page_title" => "Booking",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
            ];

            $html = "";
            $array = [];

            foreach ($result as $r) {

                $u = 0;

                if (isset($r->notice_period) && !empty($r->notice_period)) {

                    if ($timeDifference < ($r->notice_period)) {

                        $u = 1;
                        // continue;
                    }
                }


                if (($r->capacity) != 0) {
                    $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                    $cal_capacity_r = $this->db->query($cal_capacity)->getRow();
                    $cal_capacity_result = $cal_capacity_r->count;

                    $product_capacity = ($r->capacity) - ($cal_capacity_result);

                    if ($product_capacity <= 0) {

                        $u = 1;
                        // continue;
                    }
                }


                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";
                $result = $this->db->query($sql_data)->getRow();
                // print_r($sql_data);
                // pre($result);
                $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id ORDER BY id desc";
                $resultCO = $this->db->query($sql_data)->getRow();
                $resultCloseout= '';

                if ($resultCO) {
                    $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                    AND 
                    (
                        (`close_out_from` <= '$formated_arrive_date' AND `close_out_to` >= '$formated_arrive_date') 
                        OR 
                        (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$changedDate')
                    ) limit 1"; 
                    $resultCloseout = $this->db->query($sql_data)->getRow();

                    
                    if ($resultCO->close_out_type_id == 2) {
                        $sql_data = "SELECT * FROM `tbl_close_outs` WHERE `product_id` = $r->id 
                        AND (`close_out_from` <= '$changedDate' AND `close_out_to` >= '$formated_arrive_date') limit 1"; 
                        $resultCloseout = $this->db->query($sql_data)->getRow();
                        // print_r($resultCloseout);
                    }
                }
                if ($resultCloseout) {
                    $u=1;
                }

                //  && empty($resultCloseout)
                if (isset($result->$dayName) && !empty($result->$dayName)) {
                    $range = $result->$dayName;

                    $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                    $result = $this->db->query($sql_data)->getRow();

                    if (isset($result->price) && !empty($result->price)) {
                        $price = $result->price;
                        $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $product_code_r = $this->db->query($product_code)->getRow();
                        //$link_price = $product_code_r->linked_price;
                        if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                            $price = 0;
                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                            $result = $this->db->query($sql_data)->getRow();
                            if (isset($result->$dayName) && !empty($result->$dayName)) {
                                $range = $result->$dayName;

                                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                                $result = $this->db->query($sql_data)->getRow();

                                if (isset($result->price) && !empty($result->price)) {
                                    $price = $result->price;

                                    $link_price = $r->linked_price;


                                    // $link_amount_to_add = $price + $link_price;


                                    $price = $price + $link_price;
                                }
                            }
                        }
                        $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                        $adjust_prices_by__product_capacity = 1;
                        $adjust_prices_by__operator_capacity = 2;


                        // if(($r->capacity) != 0)
                        // {
                        $amount_to_add = 0;
                        if (($r->capacity) != 0) {
                            if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                                $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                                $result = $this->db->query($sql_data)->getRow();
                                $capacity_full = $result->capacity_full;
                                $capacity = $r->capacity;

                                $percentage_of_capacity = $capacity_full / $capacity * 100;

                                $capacity_threshold_one = $r->capacity_threshold_one;

                                $capacity_threshold_two = $r->capacity_threshold_two;
                                if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                    $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                    $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                                }
                                if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                    $amount_to_add = 0;
                                    $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                    $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                                }

                                $price = $price + $amount_to_add;
                            } //by product
                        } //capity check

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacitys;



                            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                            $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                            $capacity = $sql_data_operator_res->capacity;


                            $limiter = $capacity - $operator_capacity;


                            if ($limiter <= 0) {
                                $u = 1;
                                // continue;
                            }


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $percentage_of_capacity = intval($percentage_of_capacity);

                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;



                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            // if (!empty($capacity_threshold_two)) {
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator

                        //  }
                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime .'&traffic_source='.$traffic_source.'" class="btn"><button type="submit"
                                    name="Check-Availability"
                                    class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                        class="glyphicon glyphicon-circle-arrow-right"
                                        aria-hidden="true"></span>Book Now</button></a>';

                        $booknow = strip_tags($booknow);

                        if (empty($website)) {
                            $website = "skyparkluton.co.uk";
                        }
                        $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                        //    exit;     
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                        //    print_r($get_promotion_code_data_r);

                        //    exit;
                        $change_price = "";
                        $promo_price = "";

                        if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                            $promo_price = $get_promotion_code_data_r->amount;

                            $price = $price - $promo_price;

                            // $price = (round($price,2));
                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";


                        }

                        $price = (round($price, 2));

                        $array[] = array(
                            "u" => "$u",
                            "price" => "$price",
                            "id" => "$r->id"


                        );



                        // exit;

                    }
                } //price check
            }



            // Sort the array by price in ascending order
            usort($array, function ($a, $b) {
                return $a['price'] - $b['price'];
            });

            // pre($array);
            $desc_html = "";
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html .= $this->get_sorted_price_goAirport_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a,$lang);

            }

            if (empty($desc_html)) {

                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }

    public function create_booking_by_website()
    {

        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') {

            $code = $_GET['code'];
            $website = isset($_GET['website']) ? $_GET['website'] : '';
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            // $date1 = strtotime($selectedDate);
            // $date2 = strtotime($changedDate);
            // $number_of_days = floor(($date2 - $date1) / (60 * 60 * 24));
            // $number_of_days = $number_of_days + 1;
            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));
            $formated_arrive_date = date('Y-m-d', $date);
            $changedDate = strtotime($changedDate);
            $changedDate = date('Y-m-d', $changedDate);

            $selectedDate = strtotime($selectedDate);
            $selectedDate = date('Y-m-d', $selectedDate);

            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////


            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];




            //////////////////////////////////////////////////////////////////////////////////////////////////


            if($airport=='DUB'){

                if($arrivalTime < $departureTime){
                 
                    $number_of_days=$number_of_days+1;

                }


            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time="00:00";
            
            }

            $sql_data = "SELECT * FROM `tbl_websites` WHERE `domain`='$website'";
            $result = $this->db->query($sql_data)->getRow();
            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND (`exclusive_to_website_id`='$result->id') AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // (`exclusive_to_website_id`='$result->id' || `exclusive_to_website_id`=0)
            $result = $this->db->query($sql_data)->getResult();
            // pre($result);
            $data = [
                "page_title" => "Booking",
                "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
            ];

            $html = "";
            $array = [];



            // echo"...";

            foreach ($result as $r) {

                $u = 0;

                if (isset($r->notice_period) && !empty($r->notice_period)) {

                    if ($timeDifference < ($r->notice_period)) {

                        $u = 1;
                        // continue;
                    }

                }


                if (($r->capacity) != 0) {
                    $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                    $cal_capacity_r = $this->db->query($cal_capacity)->getRow();
                    $cal_capacity_result = $cal_capacity_r->count;

                    $product_capacity = ($r->capacity) - ($cal_capacity_result);

                    if ($product_capacity <= 0) {

                        $u = 1;
                        // continue;
                    }
                }


                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                $result = $this->db->query($sql_data)->getRow();
                if (isset($result->$dayName) && !empty($result->$dayName)) {
                    $range = $result->$dayName;

                    $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                    $result = $this->db->query($sql_data)->getRow();

                    if (isset($result->price) && !empty($result->price)) {
                        $price = $result->price;
                        $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $product_code_r = $this->db->query($product_code)->getRow();
                        //$link_price = $product_code_r->linked_price;
                        if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                            $price = 0;
                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                            $result = $this->db->query($sql_data)->getRow();
                            if (isset($result->$dayName) && !empty($result->$dayName)) {
                                $range = $result->$dayName;

                                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                                $result = $this->db->query($sql_data)->getRow();

                                if (isset($result->price) && !empty($result->price)) {
                                    $price = $result->price;

                                    $link_price = $r->linked_price;


                                    // $link_amount_to_add = $price + $link_price;


                                    $price = $price + $link_price;
                                }
                            }
                        }
                        $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                        $adjust_prices_by__product_capacity = 1;
                        $adjust_prices_by__operator_capacity = 2;


                        // if(($r->capacity) != 0)
                        // {
                        $amount_to_add = 0;
                        if (($r->capacity) != 0) {
                            if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                                $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                                $result = $this->db->query($sql_data)->getRow();
                                $capacity_full = $result->capacity_full;
                                $capacity = $r->capacity;

                                $percentage_of_capacity = $capacity_full / $capacity * 100;

                                $capacity_threshold_one = $r->capacity_threshold_one;

                                $capacity_threshold_two = $r->capacity_threshold_two;
                                if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                    $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                    $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                                }
                                if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                    $amount_to_add = 0;
                                    $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                    $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                                }

                                $price = $price + $amount_to_add;
                            } //by product
                        } //capity check

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacitys;



                            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                            $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                            $capacity = $sql_data_operator_res->capacity;


                            $limiter = $capacity - $operator_capacity;


                            if ($limiter <= 0) {
                                $u = 1;
                                // continue;
                            }


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $percentage_of_capacity = intval($percentage_of_capacity);

                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            // echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";exit;



                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            // if (!empty($capacity_threshold_two)) {
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator

                        //  }
                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '"><button type="submit"
                                    name="Check-Availability"
                                    class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                        class="glyphicon glyphicon-circle-arrow-right"
                                        aria-hidden="true"></span>Book Now</button></a>';

                        $booknow = strip_tags($booknow);

                        if (empty($website)) {
                            $website = "skyparkluton.co.uk";
                        }
                        $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                        //    exit;     
                        // $sql_data = "SELECT * FROM `tbl_products`";
                        $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                        //    print_r($get_promotion_code_data_r);

                        //    exit;
                        $change_price = "";
                        $promo_price = "";

                        if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                            $promo_price = $get_promotion_code_data_r->amount;

                            $price = $price - $promo_price;

                            // $price = (round($price,2));
                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $promo_price = (round($promo_price, 2));

                            $change_price = "You Save $cur$promo_price";


                        }

                        $price = (round($price, 2));

                        $array[] = array(
                            "u" => "$u",
                            "price" => "$price",
                            "id" => "$r->id"


                        );



                        // exit;

                    }
                } //price check
            }



            // Sort the array by price in ascending order
            usort($array, function ($a, $b) {
                return $a['price'] - $b['price'];
            });

            // print_r($array);
            $desc_html = "";
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html .= $this->get_sorted_price_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime,'', $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a);

            }





            if (empty($desc_html)) {

                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }

    public function get_sorted_price_html($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur = '£', $webtype, $airport, $u_a)
    {

        $formatted_departureTime_Time = $this->formatTime($departureTime);

        if ($formatted_departureTime_Time !== false) {
            // echo $formatted_departureTime_Time; // Outputs: 01:30

        } else {
            $formatted_departureTime_Time="00:00";
        
        }
        
        
        $sql_data = "SELECT * FROM `tbl_products` WHERE id='$product___id'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $result = $this->db->query($sql_data)->getResult();

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];

        $html = "";
        foreach ($result as $r) {

            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


            $result = $this->db->query($sql_data)->getRow();
            if (isset($result->$dayName) && !empty($result->$dayName)) {
                $range = $result->$dayName;

                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                $result = $this->db->query($sql_data)->getRow();

                if (isset($result->price) && !empty($result->price)) {
                    $price = $result->price;
                    $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $product_code_r = $this->db->query($product_code)->getRow();
                    //$link_price = $product_code_r->linked_price;
                    if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                        $price = 0;
                        // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";


                        $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                        $result = $this->db->query($sql_data)->getRow();
                        if (isset($result->$dayName) && !empty($result->$dayName)) {
                            $range = $result->$dayName;

                            $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                            $result = $this->db->query($sql_data)->getRow();

                            if (isset($result->price) && !empty($result->price)) {
                                $price = $result->price;

                                $link_price = $r->linked_price;


                                // $link_amount_to_add = $price + $link_price;


                                $price = $price + $link_price;
                            }
                        }
                    }
                    $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                    $adjust_prices_by__product_capacity = 1;
                    $adjust_prices_by__operator_capacity = 2;

                    // if(($r->capacity) != 0)
                    // {
                    $amount_to_add = 0;

                    if (($r->capacity) != 0) {

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                            $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $capacity_full = $result->capacity_full;
                            $capacity = $r->capacity;

                            $percentage_of_capacity = $capacity_full / $capacity * 100;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;
                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by product

                    } //capacity check
                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                        $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacitys;



                        $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                        $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                        $capacity = $sql_data_operator_res->capacity;

                        $limiter = $capacity - $operator_capacity;

                        // if($limiter <=0) {
                        //         continue;
                        // }

                        $percentage_of_capacity = $operator_capacity / $capacity * 100;

                        $capacity_threshold_one = $r->capacity_threshold_one;

                        $capacity_threshold_two = $r->capacity_threshold_two;
                        if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                            $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                            $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                        }
                        // if (!empty($capacity_threshold_two)) {
                        if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {


                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                        // $price=1000;

                    } //by operator

                    //  }
                    $information = $r->information;
                    $introduction = $r->introduction;
                    $security_measures = $r->security_measures;
                    $departure_procedures = $r->departure_procedures;
                    $productName = $r->name;
                    $arrival_procedures = $r->arrival_procedures;
                    $transfers = $r->transfers;

                    $booknow = '  <a href="provide-detail.php?airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'"><button type="submit"
                                name="Check-Availability"
                                class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                    class="glyphicon glyphicon-circle-arrow-right"
                                    aria-hidden="true"></span>Book Now</button></a>';

                    $booknow = strip_tags($booknow);

                    $booknow_url = "provide-detail.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if (empty($website)) {
                        $website = "skyparkluton.co.uk";
                    }
                    $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                    //    exit;     
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                    //    print_r($get_promotion_code_data_r);

                    //    exit;
                    $change_price = "";
                    $promo_price = "";

                    if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                        $promo_price = $get_promotion_code_data_r->amount;

                        $price = $price - $promo_price;

                        // $price = (round($price,2));
                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";

                    } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                        $code_price = $get_promotion_code_data_r->amount;

                        $promo_price = $price * $code_price / 100;

                        $price = $price - $promo_price;

                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";


                    }

                    $price = (round($price, 2));

                    $array[] = array(

                        "price" => "$price",
                        "id" => "$r->id"


                    );



                    // exit;
                    if ($r->park_mark || $r->product_type == 'Park & Ride') {
                        $htmlmeetngreet = '<div class="col-sm-6">
                                <ul class="small p-0 temp1-mt-4 m-0 text-med-gray no-bullets parkride">

                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fab fa-product-hunt pr-1"></i> Park and Ride
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fas fa-map-marker-alt pr-1"></i> ' . $r->distance_miles . ' miles from the
                                         ' . $webtype . '
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fas fa-bus pr-1"></i> ' . $r->transfer_time . ' minutes transfer
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-smile pr-1"></i><span> Can be cancelled</span>
                                    </li>';
                            if ($r->customize1) {
                                $htmlmeetngreet .= '<li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-smile pr-1"></i><span> ' . $r->customize1 . '</span>
                                    </li>';
                            }
                            if ($r->customize2) {
                                $htmlmeetngreet .= '<li class="my-2 no-wrap d-none d-sm-block">
                                    <i class="far fa-smile pr-1"></i><span> ' . $r->customize2 . '</span>
                                    </li>';
                            }
                            $htmlmeetngreet .='</ul>
                            </div>';
                    } else {
                        $htmlmeetngreet = '<div class="col-sm-6 pl-0">
                                <ul class="small p-0 temp1-mt-4 m-0 text-med-gray no-bullets parkride">
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-handshake pr-1"></i> Meet and Greet
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fas fa-map-marker-alt pr-1"></i> Valet Parking
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-building pr-1"></i> Arrive At The Terminal
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-smile pr-1"></i><span> Can be cancelled</span>
                                    </li>';
                            if ($r->customize1) {
                                $htmlmeetngreet .= '<li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-smile pr-1"></i><span> ' . $r->customize1 . '</span>
                                    </li>';
                            }
                            if ($r->customize2) {
                                $htmlmeetngreet .= '<li class="my-2 no-wrap d-none d-sm-block">
                                    <i class="far fa-smile pr-1"></i><span> ' . $r->customize2 . '</span>
                                    </li>';
                            }
                            $htmlmeetngreet .='</ul>
                            </div>';
                    }


                    $lbl_transfer_time = htmlspecialchars($r->transfer_time);
                    $lbl_distance_miles = htmlspecialchars($r->distance_miles);
                    $htmlmeetngreet = $htmlmeetngreet;
                    $productName = htmlspecialchars($productName);
                    $lbl_transfer_time = htmlspecialchars($lbl_transfer_time);
                    $lbl_distance_miles = htmlspecialchars($lbl_distance_miles);
                    $booknow_url = htmlspecialchars($booknow_url);
                    $price = $price;

                    // $price = (round($price,2));

                    $introduction = htmlspecialchars($introduction);
                    $information = htmlspecialchars($information);
                    $security_measures = htmlspecialchars($security_measures);
                    $departure_procedures = htmlspecialchars($departure_procedures);
                    $arrival_procedures = htmlspecialchars($arrival_procedures);
                    $transfers = htmlspecialchars($transfers);

                    $booknow_url = "provide-detail.php?airport=$airport&promo_code=$code&promo_price=$promo_price&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if ($u_a == 0) {

                        $moreinfo = "<button class=\"btn btn-outline-dark buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\" type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$cur$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers'>More Info</button>";


                        $strick_hash="$price$price$price";
                       
                        $hash = hash('sha256', $strick_hash);

                        // $hash='test';


                        $buy_now_button = ' <div class="col-sm-12">
                                                <a href="provide-detail.php?airport=' . $airport . '&promo_code=' . $code . '&promo_price=' . $promo_price . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'&var=' . $hash . '"><button type="submit"
                                                name="Check-Availability"
                                                class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                                    class="glyphicon glyphicon-circle-arrow-right"
                                                    aria-hidden="true"></span>Book Now</button></a>
                                            </div>';
                    } else {

                        $moreinfo = "";
                        $buy_now_button = "<p> <button type='button' class='btn btn-default'style='font-size: 18px;border: 2px solid gray;font-weight: bold;width: 300px'> SOLD OUT </button></p>";
                        // $buy_now_button = "<p> <b> Not Available </b></p>";

                    }

                    //$moreinfo="<button type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers' class=\"btn btn-outline-dark buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\"><span class=\"glyphicon glyphicon-plus\" aria-hidden="true"></span>More Info</button>";
                    if (trim($r->logo) == "" || trim($r->logo) == "na") {
                        $product_html = '<div class="row result-box-title">
                            <div class="col align-self-center">
                                <h4 class="mt-2 bx-hd">' . $r->name . '</h4>
                            </div>
                            <div class="col-3">
                                <div class="result-box-logo">
                                   <img src="https://globalparkingtech.co.uk/logos/products/' . trim($r->logo) . '">
                                </div>
                            </div>
                        </div>';
                    } else {
                        $product_html = '<div class="row result-box-title">
                            <div class="col align-self-center">
                                <h4 class="mt-2 bx-hd">' . $r->name . '</h4>
                            </div>
                            <div class="col-3">
                                <div class="result-box-logo">
                                    <img src="https://globalparkingtech.co.uk/logos/products/' . trim($r->logo) . '">
                                </div>
                            </div>
                        </div>';
                    }


                    $html .= '<div class="col-sm-12">
                        <div class="box-airport">
                            <div class="row">
                                <div class="col-sm-4">

                                    <div class="main-picture" style="background-image: url(assets/images/hero_lm.jpg);">
                                        <div class="overlay">
                                            <div class="trusted-score-breakdown col-10 mx-auto pt-0">
                                                <div class="row">
                                                    <div class="col-12 pl-0">
                                                        <div class="score-label">Accessibility</div>
                                                    </div>
                                                    <div class="col-12 pl-0">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar" role="progressbar"
                                                                aria-valuenow="' . $r->score_accessibility . '" aria-valuemin="0" aria-valuemax="100"
                                                                style="width: ' . $r->score_accessibility . '%;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 pl-0">
                                                        <div class="score-label">Price</div>
                                                    </div>
                                                    <div class="col-12 pl-0">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar" role="progressbar"
                                                                aria-valuenow="' . $r->score_price . '" aria-valuemin="0" aria-valuemax="100"
                                                                style="width: ' . $r->score_price . '%;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 pl-0">
                                                        <div class="score-label">Efficiency</div>
                                                    </div>
                                                    <div class="col-12 pl-0">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar" role="progressbar"
                                                                aria-valuenow="' . $r->score_efficiency . '" aria-valuemin="0" aria-valuemax="100"
                                                                style="width: ' . $r->score_efficiency . '%;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 pl-0">
                                                        <div class="score-label">Security</div>
                                                    </div>
                                                    <div class="col-12 pl-0">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar" role="progressbar"
                                                                aria-valuenow="' . $r->score_security . '" aria-valuemin="0" aria-valuemax="100"
                                                                style="width: ' . $r->score_security . '%;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-8">



                                    ' . $product_html . '


                                    <div class="seperator mb-3 mt-2">
                                        <span class="line mr-2 bg-color"></span><span class="dots bg-color"></span>
                                    </div>

                                    <div class="row">
                                        ' . $htmlmeetngreet . '
                                        <div class="col-sm-6">
                                            <div class="row">
                                          
                                                <div class="col-sm-12">
                                                    <h2 class="tt_resultPrice-resp"><strong>' . $cur . $price . '</strong></h2>
                                                </div>
                                                <div class="col-12 p-0 m-0 text-sm-center">
                                                    <strong><p class="c">' . $change_price . '</p></strong>
                                                </div> 
                                               
                                               ' . $buy_now_button . '

                                                <div class="col-sm-12">
                                                    ' . $moreinfo . '
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>';
                }
            } //price check
        }


        return $html;

    }
    public function get_sorted_price_newhtml($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur = '£', $webtype, $airport, $u_a)
    {

        $formatted_departureTime_Time = $this->formatTime($departureTime);

        if ($formatted_departureTime_Time !== false) {
            // echo $formatted_departureTime_Time; // Outputs: 01:30

        } else {
            $formatted_departureTime_Time="00:00";
        
        }
        
        $sql_data = "SELECT * FROM `tbl_products` WHERE id='$product___id'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $result = $this->db->query($sql_data)->getResult();

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];

        $html = "";
        foreach ($result as $r) {

            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


            $result = $this->db->query($sql_data)->getRow();
            if (isset($result->$dayName) && !empty($result->$dayName)) {
                $range = $result->$dayName;

                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                $result = $this->db->query($sql_data)->getRow();

                if (isset($result->price) && !empty($result->price)) {
                    $price = $result->price;
                    $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $product_code_r = $this->db->query($product_code)->getRow();
                    //$link_price = $product_code_r->linked_price;
                    if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                        $price = 0;
                        // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";


                        $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                        $result = $this->db->query($sql_data)->getRow();
                        if (isset($result->$dayName) && !empty($result->$dayName)) {
                            $range = $result->$dayName;

                            $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                            $result = $this->db->query($sql_data)->getRow();

                            if (isset($result->price) && !empty($result->price)) {
                                $price = $result->price;

                                $link_price = $r->linked_price;


                                // $link_amount_to_add = $price + $link_price;


                                $price = $price + $link_price;
                            }
                        }
                    }
                    $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                    $adjust_prices_by__product_capacity = 1;
                    $adjust_prices_by__operator_capacity = 2;

                    // if(($r->capacity) != 0)
                    // {
                    $amount_to_add = 0;

                    if (($r->capacity) != 0) {

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                            $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $capacity_full = $result->capacity_full;
                            $capacity = $r->capacity;

                            $percentage_of_capacity = $capacity_full / $capacity * 100;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;
                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by product

                    } //capacity check
                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                        $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacitys;



                        $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                        $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                        $capacity = $sql_data_operator_res->capacity;

                        $limiter = $capacity - $operator_capacity;

                        // if($limiter <=0) {
                        //         continue;
                        // }

                        $percentage_of_capacity = $operator_capacity / $capacity * 100;

                        $capacity_threshold_one = $r->capacity_threshold_one;

                        $capacity_threshold_two = $r->capacity_threshold_two;
                        if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                            $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                            $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                        }
                        // if (!empty($capacity_threshold_two)) {
                        if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {


                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                        // $price=1000;

                    } //by operator

                    //  }
                    $information = $r->information;
                    $introduction = $r->introduction;
                    $security_measures = $r->security_measures;
                    $departure_procedures = $r->departure_procedures;
                    $productName = $r->name;
                    $arrival_procedures = $r->arrival_procedures;
                    $transfers = $r->transfers;

                    $booknow = '  <a href="provide-detail.php?airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'" class="btn btn-block">Select This Service</a>'; 

                    $booknow = strip_tags($booknow);

                    $booknow_url = "provide-detail.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if (empty($website)) {
                        $website = "skyparkluton.co.uk";
                    }
                    $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                    //    exit;     
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                    //    print_r($get_promotion_code_data_r);

                    //    exit;
                    $change_price = "";
                    $promo_price = "";

                    if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                        $promo_price = $get_promotion_code_data_r->amount;

                        $price = $price - $promo_price;

                        // $price = (round($price,2));
                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";

                    } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                        $code_price = $get_promotion_code_data_r->amount;

                        $promo_price = $price * $code_price / 100;

                        $price = $price - $promo_price;

                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";


                    }

                    $price = (round($price, 2));

                    $array[] = array(

                        "price" => "$price",
                        "id" => "$r->id"


                    );



                    // exit;
                    if ($r->product_type == 'Park & Ride') {
                        $htmlmeetngreet = '<ul class="service-features">

                                    <li>
                                        <i class="fas fa-car"></i> Park and Ride
                                    </li>
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> ' . $r->distance_miles . ' miles from the
                                         ' . $webtype . '
                                    </li>
                                    <li>
                                        <i class="fas fa-clock"></i> ' . $r->transfer_time . ' minutes transfer
                                    </li>
                                    <li>
                                        <i class="fas fa-ban"></i><span> Can be cancelled</span>
                                    </li>';
                            if ($r->customize1) {
                                $htmlmeetngreet .= '<li>
                                        <i class="far fa-smile pr-1"></i><span> ' . $r->customize1 . '</span>
                                    </li>';
                            }
                            if ($r->customize2) {
                                $htmlmeetngreet .= '<li>
                                    <i class="fas fa-smile"></i><span> ' . $r->customize2 . '</span>
                                    </li>';
                            }
                            $htmlmeetngreet .='</ul>';
                    } elseif ($r->product_type == 'Meet and Greet') {
                        $htmlmeetngreet = '<ul class="service-features">
                                    <li>
                                        <i class="fas fa-car"></i> Meet and Greet
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i> Valet Parking
                                    </li>
                                    <li>
                                        <i class="fas fa-clock"></i> Arrive At The Terminal
                                    </li>
                                    <li>
                                        <i class="fas fa-ban"></i><span> Can be cancelled</span>
                                    </li>';
                            if ($r->customize1) {
                                $htmlmeetngreet .= '<li>
                                        <i class="fas fa-smile"></i><span> ' . $r->customize1 . '</span>
                                    </li>';
                            }
                            if ($r->customize2) {
                                $htmlmeetngreet .= '<li>
                                    <i class="fas fa-smile"></i><span> ' . $r->customize2 . '</span>
                                    </li>';
                            }
                            $htmlmeetngreet .='</ul>';
                    } else{
                        $htmlmeetngreet = '<ul class="service-features">
                                    <li>
                                        <i class="fas fa-car"></i> '.$r->product_type.'
                                    </li>

                                    <li>
                                        <i class="fas fa-clock"></i> Arrive At The Car Park
                                    </li>
                                    <li>
                                        <i class="fas fa-map-marker-alt"></i> ' . $r->distance_miles . ' miles from the
                                         ' . $webtype . '
                                    </li>
                                    <li>
                                        <i class="fas fa-ban"></i><span> Can be cancelled</span>
                                    </li>';
                            if ($r->customize1) {
                                $htmlmeetngreet .= '<li>
                                        <i class="fas fa-smile"></i><span> ' . $r->customize1 . '</span>
                                    </li>';
                            }
                            if ($r->customize2) {
                                $htmlmeetngreet .= '<li>
                                    <i class="fas fa-smile"></i><span> ' . $r->customize2 . '</span>
                                    </li>';
                            }
                            $htmlmeetngreet .='</ul>';
                    }


                    $lbl_transfer_time = htmlspecialchars($r->transfer_time);
                    $lbl_distance_miles = htmlspecialchars($r->distance_miles);
                    $htmlmeetngreet = $htmlmeetngreet;
                    $productName = htmlspecialchars($productName);
                    $lbl_transfer_time = htmlspecialchars($lbl_transfer_time);
                    $lbl_distance_miles = htmlspecialchars($lbl_distance_miles);
                    $booknow_url = htmlspecialchars($booknow_url);
                    $price = $price;

                    // $price = (round($price,2));

                    $introduction = htmlspecialchars($introduction);
                    $information = htmlspecialchars($information);
                    $security_measures = htmlspecialchars($security_measures);
                    $departure_procedures = htmlspecialchars($departure_procedures);
                    $arrival_procedures = htmlspecialchars($arrival_procedures);
                    $transfers = htmlspecialchars($transfers);

                    $booknow_url = "provide-detail.php?airport=$airport&promo_code=$code&promo_price=$promo_price&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if ($u_a == 0) {

                        $moreinfo = "<button class=\"btn btn-block\" type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$cur$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers'>More Info</button>";


                        $strick_hash="$price$price$price";
                       
                        $hash = hash('sha256', $strick_hash);

                        // $hash='test';


                        $buy_now_button = ' <div class="col-sm-12">
                                                <a href="provide-detail.php?airport=' . $airport . '&promo_code=' . $code . '&promo_price=' . $promo_price . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&traffic_source='.$traffic_source.'&var=' . $hash . '" class="btn btn-block">Select This Service</a>
                                            </div>';
                    } else {

                        $moreinfo = "";
                        // $buy_now_button = "<p> <button type='button' class='btn btn-default'style='font-size: 18px;border: 2px solid gray;font-weight: bold;width: 300px'> SOLD OUT </button></p>";
                        $buy_now_button = "<p> <b> Not Available </b></p>";

                    }

                    if (!empty($r->logo1) || trim($r->logo1) != NULL) {
                        $product_html = '<div class="service-image">
                                <img src="https://globalparkingtech.co.uk/logos/products/' . trim($r->logo1) . '" alt="' . $r->name . '">
                            </div>
                            <div class="service-content">
                                <h3>' . $r->name . '</h3>
                                <div class="service-price">' . $cur . $price . '<span></span></div>
                                '.$htmlmeetngreet.'
                                ' . $buy_now_button . '
                                ' . $moreinfo . '
                            </div>';
                    } else {
                        $product_html = '<div class="service-image">
                                <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="' . $r->name . '">
                            </div>
                            <div class="service-content">
                                <h3>' . $r->name . '</h3>
                                <div class="service-price">' . $cur . $price . '<span></span></div>
                                '.$htmlmeetngreet.'
                                ' . $buy_now_button . '
                                ' . $moreinfo . '
                            </div>';
                    }


                    $html .= '<div class="service-card">
                        ' . $product_html . '
                    </div>';
                }
            } //price check
        }
        // pre($html);
        return $html;

    }

    public function get_sorted_price_newUI_html($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur = '£', $webtype, $airport, $u_a, $passenger)
    {

        $formatted_departureTime_Time = $this->formatTime($departureTime);

        if ($formatted_departureTime_Time !== false) {
            // echo $formatted_departureTime_Time; // Outputs: 01:30

        } else {
            $formatted_departureTime_Time="00:00";
        
        }
        
        
        
        
        $sql_data = "SELECT * FROM `tbl_products` WHERE id='$product___id'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $result = $this->db->query($sql_data)->getResult();

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];

        $html = "";
        foreach ($result as $r) {

            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


            $result = $this->db->query($sql_data)->getRow();
            if (isset($result->$dayName) && !empty($result->$dayName)) {
                $range = $result->$dayName;

                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                $result = $this->db->query($sql_data)->getRow();

                if (isset($result->price) && !empty($result->price)) {
                    $price = $result->price;
                    $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $product_code_r = $this->db->query($product_code)->getRow();
                    //$link_price = $product_code_r->linked_price;
                    if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                        $price = 0;
                        // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";


                        $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                        $result = $this->db->query($sql_data)->getRow();
                        if (isset($result->$dayName) && !empty($result->$dayName)) {
                            $range = $result->$dayName;

                            $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                            $result = $this->db->query($sql_data)->getRow();

                            if (isset($result->price) && !empty($result->price)) {
                                $price = $result->price;

                                $link_price = $r->linked_price;


                                // $link_amount_to_add = $price + $link_price;


                                $price = $price + $link_price;
                            }
                        }
                    }
                    $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                    $adjust_prices_by__product_capacity = 1;
                    $adjust_prices_by__operator_capacity = 2;

                    // if(($r->capacity) != 0)
                    // {
                    $amount_to_add = 0;

                    if (($r->capacity) != 0) {

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                            $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $capacity_full = $result->capacity_full;
                            $capacity = $r->capacity;

                            $percentage_of_capacity = $capacity_full / $capacity * 100;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;
                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by product

                    } //capacity check
                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                        $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacitys;



                        $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                        $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                        $capacity = $sql_data_operator_res->capacity;

                        $limiter = $capacity - $operator_capacity;

                        // if($limiter <=0) {
                        //         continue;
                        // }

                        $percentage_of_capacity = $operator_capacity / $capacity * 100;

                        $capacity_threshold_one = $r->capacity_threshold_one;

                        $capacity_threshold_two = $r->capacity_threshold_two;
                        if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                            $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                            $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                        }
                        // if (!empty($capacity_threshold_two)) {
                        if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {


                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                        // $price=1000;

                    } //by operator

                    //  }
                    $information = $r->information;
                    $introduction = $r->introduction;
                    $security_measures = $r->security_measures;
                    $departure_procedures = $r->departure_procedures;
                    $productName = $r->name;
                    $arrival_procedures = $r->arrival_procedures;
                    $transfers = $r->transfers;

                    $booknow = '  <a href="provide-detail.php?airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&passenger='. $passenger .'&traffic_source='.$traffic_source.'" class="btn">Book Now</a>';

                    $booknow = strip_tags($booknow);

                    $booknow_url = "provide-detail.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&passenger=$passenger&traffic_source=$traffic_source";

                    if (empty($website)) {
                        $website = "skyparkluton.co.uk";
                    }
                    $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                    //    exit;     
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                    //    print_r($get_promotion_code_data_r);

                    //    exit;
                    $change_price = "";
                    $promo_price = "";

                    if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                        $promo_price = $get_promotion_code_data_r->amount;

                        $price = $price - $promo_price;

                        // $price = (round($price,2));
                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";

                    } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                        $code_price = $get_promotion_code_data_r->amount;

                        $promo_price = $price * $code_price / 100;

                        $price = $price - $promo_price;

                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";


                    }

                    $price = (round($price, 2));

                    $array[] = array(

                        "price" => "$price",
                        "id" => "$r->id"


                    );

                    if ($r->park_mark || $r->product_type == 'Park & Ride') {
                        $htmlmeetngreet = '<ul class="option-features parkride">

                                    <li class="badge badge-success">
                                        <i class="fab fa-product-hunt pr-1"></i> Park and Ride
                                    </li>
                                    <li class="badge badge-info">
                                        <i class="fas fa-map-marker-alt pr-1"></i> ' . $r->distance_miles . ' miles from the
                                         ' . $webtype . '
                                    </li>
                                    <li class="badge badge-primary">
                                        <i class="fas fa-bus pr-1"></i> ' . $r->transfer_time . ' minutes transfer
                                    </li>';
                                if ($r->customize1) {
                                    $htmlmeetngreet .= '<li class="badge badge-info">
                                            <i class="far fa-smile pr-1"></i><span> ' . $r->customize1 . '</span>
                                        </li>';
                                }
                                if ($r->customize2) {
                                    $htmlmeetngreet .= '<li class="badge btn-info">
                                        <i class="far fa-smile pr-1"></i><span> ' . $r->customize2 . '</span>
                                        </li>';
                                }
                                $htmlmeetngreet .='</ul>';
                    } else {
                        $htmlmeetngreet = '<ul class="option-features parkride">
                                    <li class="badge badge-success">
                                        <i class="far fa-handshake pr-1"></i> Meet and Greet
                                    </li>
                                    <li class="badge badge-info">
                                        <i class="fas fa-map-marker-alt pr-1"></i> Valet Parking
                                    </li>
                                    <li class="badge badge-primary">
                                        <i class="far fa-building pr-1"></i> Arrive At The Terminal
                                    </li>';
                                if ($r->customize1) {
                                    $htmlmeetngreet .= '<li class="badge badge-info">
                                            <i class="far fa-smile pr-1"></i><span> ' . $r->customize1 . '</span>
                                        </li>';
                                }
                                if ($r->customize2) {
                                    $htmlmeetngreet .= '<li class="badge badge-info">
                                        <i class="far fa-smile pr-1"></i><span> ' . $r->customize2 . '</span>
                                        </li>';
                                }
                                $htmlmeetngreet .='</ul>';
                    }


                    $lbl_transfer_time = htmlspecialchars($r->transfer_time);
                    $lbl_distance_miles = htmlspecialchars($r->distance_miles);
                    $htmlmeetngreet = $htmlmeetngreet;
                    $productName = htmlspecialchars($productName);
                    $lbl_transfer_time = htmlspecialchars($lbl_transfer_time);
                    $lbl_distance_miles = htmlspecialchars($lbl_distance_miles);
                    $booknow_url = htmlspecialchars($booknow_url);
                    $price = $price;

                    // $price = (round($price,2));

                    $introduction = htmlspecialchars($introduction);
                    $information = htmlspecialchars($information);
                    $security_measures = htmlspecialchars($security_measures);
                    $departure_procedures = htmlspecialchars($departure_procedures);
                    $arrival_procedures = htmlspecialchars($arrival_procedures);
                    $transfers = htmlspecialchars($transfers);

                    $booknow_url = "provide-detail.php?airport=$airport&promo_code=$code&promo_price=$promo_price&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&passenger=$passenger&traffic_source=$traffic_source";

                    if ($u_a == 0) {

                        $moreinfo = "<button class=\"btn btn-accent moreInfoBtn buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\" type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$cur$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers'>More Info</button>";


                        $strick_hash="$price$price$price";
                       
                        $hash = hash('sha256', $strick_hash);

                        $buy_now_button = ' <div class="option-btn">
                                                <a href="provide-detail.php?airport=' . $airport . '&promo_code=' . $code . '&promo_price=' . $promo_price . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '&passenger='. $passenger .'&traffic_source='.$traffic_source.'&var=' . $hash . '" class="btn"></span>Book Now</a>
                                            </div>';
                    } else {


                        $moreinfo = "";
                        $buy_now_button = "<p> <b> Not Available </b></p>";

                    }
                    // style="background-image: url(https://globalparkingtech.co.uk/logos/products/' . trim($r->logo) . ');"
                    if (trim($r->logo) == "" || trim($r->logo) == "na") {
                        $product_html = '<div class="option-img">'. $r->product_type .'</div>
                                <div class="option-header">
                                    <h3 class="option-title">' . $r->name . '</h3>
                                    <div class="option-price">' . $cur . $price . '</div>
                                </div>';
                    } else {
                        $product_html = '<div class="option-img">'. $r->product_type .'</div>
                                <div class="option-header">
                                    <h3 class="option-title">' . $r->name . '</h3>
                                    <div class="option-price">' . $cur . $price . '</div>
                                </div>';
                    }
                    
                    $html .='<div class="option-card">
                                '. $product_html .'
                                <hr>
                                ' . $htmlmeetngreet . '
                                ' . $buy_now_button . '
                                ' . $moreinfo . '
                            </div>';
                }
            } //price check
        }


        return $html;

    }

    public function get_sorted_price_goAirport_html($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $traffic_source, $number_of_days, $code, $website, $dayName, $selectedDate, $cur = '£', $webtype, $airport, $u_a, $lang)
    {

        $formatted_departureTime_Time = $this->formatTime($departureTime);

        if ($formatted_departureTime_Time !== false) {
            // echo $formatted_departureTime_Time; // Outputs: 01:30

        } else {
            $formatted_departureTime_Time="00:00";
        
        }
        
        
        $sql_data = "SELECT * FROM `tbl_products` WHERE id='$product___id'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $result = $this->db->query($sql_data)->getResult();

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];

        $html = "";
        foreach ($result as $r) {

            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


            $result = $this->db->query($sql_data)->getRow();
            if (isset($result->$dayName) && !empty($result->$dayName)) {
                $range = $result->$dayName;

                $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                $result = $this->db->query($sql_data)->getRow();

                if (isset($result->price) && !empty($result->price)) {
                    $price = $result->price;
                    $product_code = "SELECT * FROM `tbl_products` WHERE `product_code`='$r->linked_product_code' ";
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $product_code_r = $this->db->query($product_code)->getRow();
                    //$link_price = $product_code_r->linked_price;
                    if (isset($product_code_r->id) && !empty($product_code_r->id)) {

                        $price = 0;
                        // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";


                        $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


                        $result = $this->db->query($sql_data)->getRow();
                        if (isset($result->$dayName) && !empty($result->$dayName)) {
                            $range = $result->$dayName;

                            $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND`name`=$number_of_days";
                            $result = $this->db->query($sql_data)->getRow();

                            if (isset($result->price) && !empty($result->price)) {
                                $price = $result->price;

                                $link_price = $r->linked_price;


                                // $link_amount_to_add = $price + $link_price;


                                $price = $price + $link_price;
                            }
                        }
                    }
                    $adjust_prices_by_capacity = $r->adjust_prices_by_capacity;
                    $adjust_prices_by__product_capacity = 1;
                    $adjust_prices_by__operator_capacity = 2;

                    // if(($r->capacity) != 0)
                    // {
                    $amount_to_add = 0;

                    if (($r->capacity) != 0) {

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                            $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                            $result = $this->db->query($sql_data)->getRow();
                            $capacity_full = $result->capacity_full;
                            $capacity = $r->capacity;

                            $percentage_of_capacity = $capacity_full / $capacity * 100;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;
                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by product

                    } //capacity check
                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                        $sql_data = "SELECT count(*) as operator_capacitys  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacitys;



                        $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $r->operator_id";
                        $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                        $capacity = $sql_data_operator_res->capacity;

                        $limiter = $capacity - $operator_capacity;

                        // if($limiter <=0) {
                        //         continue;
                        // }

                        $percentage_of_capacity = $operator_capacity / $capacity * 100;

                        $capacity_threshold_one = $r->capacity_threshold_one;

                        $capacity_threshold_two = $r->capacity_threshold_two;
                        if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                            $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                            $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                        }
                        // if (!empty($capacity_threshold_two)) {
                        if (!empty($capacity_threshold_two) && $percentage_of_capacity >= $capacity_threshold_two) {


                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                        // $price=1000;

                    } //by operator

                    //  }
                    $information = ($lang == 'ar' && $r->information_ar)? $r->information_ar : $r->information;
                    $introduction = ($lang == 'ar' && $r->introduction_ar)? $r->introduction_ar : $r->introduction;
                    $security_measures = ($lang == 'ar' && $r->security_measures_ar)? $r->security_measures_ar : $r->security_measures;
                    $departure_procedures = ($lang == 'ar' && $r->departure_procedures_ar)? $r->departure_procedures_ar : $r->departure_procedures;
                    // $productName = $r->name;
                    $arrival_procedures = ($lang == 'ar' && $r->arrival_procedures_ar)? $r->arrival_procedures_ar : $r->arrival_procedures;
                    $transfers = ($lang == 'ar' && $r->transfers_ar)? $r->transfers_ar : $r->transfers;

                    $productName = $r->name;
                    if ($lang == 'ar') {
                        $productName = ($r->name_ar)? $r->name_ar: $r->name;
                    }

                    $booknow = '<a href="profile.php?airport=' . $airport .
                            '&operator_id=' . $r->operator_id .
                            '&p_id=' . $r->id .
                            '&price=' . $price .
                            '&name=' . urlencode($r->name) .
                            '&selectedDate=' . $selectedDate .
                            '&changedDate=' . $changedDate .
                            '&arrivalTime=' . $arrivalTime .
                            '&departureTime=' . $departureTime .
                            '&traffic_source=' . $traffic_source . '" 
                            class="w-full block py-2 rounded-lg text-center font-medium hover:scale-105 hover:shadow-lg transition transform duration-300 ease-in-out" 
                            style="background-color: #252654; color:#ffffff;" 
                            onmouseover="this.style.backgroundColor=\'#f8bf12\'; this.style.color=\'#252654\';" 
                            onmouseout="this.style.backgroundColor=\'#252654\'; this.style.color=\'#ffffff\';">
                            Book Now
                            </a>';


                    $booknow = strip_tags($booknow);

                    $booknow_url = "profile.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if (empty($website)) {
                        $website = "skyparkluton.co.uk";
                    }
                    $get_promotion_code_data = "SELECT * FROM `tbl_promotion_code` WHERE  `code`='$code' AND    (`website`='$website' OR `website`='All') AND (`valid_from`>=$formated_arrive_date and `valid_to`>= $changedDate)";
                    //    exit;     
                    // $sql_data = "SELECT * FROM `tbl_products`";
                    $get_promotion_code_data_r = $this->db->query($get_promotion_code_data)->getRow();

                    //    print_r($get_promotion_code_data_r);

                    //    exit;
                    $change_price = "";
                    $promo_price = "";

                    if (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'value') {

                        $promo_price = $get_promotion_code_data_r->amount;

                        $price = $price - $promo_price;

                        // $price = (round($price,2));
                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";

                    } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                        $code_price = $get_promotion_code_data_r->amount;

                        $promo_price = $price * $code_price / 100;

                        $price = $price - $promo_price;

                        $promo_price = (round($promo_price, 2));
                        $change_price = "You Save $cur$promo_price";


                    }

                    $price = (round($price, 2));

                    $array[] = array(

                        "price" => "$price",
                        "id" => "$r->id"


                    );
                    $item1='Meet and Greet';
                    $item2 ='Valet Parking';
                    $item3 ='Arrive At The Terminal';

                
                    if ($lang == 'ar') 
                    {
                        $item1= 'الاستقبال والترحيب';
                        $item2= 'خدمة صف السيارات';
                        $item3 ='الوصول إلى المطار';
                    }

                    if ($r->park_mark || $r->product_type == 'Park & Ride') {
                        $htmlmeetngreet = '<div class="flex flex-col gap-2 mb-4 text-gray-700 text-sm">

                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-handshake" style="color:#f8bf12;"></i> Park and Ride
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-map-marker-alt pr-1" style="color:#f8bf12;"></i> ' . $r->distance_miles . ' miles from the
                                         ' . $webtype . '
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bus pr-1" style="color:#f8bf12;"></i> ' . $r->transfer_time . ' minutes transfer
                                    </div>';
                                if ($r->customize1) {
                                    $htmlmeetngreet .= '<div class="flex items-center gap-2">
                                            <i class="fa-solid fa-smile pr-1" style="color:#f8bf12;"></i><span> ' . $r->customize1 . '</span>
                                        </div>';
                                }
                                if ($r->customize2) {
                                    $htmlmeetngreet .= '<div class="flex items-center gap-2">
                                        <i class="fa-solid fa-smile pr-1" style="color:#f8bf12;"></i><span> ' . $r->customize2 . '</span>
                                        </div>';
                                }
                                $htmlmeetngreet .='</div>';
                    } else {
                        $htmlmeetngreet = '<div class="flex flex-col gap-2 mb-4 text-gray-700 text-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-handshake pr-1" style="color:#f8bf12;"></i> '.$item1.'
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-map-marker-alt pr-1" style="color:#f8bf12;"></i> '.$item2.'
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-building pr-1" style="color:#f8bf12;"></i> '.$item3.'
                                    </div>';
                                if ($r->customize1) {
                                    $htmlmeetngreet .= '<div class="flex items-center gap-2">
                                            <i class="fa-solid fa-smile pr-1" style="color:#f8bf12;"></i><span> ' . $r->customize1 . '</span>
                                        </div>';
                                }
                                if ($r->customize2) {
                                    $htmlmeetngreet .= '<div class="flex items-center gap-2">
                                        <i class="fa-solid fa-smile pr-1" style="color:#f8bf12;"></i><span> ' . $r->customize2 . '</span>
                                        </div>';
                                }
                                $htmlmeetngreet .='</div>';
                    }


                    $lbl_transfer_time = htmlspecialchars($r->transfer_time);
                    $lbl_distance_miles = htmlspecialchars($r->distance_miles);
                    $htmlmeetngreet = $htmlmeetngreet;
                    $productName = htmlspecialchars($productName);
                    $lbl_transfer_time = htmlspecialchars($lbl_transfer_time);
                    $lbl_distance_miles = htmlspecialchars($lbl_distance_miles);
                    $booknow_url = htmlspecialchars($booknow_url);
                    $price = $price;

                    // $price = (round($price,2));

                    $introduction = htmlspecialchars($introduction);
                    $information = htmlspecialchars($information);
                    $security_measures = htmlspecialchars($security_measures);
                    $departure_procedures = htmlspecialchars($departure_procedures);
                    $arrival_procedures = htmlspecialchars($arrival_procedures);
                    $transfers = htmlspecialchars($transfers);

                    $booknow_url = "profile.php?airport=$airport&promo_code=$code&promo_price=$promo_price&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime&traffic_source=$traffic_source";

                    if ($u_a == 0) {

                        $moreinfo = "<button class=\" moreInfoBtn absolute top-3 right-3 modal-button px-2 py-1 rounded-full text-sm font-bold shadow-md z-10 flex items-center gap-1 transition duration-200 ease-in-out bg-white text-gray-700 hover:bg-gray-100 active:bg-gray-700 active:text-white\" data-modal=\"modal1\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$cur$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers' data-name='$productName' data-logo='$r->logo' data-map='$r->map_link'>
                              <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"w-3 h-3\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" stroke-width=\"2\">
                                  <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z\" />
                              </svg>
                              More Info & Map 
                            </button>";


                        $strick_hash="$price$price$price";
                       
                        $hash = hash('sha256', $strick_hash);

                        $buy_now_button = ' <div class="flex gap-2 items-center"> <span class="w-full block font-bold text-lg text-center bg-gray-100 py-2 rounded-lg" style="color:#252654;"> ' . $cur . $price . '  To Pay  </span>
                                                <a href="profile.php?airport=' . $airport . '&promo_code=' . $code . '&promo_price=' . $promo_price . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime .'&traffic_source='.$traffic_source.'&var=' . $hash . '" class="w-full block py-2 rounded-lg text-center font-medium hover:scale-105 hover:shadow-lg transition transform duration-300 ease-in-out" 
                            style="background-color: #252654; color:#ffffff;" onmouseover="this.style.backgroundColor=\'#f8bf12\'; this.style.color=\'#252654\';" onmouseout="this.style.backgroundColor=\'#252654\'; this.style.color=\'#ffffff\';"></span>Book Now</a>
                                            </div>';
                    } else {


                        $moreinfo = "";
                        $buy_now_button = "<p> <b> Not Available </b></p>";

                    }
                    $priceHtml='<span class="font-semibold" style="color:#252654;">' . $cur . $price . '</span>';
                    if ($change_price) {
                        $priceHtml = '<div>
                             <span class="text-red-600 font-bold line-through text-base">' . $cur . ($price+$promo_price) . '</span>
                             <span class="ml-2 text-green-600 font-bold text-base">'.$change_price.'</span>
                        </div>';
                    }
                    
                    $producType = ($lang == 'ar')? 'خدمة النقل مشمولة':"Shuttle Included";
                    if ($r->product_type =='Meet & Greet') {
                        $producType =($lang == 'ar')? "في المحطة":"At Terminal";
                    }
                    // style="background-image: url(https://globalparkingtech.co.uk/logos/products/' . trim($r->logo) . ');"
                    if (trim($r->logo) == "" || trim($r->logo) == "na") {
                        $product_html = '<div class="mb-4 overflow-hidden rounded-xl relative">
                                    <img src="assets/images/products/5.png" class="w-full h-64 object-cover transition-transform duration-[1000ms] ease-in-out hover:scale-110 bg-gray-100 p-2 rounded-lg" />
                                    '.$moreinfo.'
                                </div>
                                <div class="h-16 overflow-hidden mb-2">
                                 <h4 class="text-xl font-bold">' . $productName . '</h4>
                              </div>
                            <div class="flex justify-between items-center mb-4"> '.$priceHtml.'  <span class="text-gray-500 text-sm bg-gray-100 px-2 py-1 rounded">Shuttle Included</span> </div>';
                    } else {
                        $product_html = '<div class="mb-4 overflow-hidden rounded-xl relative">
                                    <img src="https://globalparkingtech.co.uk/logos/products/' . trim($r->logo) . '" class="w-full h-64 object-cover transition-transform duration-[1000ms] ease-in-out hover:scale-110 bg-gray-100 p-2 rounded-lg" />
                                    '.$moreinfo.'
                                </div>
                                <div class="h-16 overflow-hidden mb-2">
                                 <h4 class="text-xl font-bold">' . $productName . '</h4>
                              </div>
                            <div class="flex justify-between items-center mb-4"> '.$priceHtml.' <span class="text-gray-500 text-sm bg-gray-100 px-2 py-1 rounded"></span> '.$producType.'</div>';
                    }
                    
                    $html .='<div class="product-card relative bg-white rounded-2xl shadow-lg p-5 cursor-pointer h-full flex flex-col border-2 border-secondary" data-category="'.$r->product_type .'" data-price="'.$price.'" style="border: 2px solid #f8bf12;">
                        '. $product_html .'
                        ' . $htmlmeetngreet . '
                        <div class="border-t border-gray-200 mb-4"></div>
                            ' . $buy_now_button . '
                        </div>';
                }
            } //price check
        }


        return $html;

    }

    public function bookings()
    {

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Booking", "status" => "", "link" => false]
                ]
        ];

        return view('booking/bookings', $data);
    }


    public function booking_report_view()
    {

        $data = [
            "page_title" => "Booking",
            "breadcrumb" => [
                    ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                    ["href" => base_url('operators'), "title" => "Create Booking", "status" => "", "link" => false]
                ]
        ];
        return view('booking/report', $data);
    }

    public function bookings_report()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = $_GET['reference'];
        $surname = $_GET['surname'];
        $CarRegistration = $_GET['CarRegistration'];
        $Email = $_GET['Email'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';

        $DateFrom = strtotime($DateFrom);

        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);

        $DateTo = date('Y-m-d', $DateTo);
        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'reference',
            2 => 'surname',
            3 => 'carReg',
            4 => 'email',
            5 => 'booked_at',
            6 => 'depart_at'

        ];
        $sql_count = "SELECT count(*) as total FROM tbl_booking";
        $sql_data = "SELECT * FROM `tbl_booking`";
        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'created_at') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        } else {
            if (!empty($reference) or !empty($surname) or !empty($CarRegistration) or !empty($Email)) {

                $condition .= "  where  (reference='$reference'  or surname='$surname' or carReg='$CarRegistration' or email='$Email') and (booked_at>='$DateFrom' or depart_at<='$DateTo')";
            }
        }

        $sql_count = $sql_count . $condition;
        $sql_data = $sql_data . $condition;

        // return json_encode($sql_data);

        // exit;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = " " . $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));



            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";

            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = $result2->description;



            $row[] = $operator_name;
            $row[] = $value->reference;
            $row[] = $value->price;
            $row[] = $value->surname;
            $row[] = $value->airport;
            $row[] = $value->carReg;
            $row[] = $value->created_at;
            $row[] = $value->booked_at;
            $row[] = $value->depart_at;
            if ($value->status == 1) {
                $badge = "badge badge-glow bg-success";
                $row[] = "<span class='$badge'>Completed</span>";
            } elseif ($value->status == 0) {
                $badge = "badge badge-glow bg-warning";
                $row[] = "Pending";
                $row[] = "<span class='$badge'>Pending</span>";
            } else {
                $badge = "badge badge-glow bg-danger";
                $row[] = "<span class='$badge'>Cancelled</span>";
            }
            $row[] = $value->source;
            // $badge="";            
            // $id=id_en($value->id);
            // $action="<div class=\"btn-group\">
            //     <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            //     Actions
            //     </a>
            //     <div class=\"dropdown-menu\">
            //       <a class=\"dropdown-item\" href=".base_url("products/edit?id=".urlencode($id))."><i data-feather=\"edit\"></i> Edit</a>

            //       <a class=\"dropdown-item\" href=".base_url("products/range?id=".urlencode($id))."><i data-feather='list'></i> Manage Range</a>

            //       <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
            //     </div>
            //   </div>";
            // $row[] = $action;
            $data[] = $row;
        }
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }


    public function get_domain()
    {
        $code = $_GET['code'];
        $sql_data = "SELECT * FROM `tbl_websites` WHERE `id`='$code' LIMIT 1";
        $result = $this->db->query($sql_data)->getResult();
        return $this->setResponseFormat('json')->respond($result);
    }

    // Go Comperision
    public function get_go_booking()
    {
        // Validate access token
        $accessToken = $this->request->getGet('access_token');
        $reference = $this->request->getGet('reference_no');
        
        // Define valid token (store in config for better security)
        $validToken = '5MEsB9lLwVqu4qndXvEUE428bqGZY';
        
        // Check access token
        if (!$accessToken || $accessToken !== $validToken) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Access token is invalid',
                'code' => 401
            ]);
        }
        
        // Check reference number
        if (!$reference) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Reference number is required',
                'code' => 400
            ]);
        }
        
        try {
            // Use Query Builder for security and readability
            $result = $this->db->table('tbl_booking')
                              ->where('reference', $reference)
                              ->get()
                              ->getRow();
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $result,
                    'code' => 200
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Booking not found',
                    'reference' => $reference, // Helpful for debugging
                    'code' => 404
                ]);
            }
            
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Booking API Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Internal server error',
                'code' => 500
            ]);
        }
    }

    public function update_go_booking()
    {
        if (isset($_GET['access_token']) && $_GET['access_token'] == '5MEsB9lLwVqu4qndXvEUE428bqGZY') 
        {
            $reference = isset($_GET['reference']) ? $_GET['reference'] : '';

            $carReg = isset($_GET['Car_Registration']) ? $_GET['Car_Registration'] : '';
            $carMake = isset($_GET['Car_Manufacturer']) ? $_GET['Car_Manufacturer'] : '';
            $carModel = isset($_GET['Car_Model']) ? $_GET['Car_Model'] : '';
            $carColour = isset($_GET['Car_Colour']) ? $_GET['Car_Colour'] : '';
            $passenger = isset($_GET['passenger']) ? $_GET['passenger'] : '';

            $required_OutTerminal = isset($_GET['Departure_Terminal']) ? $_GET['Departure_Terminal'] : '';
            $required_RetTerminal = isset($_GET['Return_Terminal']) ? $_GET['Return_Terminal'] : '';
            $email = isset($_GET['Email']) ? $_GET['Email'] : '';
            $contactNumber = isset($_GET['Contact_Number']) ? $_GET['Contact_Number'] : '';
            $Departure_Flight_Number = isset($_GET['Departure_Flight_Number']) ? $_GET['Departure_Flight_Number'] : '';
            $Return_Flight_Number = isset($_GET['Return_Flight_Number']) ? $_GET['Return_Flight_Number'] : '';

            $arrival_date = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';
            $departure_date = isset($_GET['changedDate']) ? $_GET['changedDate'] : '';
            $formattedTimearrivalTime = isset($_GET['arrivalTime']) ? $_GET['arrivalTime'] : '';
            $formattedTimedepartureTime = isset($_GET['departureTime']) ? $_GET['departureTime'] : '';

            // $arrival_date = strtotime($arrival_date);
            // $arrival_date = date('Y-m-d', $arrival_date);
            // $departure_date = strtotime($departure_date);
            // $departure_date = date('Y-m-d', $departure_date);
            $arrival_date = "$arrival_date $formattedTimedepartureTime:00";
            $departure_date = "$departure_date $formattedTimearrivalTime:00";

            $created_at = isset($_GET['created_at']) ? $_GET['created_at']: '';
            $status = isset($_GET['status']) ? $_GET['status']: '';
            $booking_type = isset($_GET['booking_type']) ? $_GET['booking_type']: '';

            if (strpos("completed", $status) !== false) {
                $status=1;
            } elseif(strpos("cancelled", $status) !== false) {
                $status=2;
            } else{
                $status=0;
            }

            $result_check = 0;
       

            $validationRules = [
                'Car_Registration' => 'required',
                'Car_Manufacturer' => 'required',
                'Car_Model' => 'required',
                'Car_Colour' => 'required',
                'Contact_Number' => 'required',
                'Email' => 'required|valid_email'
            ];

            // Apply the validation rules to the incoming data.
            $this->validation->setRules($validationRules);

            // Run the validation against the data.
            if (!$this->validation->withRequest($this->request)->run()) {
                // Validation failed, get the validation errors.
                $errors = $this->validation->getErrors();
                echo json_encode(['code' => 0, 'error' => $errors]);
            } else {
                // Validation passed, continue with your logic.
               
                $sql_data = "UPDATE tbl_booking 
                        SET 
                            carReg = '$carReg',
                            carMake = '$carMake',
                            carModel = '$carModel',
                            carColour = '$carColour',
                            OutTerminal = '$required_OutTerminal',
                            RetTerminal = '$required_RetTerminal',
                            email = '$email',
                            contactNumber = '$contactNumber',
                            depart_at = '$arrival_date',
                            return_at = '$departure_date',
                            status = '$status',
                            InFltNo = '$Departure_Flight_Number',
                            OutFltNo = '$Return_Flight_Number',
                            passenger = '$passenger',
                            booking_type = '$booking_type',
                            created_at = '$created_at',
                            booked_at = '$created_at'
                        WHERE 
                            reference = '$reference'";
                $result = $this->db->query($sql_data);

                if ($result) {
                    echo json_encode(['code' => 1, 'msg' => "Data updated successfully", 'ref_id' => "$reference"]);
                    exit();
                } else {

                    echo json_encode(['code' => 0, 'error' => "not updated"]);
                }
            }
        }
    }
    // Subscriber
    public function subscriber()
    {
        // Get parameters
        $email = $this->request->getGet('email');
        $accessToken = $this->request->getGet('access_token');
        
        // Validate access token
        if ($accessToken !== '5MEsB9lLwVqu4qndXvEUE428bqGZY') {
            return $this->response->setJSON([
                'status' => false, 
                'message' => "Access token is invalid"
            ]);
        }
        
        // Validate email
        if (empty($email)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => "Email is required"
            ]);
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => "Invalid email format"
            ]);
        }
        
        try {
            // Check if email already exists
            $existing = $this->db->table('tbl_subscriber')
                                ->where('email', $email)
                                ->get()
                                ->getRow();
            
            if ($existing) {
                return $this->response->setJSON([
                    'status' => false, 
                    'message' => "Email already subscribed"
                ]);
            }
            
            // Prepare data
            $data = [
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert record
            $result = $this->db->table('tbl_subscriber')->insert($data);
            
            if ($result) {
                // Get inserted data
                $insertedId = $this->db->insertID();
                $subscriberData = $this->db->table('tbl_subscriber')
                                          ->where('id', $insertedId)
                                          ->get()
                                          ->getRow();
                
                return $this->response->setJSON([
                    'status' => true, 
                    'message' => "Successfully subscribed",
                    'data' => $subscriberData
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false, 
                    'message' => "Failed to subscribe"
                ]);
            }
            
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Subscriber error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => false, 
                'message' => "An error occurred. Please try again."
            ]);
        }
    }
    
}