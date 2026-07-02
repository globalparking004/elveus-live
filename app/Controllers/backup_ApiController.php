<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use ValueError;

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

public function sagepay()
{


    $sql_email_data="select * from tbl_booking where transaction_id='8F310C32-A0C1-B683-2B72-0BB53CE8BA9F'";
    $sql_email_data_r = $this->db->query($sql_email_data)->getRow();
    echo $book_id=$sql_email_data_r->id;

    echo $email=$sql_email_data_r->email;
    $from="bookings@globalparkingmanagement.co.uk";

    $webtype=$_GET['webtype'];

    // $res=send_email($email, "Your Parking Booking Confirmation", $book_id,$from,$webtype);




}



    public function insert_random_strings($length_of_string="")
    {   
        $sql = "SELECT reference FROM tbl_reference";
        $result = $this->db->query($sql)->getRow();
        $reference=$result->reference;
        $reference=strval($reference)+1;
        $sql ="UPDATE tbl_reference SET reference='$reference'";
        $this->db->query($sql);
        return $reference;

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
        $message='<!DOCTYPE html>
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
            <td>'.$enquiry_type.'</td>
        </tr>
        <tr>
            <td><strong>Sent On:</strong></td>
            <td>'.$longDateFormat.'</td>
        </tr>
        <tr>
            <td><strong>Website:</strong></td>
            <td>'.$website.'</td>
        </tr>
        <tr>
            <td><strong>Name:</strong></td>
            <td>'.$title.' '.$first_name.' '.$surname.'</td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td><a href="mailto:'.$email.'">'.$email.'</a></td>
        </tr>
        <tr>
            <td><strong>Contact Number:</strong></td>
            <td>'.$number.'</td>
        </tr>
        <tr>
            <td><strong>Booking Reference:</strong></td>
            <td>'.$ref.'</td>
        </tr>
    </table>
</body>
</html>';
        $to="support@dublinairportparkandfly.com";
        $subject="Contact Request From ".$website;
        $result=send_single_email($to,$subject,$message);
        if($result)
        {
            $response=['status'=>true,"message"=>"email successfully sent"];
        }else{
            $response=['status'=>false,"message"=>"unable to send contact us email"];
        }
        echo json_encode($response);
        exit();
    }

    private function generateUniqueTimestamp() {
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

            $new_price = isset($_GET['price']) ? $_GET['price'] : '';
            $new_reference = isset($_GET['new_reference']) ? $_GET['new_reference'] : "";
            $required_OutTerminal = isset($_GET['Departure_Terminal']) ? $_GET['Departure_Terminal'] : '';
            $required_RetTerminal = isset($_GET['Return_Terminal']) ? $_GET['Return_Terminal'] : '';
            $firstName = isset($_GET['First_Name']) ? $_GET['First_Name'] : '';
            $surname = isset($_GET['Surname']) ? $_GET['Surname'] : '';
            $email = isset($_GET['Email']) ? $_GET['Email'] : '';
            $contactNumber = isset($_GET['Contact_Number']) ? $_GET['Contact_Number'] : '';
            $agent = isset($_GET['Departure_Flight_Number']) ? $_GET['Departure_Flight_Number'] : '';
            $rdate = time();
            $arrival_date = isset($_GET['selectedDate']) ? $_GET['selectedDate'] : '';
            $departure_date = isset($_GET['changedDate']) ? $_GET['changedDate'] : '';
            $formattedTimearrivalTime = isset($_GET['arrivalTime']) ? $_GET['arrivalTime'] : '';
            $formattedTimedepartureTime = isset($_GET['departureTime']) ? $_GET['departureTime'] : '';
            $promoCode = isset($_GET['promoCode']) ? $_GET['promoCode'] : '';

            $promo_price = isset($_GET['promo_price']) ? $_GET['promo_price'] : '';
            $arrival_date = strtotime($arrival_date);
            $arrival_date = date('Y-m-d', $arrival_date);
            $departure_date = strtotime($departure_date);
            $departure_date = date('Y-m-d', $departure_date);
            $arrival_date = "$arrival_date $formattedTimearrivalTime:00";
            $departure_date = "$departure_date $formattedTimedepartureTime:00";

            // print_r($arrival_date);
            // exit();

            // exit("testing");


            $operatorid = isset($_GET['operator_id']) ? $_GET['operator_id'] : '';

            if (empty($new_reference)) {

                $new_reference = "GL-$airport-".$this->insert_random_strings(10);
            }
            if (empty($new_price)) {

            }

            $currentTimestamp = time();
            $formattedDate = date("Y-m-d H:i:s", $currentTimestamp);


            $result_check = 0;
            if (!empty($promoCode)) {
                
                $query = $this->db->query("SELECT * FROM tbl_promotion_code WHERE code = '$promoCode' AND website like '$website'");
                $result_check = $query->getRow();
                if($result_check){

                }else{
                    $promoCode="";
                    $website=$website;

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

                $sql_data = "INSERT INTO tbl_booking 
                        (price, reference, carReg, carMake, carModel, carColour, OutTerminal, RetTerminal, 
                        firstName, surname, email, contactNumber ,product_id,airport,booked_at,depart_at,source,return_at,status,operator_id,promocode,promo_price) 
                        VALUES 
                        ('$new_price', '$new_reference', '$carReg', '$carMake', '$carModel', '$carColour', '$required_OutTerminal', '$required_RetTerminal', 
                        '$firstName', '$surname', '$email', '$contactNumber',$id,'$airport','$formattedDate','$arrival_date','$website','$departure_date','0','$operatorid','$promoCode','$promo_price');
                        ";
                $result = $this->db->query($sql_data);
                $booking_last_inserted_id = $this->db->insertID();

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


    public function checkStatus()
    {
        $transaction_Id = isset($_GET['transaction_Id']) ? $_GET['transaction_Id'] : '';

         $sql_data = "select status from  tbl_booking where transaction_id ='$transaction_Id'";
        $result = $this->db->query($sql_data)->getRow();
        // echo json_encode($result);




// print_r($result);
if(isset($result->status) && ($result->status)==1)
{
    
   echo json_encode($result->status);
    exit;

}else{

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

            if(isset($_GET['trans_id']) && !empty($_GET['trans_id']))
            {

                $sql_data = "update tbl_booking set status='$status' where transaction_id ='$transaction_id' ";


                $sql_email_data="select * from tbl_booking where transaction_id='$transaction_id'";
                $sql_email_data_r = $this->db->query($sql_email_data)->getRow();
                $book_id=$sql_email_data_r->id;
            
                $email=$sql_email_data_r->email;
                $from="bookings@globalparkingmanagement.co.uk";
            
                $webtype=$_GET['webtype'];
            
                $res=send_email($email, "Your Parking Booking Confirmation", $book_id,$from,$webtype);





            }else{

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
            if(isset($_GET['from']))
            {
                $from = $_GET['from'];
            }else{
                $from = "bookings@globalparkingmanagement.co.uk";
            }
            if(isset($_GET['webtype']))
            {
                $webtype=$_GET['webtype'];
            }else{
                $webtype="Cruise Ports";
            }            
            $booking_id = $_GET['booking_last_inserted_id'];
            $from="bookings@globalparkingmanagement.co.uk";
            $res=send_email($email, "Your Parking Booking Confirmation", $booking_id,$from,$webtype);
            print_r($res);
        }
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
            $date1 = strtotime($selectedDate);
            $date2 = strtotime($changedDate);
            $number_of_days = floor(($date2 - $date1) / (60 * 60 * 24));
            $number_of_days = $number_of_days + 1;
            $dateString = $selectedDate;
            $date = strtotime($dateString);
            $dayName = strtolower(date('l', $date));
            $formated_arrive_date = date('Y-m-d', $date);
            $changedDate = strtotime($changedDate);
            $changedDate = date('Y-m-d', $changedDate);
            $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';

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
            $array=[];

           

            // echo"...";

            foreach ($result as $r) {

                

                $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";

                // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


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
                            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";

                            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $product_code_r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";


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

                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                            $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id";
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
                            if (!empty($capacity_threshold_two)) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by product


                        if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                            $sql_data = "SELECT count(*) as operator_capacity tbl_booking FROM `tbl_booking` WHERE `operator_id`= $r->operator_id";
                            $result = $this->db->query($sql_data)->getRow();
                            $operator_capacity = $result->operator_capacity;



                            $sql_data = "SELECT  * tbl_booking FROM `tbl_booking` WHERE `id`= $r->operator_id";
                            $result = $this->db->query($sql_data)->getRow();
                            $capacity = $result->capacity;


                            $percentage_of_capacity = $operator_capacity / $capacity * 100;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;
                            if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                                $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                                $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                            }
                            if (!empty($capacity_threshold_two)) {

                                $amount_to_add = 0;
                                $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                                $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                            }

                            $price = $price + $amount_to_add;
                        } //by operator


                        $information = "$r->information";
                        $introduction = "$r->introduction";
                        $security_measures = "$r->security_measures";
                        $departure_procedures = "$r->departure_procedures";
                        $productName = "$r->name";
                        $arrival_procedures = strip_tags($r->arrival_procedures);
                        $transfers = "$r->transfers";

                        $booknow = '  <a href="provide-detail.php??airport='.$airport.'&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '"><button type="submit"
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

                            $price = (round($price,2));
                            $promo_price = (round($promo_price,2));

                            $change_price = "You Save $cur$promo_price";

                        } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                            $code_price = $get_promotion_code_data_r->amount;

                            $promo_price = $price * $code_price / 100;

                            $price = $price - $promo_price;

                            $price = (round($price,2));
                            $promo_price = (round($promo_price,2));

                            $change_price = "You Save $cur$promo_price";


                        }


                        $array[] = array(

                            "price" => "$price",
                            "id" => "$r->id"


                        );



                        // exit;

                    }
                } //price check
            }

         

            // Sort the array by price in ascending order
            usort($array, function($a, $b) {
                return $a['price'] - $b['price'];
            });

            // print_r($array);
            $desc_html = "";
            foreach ($array as $data) {

                 $price = $data['price'];
                 $id = $data['id'];


                $desc_html .= $this->get_sorted_price_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $number_of_days, $code, $website, $dayName, $selectedDate,$cur,$webtype,$airport);

            }





            if (empty($desc_html)) {

                // echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                return $desc_html;
            } else {

                return ($desc_html);
            }
        }
    }






    public function get_sorted_price_html($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $number_of_days, $code, $website, $dayName, $selectedDate,$cur='£',$webtype,$airport)
    {

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

            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";
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
                        $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $product_code_r->id AND `dfrom` >= $formated_arrive_date OR `dto` <= $changedDate limit 1";
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

                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {


                        $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id";
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
                        if (!empty($capacity_threshold_two)) {

                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                    } //by product


                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__operator_capacity) {


                        $sql_data = "SELECT count(*) as operator_capacity tbl_booking FROM `tbl_booking` WHERE `operator_id`= $r->operator_id";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacity;



                        $sql_data = "SELECT  * tbl_booking FROM `tbl_booking` WHERE `id`= $r->operator_id";
                        $result = $this->db->query($sql_data)->getRow();
                        $capacity = $result->capacity;


                        $percentage_of_capacity = $operator_capacity / $capacity * 100;

                        $capacity_threshold_one = $r->capacity_threshold_one;

                        $capacity_threshold_two = $r->capacity_threshold_two;
                        if (!empty($capacity_threshold_one) && $percentage_of_capacity >= $capacity_threshold_one) {

                            $capacity_threshold_one_increase = $r->capacity_threshold_one_increase;
                            $amount_to_add = $price * ($capacity_threshold_one_increase / 100);
                        }
                        if (!empty($capacity_threshold_two)) {

                            $amount_to_add = 0;
                            $capacity_threshold_two_increase = $r->capacity_threshold_two_increase;
                            $amount_to_add = $price * ($capacity_threshold_two_increase / 100);
                        }

                        $price = $price + $amount_to_add;
                    } //by operator


                    $information = $r->information;
                    $introduction = $r->introduction;
                    $security_measures = $r->security_measures;
                    $departure_procedures = $r->departure_procedures;
                    $productName = $r->name;
                    $arrival_procedures = $r->arrival_procedures;
                    $transfers = $r->transfers;

                    $booknow = '  <a href="provide-detail.php?airport='.$airport.'&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '"><button type="submit"
                                name="Check-Availability"
                                class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                    class="glyphicon glyphicon-circle-arrow-right"
                                    aria-hidden="true"></span>Book Now</button></a>';

                    $booknow = strip_tags($booknow);

                    $booknow_url="provide-detail.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=".urlencode($r->name)."&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime";

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

                        $price = (round($price,2));
                        $promo_price = (round($promo_price,2));
                        $change_price = "You Save $cur$promo_price";

                    } elseif (isset($get_promotion_code_data_r->type) && $get_promotion_code_data_r->type == 'Percentage') {

                        $code_price = $get_promotion_code_data_r->amount;

                        $promo_price = $price * $code_price / 100;

                        $price = $price - $promo_price;

                        $price = (round($price,2));
                        $promo_price = (round($promo_price,2));
                        $change_price = "You Save $cur$promo_price";


                    }


                    $array[] = array(

                        "price" => "$price",
                        "id" => "$r->id"


                    );



                    // exit;
                    if($r->park_mark)
                    {
                        $htmlmeetngreet='<div class="col-sm-6">
                                <ul class="small p-0 temp1-mt-4 m-0 text-med-gray no-bullets parkride">

                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fab fa-product-hunt pr-1"></i> Park and Ride
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fas fa-map-marker-alt pr-1"></i> ' . $r->distance_miles . ' miles from the
                                         '.$webtype.'
                                    </li>
                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="fas fa-bus pr-1"></i> ' . $r->transfer_time . ' minutes transfer
                                    </li>

                                    <li class="my-2 no-wrap d-none d-sm-block">
                                        <i class="far fa-smile pr-1"></i><span> Can be cancelled</span>
                                    </li>
                                </ul>
                            </div>';
                        }else{
                            $htmlmeetngreet='<div class="col-sm-6 pl-0">
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
                                    </li>
                                </ul>
                            </div>';
                        }
                    

$lbl_transfer_time=htmlspecialchars($r->transfer_time);
$lbl_distance_miles=htmlspecialchars($r->distance_miles);
$htmlmeetngreet=$htmlmeetngreet;
$productName=htmlspecialchars($productName);
$lbl_transfer_time=htmlspecialchars($lbl_transfer_time);
$lbl_distance_miles=htmlspecialchars($lbl_distance_miles);
$booknow_url=htmlspecialchars($booknow_url);
$price=$price;

// $price = (round($price,2));

$introduction=htmlspecialchars($introduction);
$information=htmlspecialchars($information);
$security_measures=htmlspecialchars($security_measures);
$departure_procedures=htmlspecialchars($departure_procedures);
$arrival_procedures=htmlspecialchars($arrival_procedures);
$transfers=htmlspecialchars($transfers);

$moreinfo="<button class=\"btn btn-outline-dark buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\" type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$cur$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers'>More Info</button>";
                    

                    //$moreinfo="<button type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers' class=\"btn btn-outline-dark buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\"><span class=\"glyphicon glyphicon-plus\" aria-hidden="true"></span>More Info</button>";
                    if(trim($r->logo)=="" || trim($r->logo)=="na")
                    {
                        $product_html='<div class="row result-box-title">
                            <div class="col align-self-center">
                                <h4 class="mt-2 bx-hd">' . $r->name . '</h4>
                            </div>
                            <div class="col-3">
                                <div class="result-box-logo">
                                   <img src="https://globalparkingtech.co.uk/logos/products/'.trim($r->logo).'">
                                </div>
                            </div>
                        </div>';
                    }else{
                        $product_html='<div class="row result-box-title">
                            <div class="col align-self-center">
                                <h4 class="mt-2 bx-hd">' . $r->name . '</h4>
                            </div>
                            <div class="col-3">
                                <div class="result-box-logo">
                                    <img src="https://globalparkingtech.co.uk/logos/products/'.trim($r->logo).'">
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



                        '.$product_html.'


                        <div class="seperator mb-3 mt-2">
                            <span class="line mr-2 bg-color"></span><span class="dots bg-color"></span>
                        </div>

                        <div class="row">
                            '.$htmlmeetngreet.'
                            <div class="col-sm-6">
                                <div class="row">
                              
                                    <div class="col-sm-12">
                                        <h2 class="tt_resultPrice-resp"><strong>'.$cur.$price . '</strong></h2>
                                    </div>
                                    <div class="col-12 p-0 m-0 text-sm-center">
                                        <strong><p class="c">' . $change_price . '</p></strong>
                                    </div> 
                                    <div class="col-sm-12">
                                        <a href="provide-detail.php?airport='.$airport.'&promo_code=' . $code . '&promo_price=' . $promo_price . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '"><button type="submit"
                                                name="Check-Availability"
                                                class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                                    class="glyphicon glyphicon-circle-arrow-right"
                                                    aria-hidden="true"></span>Book Now</button></a>
                                    </div>
                                    <div class="col-sm-12">
                                        '.$moreinfo.'
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
        $code=$_GET['code'];
        $sql_data = "SELECT * FROM `tbl_websites` WHERE `id`='$code' LIMIT 1";
        $result = $this->db->query($sql_data)->getResult();
        return $this->setResponseFormat('json')->respond($result);
    }
}