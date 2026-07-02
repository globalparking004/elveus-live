<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use ValueError;

class supplier_api_controller extends BaseController
{
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    public function __construct()
    {
        $this->Users = new UsersModel;
        $this->Roles = new RolesModel;
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
    }



    public function update_product_Booking()
    {
        if (!isset($_GET['access_token']) or empty($_GET['access_token'])) {

            echo json_encode(['code' => 0, 'error' => "access_token is missing"]);
            exit;
        } else {
            $access_token = $_GET['access_token'];
        }

        $token = "SELECT * FROM `tbl_supplier` WHERE `access_token`='$access_token' and status='1'";
        $token_r = $this->db->query($token)->getResult();

        $count = count($token_r);

        if (isset($count) && $count > 0) {
            if (isset($_GET['reference']) && !empty($_GET['reference'])) {
                $id = $_GET['reference'];
            } else {
                echo json_encode(['code' => 0, 'error' => "Reference number is required"]);
                exit;
            }

            // Fetch current data
            $sql_data_get_info = "SELECT * FROM `tbl_booking` WHERE `reference`='$id'";
            $current_data = $this->db->query($sql_data_get_info)->getRow();

            if (!$current_data) {
                echo json_encode(['code' => 0, 'error' => "reference is invalid"]);
                exit;
            }

            // Collecting data to be updated
            $update_data = [];
            $fields = [
                'Car_Registration' => 'carReg',
                'Car_Manufacturer' => 'carMake',
                'Car_Model' => 'carModel',
                'Car_Colour' => 'carColour',
                'Departure_Terminal' => 'OutTerminal',
                'Return_Terminal' => 'RetTerminal',
                'opitech_agent' => 'agent_id',
                'price' => 'price',
                'First_Name' => 'firstName',
                'Surname' => 'surname',
                'Email' => 'email',
                'Contact_Number' => 'contactNumber',
                'Departure_Flight_Number' => 'InFltNo',
                'Return_Flight_Number' => 'OutFltNo',
                'DepartureDate' => 'depart_at',
                'Returndate' => 'return_at',
                'promoCode' => 'promocode',
                'promo_price' => 'promo_price',
                'passenger' => 'passenger',
                'status'=>'status'
            ];

            foreach ($fields as $param => $db_field) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    if ($db_field === 'depart_at' || $db_field === 'return_at') {
                        $date_value = $_GET[$param];
                        if($db_field === 'depart_at')
                        {
                            $time_value = isset($_GET['Departuretime']) ? $_GET['Departuretime'] : '00:00';
                        }else{
                            $time_value = isset($_GET['ReturnTime']) ? $_GET['ReturnTime'] : '00:00';
                        }
                        $update_data[$db_field] = date('Y-m-d H:i:s', strtotime("$date_value $time_value"));
                    } else {
                        $update_data[$db_field] = $_GET[$param];
                    }
                }
            }


            // Perform the update
            $this->db->table('tbl_booking')->where('reference', $id)->update($update_data);

            if ($this->db->affectedRows() > 0) {
                echo json_encode(['code' => 1, 'msg' => "Data updated successfully"]);
            } else {
                echo json_encode(['code' => 0, 'error' => "No changes were made"]);
            }

        } else {
            echo json_encode(['code' => 0, 'error' => "Not Authorized"]);
            exit;
        }
    }


    public function index()
    {

        if (!isset($_GET['access_token']) or empty($_GET['access_token'])) {

            echo json_encode(['code' => 0, 'error' => "access_token is missing"]);
            exit;
        } else {
            $access_token = $_GET['access_token'];
        }

        $token = "SELECT * FROM `tbl_supplier` WHERE `access_token`='$access_token' and status='1'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $token_r = $this->db->query($token)->getResult();

        $count = count($token_r);

        // print_r($count);

        // exit;



        if (isset($count) && $count > 0) {

            if (isset($_GET['product_code']) and !empty($_GET['product_code'])) {
                $id = isset($_GET['product_code']) ? $_GET['product_code'] : '';
            } else {

                echo json_encode(['code' => 0, 'error' => "Product code is required"]);
                exit;
            }

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
            $arrival_date = isset($_GET['DepartureDate']) ? $_GET['DepartureDate'] : '';
            $departure_date = isset($_GET['ReturnDate']) ? $_GET['ReturnDate'] : '';
            $formattedTimearrivalTime = isset($_GET['Departuretime']) ? $_GET['Departuretime'] : '';
            $formattedTimedepartureTime = isset($_GET['ReturnTime']) ? $_GET['ReturnTime'] : '';
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


            // $operatorid = isset($_GET['operator_id']) ? $_GET['operator_id'] : '';

            $sql_data_get_info = "SELECT * FROM `tbl_products` WHERE `product_code`='$id'";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $sql_data_get_info_res = $this->db->query($sql_data_get_info)->getRow();



            if (!isset($sql_data_get_info_res->operator_id)) {

                echo json_encode(['code' => 0, 'error' => "Product code  is invalid"]);
                exit;

            } else {

                $operatorid = $sql_data_get_info_res->operator_id;

            }

            $p_id = $sql_data_get_info_res->id;

            if (empty($new_reference)) {

                $new_reference = "Supp-$airport-" . $this->insert_random_strings(10);
            }
            if (empty($new_price)) {

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

                $sql_data = "INSERT INTO tbl_booking 
                        (price, reference, carReg, carMake, carModel, carColour, OutTerminal, RetTerminal, agent_id,
                        firstName, surname, email, contactNumber ,product_id,airport,booked_at,depart_at,source,return_at,status,operator_id,promocode,promo_price,InFltNo,OutFltNo, passenger, show_status,third_party_rec,booking_type) 
                        VALUES 
                        ('$new_price', '$new_reference', '$carReg', '$carMake', '$carModel', '$carColour', '$required_OutTerminal', '$required_RetTerminal', '$agent',
                        '$firstName', '$surname', '$email', '$contactNumber',$p_id,'$airport','$formattedDate','$arrival_date','$website','$departure_date','1','$operatorid','$promoCode','$promo_price','$Departure_Flight_Number','$Return_Flight_Number', '$passenger', '1','1','Online');
                        ";
                $result = $this->db->query($sql_data);
                $booking_last_inserted_id = $this->db->insertID();

                $arrival_date = isset($_GET['DepartureDate']) ? $_GET['DepartureDate'] : '';
                $departure_date = isset($_GET['ReturnDate']) ? $_GET['ReturnDate'] : '';


                if ($result) {
                    echo json_encode(['code' => 1, 'msg' => "Data updated successfully", 'ref_id' => "$new_reference", 'booking_last_inserted_id' => "$booking_last_inserted_id"]);
                    //header("Location: https://skyparkluton.co.uk/payment.php?price=$new_price&ref_id=$new_reference&email=$email&arrival_date=$arrival_date&departure_date=$departure_date&booking_last_inserted_id=$booking_last_inserted_id&arrival_time=$formattedTimearrivalTime&departure_time=$formattedTimedepartureTime");
                    exit();
                } else {

                    echo json_encode(['code' => 0, 'error' => "not saved"]);
                }
            }
        } else {

            echo "not Authorized";
            exit;
        }

    }


    public function sanitize_input($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    public function show_product_supplier()
    {

        if (!isset($_GET['access_token']) or empty($_GET['access_token'])) {

            echo json_encode(['code' => 0, 'error' => "access_token is missing"]);
            exit;
        } else {
            $access_token = $_GET['access_token'];
        }
        $token = "SELECT * FROM `tbl_supplier` WHERE `access_token`='$access_token' and status='1'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $token_r = $this->db->query($token)->getResult();

        $count = count($token_r);

        if (isset($count) && $count > 0) {


            ////////////////// validatation ////////////////////
            if (
                !isset($_GET['promotion_code']) ||
                !isset($_GET['airport']) ||
                !isset($_GET['DepartureDate']) ||
                !isset($_GET['ReturnDate']) ||
                !isset($_GET['Departuretime']) ||
                !isset($_GET['ReturnTime'])
            ) {

                // If any parameter is missing, exit with an error message
                exit('Error: All parameters (promotion_code, airport, DepartureDate, ReturnDate, Departuretime, ReturnTime) are required.');
            }



            $airport = $_GET['airport'];


            $sql_data_get_info = "SELECT * FROM `tbl_websites` WHERE `code`='$airport'";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $sql_data_get_info_res = $this->db->query($sql_data_get_info)->getRow();


            if (!isset($sql_data_get_info_res)) {

                echo json_encode(['code' => 0, 'error' => "Invalid Supplier Code"]);
                exit;
            }


            $webtype = $sql_data_get_info_res->type;
            $website = $sql_data_get_info_res->domain;
            $cur = $sql_data_get_info_res->cur;

            /////////////////////////////////////////////////////////////////////////////////

            $code = isset($_GET['promotion_code']) ? $this->sanitize_input($_GET['promotion_code']) : '';
            $supplier_code = isset($_GET['supplier_code']) ? $this->sanitize_input($_GET['supplier_code']) : '';
            $selectedDate = isset($_GET['DepartureDate']) ? $this->sanitize_input($_GET['DepartureDate']) : '';
            $changedDate = isset($_GET['ReturnDate']) ? $this->sanitize_input($_GET['ReturnDate']) : '';
            $arrivalTime = isset($_GET['Departuretime']) ? $this->sanitize_input($_GET['Departuretime']) : '';
            $departureTime = isset($_GET['ReturnTime']) ? $this->sanitize_input($_GET['ReturnTime']) : '';

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

            // $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            // $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////
            $arrivalTime = str_replace(":", "", $arrivalTime);
            $departureTime = str_replace(":", "", $departureTime);

            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if ($airport == 'DUB') {

                if ($arrivalTime < $departureTime) {

                    $number_of_days = $number_of_days + 1;

                }


            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time = "00:00";

            }

            // $arrivalTime = str_replace(":", "", $arrivalTime);
            // $departureTime = str_replace(":", "", $departureTime);

            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$supplier_code' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
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

                            //                             echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";// exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            //                             echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";// exit;



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
            $desc_html = array();
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html[] = $this->get_sorted_price_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a);

            }



            ///////////////////////////////////////////////////////////////////////////////////
            if (empty($desc_html)) {

                echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                // print_r($desc_html);
            } else {

                print_r(json_encode($desc_html));
            }
        } else {

            echo "Authentication Failed";

        }
    }

    public function show_product_supplier1()
    {

        if (!isset($_GET['access_token']) or empty($_GET['access_token'])) {

            echo json_encode(['code' => 0, 'error' => "access_token is missing"]);
            exit;
        } else {
            $access_token = $_GET['access_token'];
        }
        $token = "SELECT * FROM `tbl_supplier` WHERE `access_token`='$access_token' and status='1'";
        // $sql_data = "SELECT * FROM `tbl_products`";
        $token_r = $this->db->query($token)->getResult();

        $count = count($token_r);

        if (isset($count) && $count > 0) {


            ////////////////// validatation ////////////////////
            if (
                !isset($_GET['promotion_code']) ||
                !isset($_GET['airport']) ||
                !isset($_GET['DepartureDate']) ||
                !isset($_GET['ReturnDate']) ||
                !isset($_GET['Departuretime']) ||
                !isset($_GET['ReturnTime'])
            ) {

                // If any parameter is missing, exit with an error message
                exit('Error: All parameters (promotion_code, airport, DepartureDate, ReturnDate, Departuretime, ReturnTime) are required.');
            }



            $airport = $_GET['airport'];


            $sql_data_get_info = "SELECT * FROM `tbl_websites` WHERE `code`='$airport'";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $sql_data_get_info_res = $this->db->query($sql_data_get_info)->getRow();

            if (!isset($sql_data_get_info_res)) {

                echo json_encode(['code' => 0, 'error' => "Invalid Supplier Code"]);
                exit;
            }


            $webtype = $sql_data_get_info_res->type;
            $website = $sql_data_get_info_res->domain;
            $cur = $sql_data_get_info_res->cur;

            /////////////////////////////////////////////////////////////////////////////////

            $code = isset($_GET['promotion_code']) ? $this->sanitize_input($_GET['promotion_code']) : '';
            $supplier_code = isset($_GET['supplier_code']) ? $this->sanitize_input($_GET['supplier_code']) : '';
            $selectedDate = isset($_GET['DepartureDate']) ? $this->sanitize_input($_GET['DepartureDate']) : '';
            $changedDate = isset($_GET['ReturnDate']) ? $this->sanitize_input($_GET['ReturnDate']) : '';
            $arrivalTime = isset($_GET['Departuretime']) ? $this->sanitize_input($_GET['Departuretime']) : '';
            $departureTime = isset($_GET['ReturnTime']) ? $this->sanitize_input($_GET['ReturnTime']) : '';

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

            // $cur = isset($_GET['cur']) ? $_GET['cur'] : '£';
            // $webtype = isset($_GET['webtype']) ? $_GET['webtype'] : 'Cruise Ports';


            $date1 = new \DateTime($selectedDate);
            $date2 = new \DateTime($changedDate);
            $interval = $date1->diff($date2);
            $number_of_days = $interval->format('%a') + 1;

            ///////////////////////////////////// time limiter in products //////////////////////////////////////
            $arrivalTime = str_replace(":", "", $arrivalTime);
            $departureTime = str_replace(":", "", $departureTime);

            // $inputString = "0330";
            $timeFormat = $this->convertToTimeFormat($arrivalTime);


            $providedDateTime = "$formated_arrive_date $timeFormat";
            $timeDifference = $this->getTimeDifference($providedDateTime);

            $timeDifference = $timeDifference['hours'];


            //////////////////////////////////////////////////////////////////////////////////////////////////

            if ($airport == 'DUB') {

                if ($arrivalTime < $departureTime) {

                    $number_of_days = $number_of_days + 1;

                }
            }

            $formatted_departureTime_Time = $this->formatTime($arrivalTime);

            if ($formatted_departureTime_Time !== false) {
                // echo $formatted_departureTime_Time; // Outputs: 01:30

            } else {
                $formatted_departureTime_Time = "00:00";

            }

            // $arrivalTime = str_replace(":", "", $arrivalTime);
            // $departureTime = str_replace(":", "", $departureTime);

            ///////////////////////////////////////////////////////////////////////////////////////////////////

            $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$supplier_code' AND `airport`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) order by id desc";
            // $sql_data = "SELECT * FROM `tbl_products`";
            $result = $this->db->query($sql_data)->getResult();
            // pre($sql_data);
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
                
            
                // echo'ranges';pre($sql_data);
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

                            //                             echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";// exit;

                            $capacity_threshold_one = $r->capacity_threshold_one;

                            $capacity_threshold_two = $r->capacity_threshold_two;


                            //                             echo "<p>totall capacity $operator_capacity  percent $percentage_of_capacity  thresone $capacity_threshold_one</p>";// exit;



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
            $desc_html = array();
            foreach ($array as $data) {

                $price = $data['price'];
                $id = $data['id'];
                $u_a = $data['u'];


                $desc_html[] = $this->get_sorted_price_html($id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $number_of_days, $code, $website, $dayName, $selectedDate, $cur, $webtype, $airport, $u_a);
            }


            // pre($array);
            ///////////////////////////////////////////////////////////////////////////////////
            if (empty($desc_html)) {

                echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
                // print_r($desc_html);
            } else {

                print_r(json_encode($desc_html));
            }
        } else {

            echo "Authentication Failed";
        }
    }

    public function get_sorted_price_html($product___id, $formated_arrive_date, $changedDate, $arrivalTime, $departureTime, $number_of_days, $code, $website, $dayName, $selectedDate, $cur = '£', $webtype, $airport, $u_a)
    {

        $formatted_departureTime_Time = $this->formatTime($departureTime);

        if ($formatted_departureTime_Time !== false) {
            // echo $formatted_departureTime_Time; // Outputs: 01:30

        } else {
            $formatted_departureTime_Time = "00:00";

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

                    $booknow = '  <a href="provide-detail.php?airport=' . $airport . '&operator_id=' . $r->operator_id . '&p_id=' . $r->id . '&price=' . $price . '&name=' . urlencode($r->name) . '&selectedDate=' . $selectedDate . '&changedDate=' . $changedDate . '&arrivalTime=' . $arrivalTime . '&departureTime=' . $departureTime . '"><button type="submit"
                                name="Check-Availability"
                                class="btn btn-primary buttonWithLoading buttonWithLoading1 bg-color hr"><span
                                    class="glyphicon glyphicon-circle-arrow-right"
                                    aria-hidden="true"></span>Book Now</button></a>';

                    $booknow = strip_tags($booknow);

                    $booknow_url = "provide-detail.php?airport=$airport&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime";

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
                    if ($r->park_mark) {
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
                                    </li>
                                </ul>
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
                                    </li>
                                </ul>
                            </div>';
                    }



                    $product_code_return = htmlspecialchars($r->product_code);

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

                    $booknow_url = "provide-detail.php?airport=$airport&promo_code=$code&promo_price=$promo_price&operator_id=$r->operator_id&p_id=$r->id&price=$price&name=" . urlencode($r->name) . "&selectedDate=$selectedDate&changedDate=$changedDate&arrivalTime=$arrivalTime&departureTime=$departureTime";


                    //$moreinfo="<button type=\"button\" OnClick=\"show_more_info(this)\" data-meet-greet='$htmlmeetngreet' data-product='$productName' data-transfer='$lbl_transfer_time' data-miles='$lbl_distance_miles' data-booknow='$booknow_url' data-price='$price' data-introduction='$introduction' data-information='$information' data-security_measures='$security_measures' data-departure_procedures='$departure_procedures' data-arrival_procedures='$arrival_procedures' data-transfers='$transfers' class=\"btn btn-outline-dark buttonWithLoading1 modalshowsub w-100 mt-sm-2 ml-2 ml-sm-0\"><span class=\"glyphicon glyphicon-plus\" aria-hidden="true"></span>More Info</button>";

                }
            } //price check
        }


        $supplier_array = array(
            'product_name' => $productName,
            'information' => $information,
            'departure_procedures' => $departure_procedures,
            'price' => $price,
            'currency' => $cur,
            'product_code' => $product_code_return
        );


        return $supplier_array;

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


    function convertToTimeFormat($input)
    {
        // Extract hours and minutes from the input string
        $hours = substr($input, 0, 2);
        $minutes = substr($input, 2, 2);

        // Create a time string in "HH:MM" format
        $timeString = $hours . ":" . $minutes . ":00";

        return $timeString;
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

}












