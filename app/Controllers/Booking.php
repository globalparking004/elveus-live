<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use ValueError;
use App\Libraries\PdfGenerator;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as excel;
use CodeIgniter\I18n\Time;
use DateTime;
use App\Libraries\ClickSendService;

ini_set('memory_limit', '-1'); // Adjust as needed, e.g., 256M, 512M, or -1 for unlimited


class Booking extends BaseController 
{
    use ResponseTrait;
    protected $Users;
    protected $user_id;
    protected $Roles;
    protected $Airport;
    protected $AirportType = "";
    protected $clickSend;
    protected $operator_id;

    public function __construct()
    {
        $this->Users = new UsersModel;
        $this->Roles = new RolesModel;
        $airports = session()->get('AUTH');
        $this->user_id = $airports['id'];
        $this->Airport = "*";
        $this->operator_id = "*";

        $this->clickSend = new ClickSendService();

        if ($airports['airport']) {
            $this->Airport = trim($airports['airport']);
            $this->operator_id = trim($airports['operator_id']);

        }
        if ($this->Airport == "") {
            $this->Airport = "*";
            $this->operator_id = "*";
        }
    }

    public function index()
    { 
        $data = [
            "page_title" => "Bookings",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Add Booking", "status" => "", "link" => false]
            ]
        ];
        
        return view('booking/view', $data);
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
        $id = isset($_POST['id']) ? $_POST['id'] : '';


        $airport = isset($_POST['location_code']) ? $_POST['location_code'] : '';
        $agent = isset($_POST['opitech_agent']) ? $_POST['opitech_agent'] : '';
        $rdate = time();
        $new_price = isset($_POST['new_price']) ? $_POST['new_price'] : '';
        $new_reference = isset($_POST['new_reference']) ? $_POST['new_reference'] : "";
        $carReg = isset($_POST['carReg']) ? $_POST['carReg'] : '';
        $carMake = isset($_POST['carMake']) ? $_POST['carMake'] : '';
        $carModel = isset($_POST['carModel']) ? $_POST['carModel'] : '';
        $carColour = isset($_POST['carColour']) ? $_POST['carColour'] : '';
        $passenger = isset($_POST['passenger']) ? $_POST['passenger'] : '';



        $required_OutTerminal = isset($_POST['required']['OutTerminal']) ? $_POST['required']['OutTerminal'] : '';
        $required_RetTerminal = isset($_POST['required']['RetTerminal']) ? $_POST['required']['RetTerminal'] : '';
        $required_OutFltNo = isset($_POST['required']['OutFltNo']) ? $_POST['required']['OutFltNo'] : '';
        $required_InFltNo = isset($_POST['required']['InFltNo']) ? $_POST['required']['InFltNo'] : '';

        $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
        $surname = isset($_POST['surname']) ? $_POST['surname'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $contactNumber = isset($_POST['contactNumber']) ? $_POST['contactNumber'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $billing_address_line_1 = isset($_POST['billing_address_line_1']) ? $_POST['billing_address_line_1'] : '';
        $billing_address_post_code = isset($_POST['billing_address_post_code']) ? $_POST['billing_address_post_code'] : '';
        $request_token = isset($_POST['request_token']) ? $_POST['request_token'] : '';
        $parkingQuoteId = isset($_POST['parkingQuoteId']) ? $_POST['parkingQuoteId'] : '';
        $opitechAgentId = isset($_POST['opitechAgentId']) ? $_POST['opitechAgentId'] : '';
        $completebooking = isset($_POST['completebooking']) ? $_POST['completebooking'] : '';

        $operatorid = isset($_POST['operatorid']) ? $_POST['operatorid'] : '';
        $booking_type = 'Online';



        $arrival_date = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : '';
        $departure_date = isset($_POST['departure_date']) ? $_POST['departure_date'] : '';
        $arrival_date = strtotime($arrival_date);

        $arrival_date = date('Y-m-d', $arrival_date);
        $departure_date = strtotime($departure_date);

        $departure_date = date('Y-m-d', $departure_date);


        $arrivalTime = isset($_POST['arrival_time']) ? $_POST['arrival_time'] : '';
        $departureTime = isset($_POST['departure_time']) ? $_POST['departure_time'] : '';

        $arrivalTime = "$arrivalTime";

        // Extract hours and minutes
        $hours = substr($arrivalTime, 0, 2);
        $minutes = substr($arrivalTime, 2, 2);

        // Concatenate with a colon
        $formattedTimearrivalTime = $hours . ":" . $minutes;


        // Extract hours and minutes
        $hours = substr($departureTime, 0, 2);
        $minutes = substr($departureTime, 2, 2);

        // Concatenate with a colon
        $formattedTimedepartureTime = $hours . ":" . $minutes;


        $arrival_date = "$arrival_date $formattedTimearrivalTime:00";
        $departure_date = "$departure_date $formattedTimedepartureTime:00";


        if (empty($new_reference)) {

            $new_reference = "GL-$airport-" . $this->generateUniqueTimestamp();
        }
        if (empty($new_price)) {

            $new_price = ($_POST['cal_price']);
        }

        $currentTimestamp = time();
        $formattedDate = date("Y-m-d H:i:s", $currentTimestamp);

        $validationRules = [
            'carReg' => 'required',
            'carMake' => 'required',
            'carModel' => 'required',
            'carColour' => 'required',
            'firstName' => 'required',
            'surname' => 'required',
            // 'contactNumber' => 'required',
            // 'email' => 'required|valid_email'
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
            $source = $agent.'-Dashboard';
            if (is_numeric($agent)) {
                $source = 'Dashboard';
            }
            $sql_data = "INSERT INTO tbl_booking 
                        (price, reference, carReg, carMake, carModel, carColour, OutTerminal, RetTerminal, OutFltNo, InFltNo,
                        firstName, surname, email, contactNumber ,product_id,airport,agent_id,depart_at,source,return_at,status,operator_id,booked_at,booking_type,passenger) 
                        VALUES 
                        ('$new_price', '$new_reference', '$carReg', '$carMake', '$carModel', '$carColour', '$required_OutTerminal', '$required_RetTerminal', '$required_OutFltNo', '$required_InFltNo',
                        '$firstName', '$surname', '$email', '$contactNumber',$id,'$airport','$agent','$arrival_date','$source','$departure_date','1','$operatorid','$formattedDate','$booking_type','$passenger');";
            $result = $this->db->query($sql_data);

            $id = $this->db->insertID(); 

            // send_email($email, "Your Parking Booking Confirmation", $id);
            if ($result) {
                logActivity($this->user_id, $id ,'Add new Booking', 'Add new Booking successfully');
                echo json_encode(['code' => 1, 'msg' => "Data updated successfully"]);
            } else {

                echo json_encode(['code' => 0, 'error' => "not saved"]);
            }
        }
    }

    public function create_booking2($GET = "")
    {
        if ($GET != "") {
            $airport = $GET->airport;
            $selectedDate = date("d-M-Y", strtotime($GET->depart_at));
            $changedDate = date("d-M-Y", strtotime($GET->return_at));
            $agent = $GET->agent_id;
            $arrivalTime = date("H:i", strtotime($GET->depart_at));
            $arrivalTime = str_replace(":", "", $arrivalTime);
            $departureTime = date("H:i", strtotime($GET->return_at));
            $departureTime = str_replace(":", "", $departureTime);
            $move_booking = 'move';
            $source_id = $GET->id;
        } else {
            $airport = $_GET['airport'];
            $selectedDate = $_GET['selectedDate'];
            $changedDate = $_GET['changedDate'];
            $agent = $_GET['agent'];
            $arrivalTime = $_GET['arrivalTime'];
            $departureTime = $_GET['departureTime'];
            $move_booking = isset($_POST['move_booking']) ? $_POST['move_booking'] : '';
            $source_id = isset($_POST['source_id']) ? $_POST['source_id'] : '';
        }



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
        // exit;
        $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' AND  (($arrivalTime>=`opening_time` and $departureTime <= `closing_time`) or (0247=`opening_time` and 0247 = `closing_time`)) ";
        // $sql_data = "SELECT * FROM `tbl_products` WHERE `parent`='$airport' ";

        // echo $sql_data;

        $result = $this->db->query($sql_data)->getResult();

        $data = [
            "page_title" => "Bookings",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Create Booking", "status" => "", "link" => false]
            ]
        ];

        $html = "";

        foreach ($result as $r) {
            // $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id`= $r->id  and '$formated_arrive_date' >= `dfrom` AND '$changedDate' <= `dto` limit 1";
            $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = $r->id AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1 ";
            // echo $sql_data;

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
                    $amount_to_add = 0;

                    if (!empty($adjust_prices_by_capacity) && $adjust_prices_by_capacity == $adjust_prices_by__product_capacity) {

                        $sql_data = "SELECT count(*) as capacity_full FROM `tbl_booking` WHERE `product_id`= $r->id";
                        $result = $this->db->query($sql_data)->getRow();
                        $capacity_full = $result->capacity_full;
                        $capacity = $r->capacity;
                        
                        $percentage_of_capacity = 0;
                        if ($capacity) {
                            $percentage_of_capacity = $capacity_full / $capacity * 100;
                        }

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


                        $sql_data = "SELECT count(*) as operator_capacity  FROM `tbl_booking` WHERE `operator_id`= $r->operator_id";
                        $result = $this->db->query($sql_data)->getRow();
                        $operator_capacity = $result->operator_capacity;



                        $sql_data = "SELECT  count(*) as  capacity FROM `tbl_booking` WHERE `id`= $r->operator_id";
                        $result = $this->db->query($sql_data)->getRow();
                        $capacity = $result->capacity;


                        $percentage_of_capacity = ($capacity > 0) ? $operator_capacity / $capacity * 100:'';

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


                    if (isset($move_booking) && $move_booking == "move") {

                        $html .= "<tr>
                        <td>$r->name</td>                        
                        <td>$price</td>
                        <td><button class=\"btn btn-primary\" onclick=\"move_booking_save('$source_id','$price','$r->id')\">Move Now</button></td>
                      </tr>";


                    } else {

                        $html .= "<tr>
                        <td>$r->name</td>                        
                        <td> $price</td>
                        <td><button class=\"btn btn-primary\" onclick=\"edit_data('$r->id','$price','$r->operator_id')\">Book Now</button></td>
                      </tr>";
                    }
                }
            } //price check
        }

        // exit;
        // print_r($result);

        if (isset($move_booking) && $move_booking == "move") {
            if (empty($html)) {

                return "";
            } else {

                return $html;
            }
        } else {
            if (empty($html)) {

                echo json_encode(['code' => 0, 'msg' => 'Not Avaiable']);
            } else {

                return json_encode(['code' => 0, 'msssg' => $html]);
            }
        }


        // print_r($html);

        // $html="<p>umair</p>";
        // $data['data'] = $result;
        // return view('booking/create_booking2', $data);
    }

    public function bookings()
    {

        // $data = [
        //     "page_title" => "Bookings",
        //     "breadcrumb" => [
        //         ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
        //         ["href" => base_url('bookings'), "title" => "Add Booking", "status" => "", "link" => false]
        //     ]
        // ];

        // return view('booking/view', $data);
        ///redirect("bookings/add");

        return redirect()->to('bookings/add');
    }

    public function booking_report_view()
    {
        $AUTH=session()->get('AUTH');

        $data = [
            "page_title" => "Bookings",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings Report", "status" => "", "link" => false]
            ]
        ];
        // $sql_query = "UPDATE `tbl_booking` SET source='' WHERE reference='HLZCCG' ";
        // $result = $this->db->query($sql_query);

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['operators'] = $agent_result;

        // $sql_data = "select source from tbl_booking where source IS NOT NULL AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR source LIKE '%Dashboard'  OR source='CTAP' OR source='Airport Parking With Us') GROUP BY source";
        // $result = $this->db->query($sql_data)->getResult();
        // $data['suppliers'] = $result;
        // foreach($suppliers as $s){
        //     $name=$s->source;
        //     echo "<option value='$name'>$name</option>";
        // }
        $data['type'] = (isset($_GET['type'])) ? $_GET['type'] : '';
        // $templates = $this->clickSend->smsTemplates(1); 
        // $data['templates'] = ($templates)? $templates->data->data: ''; 
        $data['templates'] = ''; 
        
        return view('booking/report', $data);
    }

    public function booking_report_view_supplier()
    {  
        $data = [
            "page_title" => "Supplier Bookings",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings Report", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        return view('booking/supplier_booking', $data);
    }

    public function booking_report_view_driver()
    {
        $data = [
            "page_title" => "Departure & Returns",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings Report", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        // $templates = $this->clickSend->smsTemplates(1); 
        // $data['templates'] = ($templates)? $templates->data->data: ''; 
        $data['templates'] = '';  

        return view('booking/report_driver', $data);
    }

    public function booking_report_view_driverb()
    {
        $data = [
            "page_title" => "Beta Departure & Returns",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings Report", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        // $templates = $this->clickSend->smsTemplates(1); 
        // $data['templates'] = ($templates)? $templates->data->data: ''; 
        $data['templates'] = ''; 

        return view('booking/report_driverb', $data);
    }

    public function booking_prices()
    {
        $data = [
            "page_title" => "Prices",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('prices'), "title" => "Prices", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        return view('general/prices', $data);
    }

    public function bookings_report()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = (isset($_GET['reference'])) ? $_GET['reference'] : '';
        $surname = (isset($_GET['surname'])) ? $_GET['surname'] : '';
        $CarRegistration = (isset($_GET['CarRegistration']))? $_GET['CarRegistration'] : '';
        $Phone = (isset($_GET['Phone'])) ? $_GET['Phone'] : '';
        $Email = (isset($_GET['Email'])) ? $_GET['Email'] : '';
        $status = (isset($_GET['status'])) ? $_GET['status'] : '';
        $airport = (isset($_GET['airport']))? $_GET['airport'] : '';
        $operator = (isset($_GET['operator']))? $_GET['operator'] : '';
        $source = (isset($_GET['source']))? $_GET['source'] : '';

        $DateFrom = (isset($_GET['DateFrom'])) ? $_GET['DateFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $filter_date = (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '';
        $website = (isset($_GET['website'])) ? $_GET['website'] : '';
        $contactNumber = (isset($_GET['contactNumber'])) ? $_GET['contactNumber'] : '';
        $role_id = $_GET['role_id'];


        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'reference',
            2 => 'surname',
            3 => 'carReg',
            4 => 'email',
            5 => 'booked_at',
            6 => 'depart_at',
            7 => 'return_at',
            8 => 'contactNumber',
            9 => 'source',
        ];

        $SQLreference = "";
        if (trim($reference) != "") {
            $SQLreference = " AND reference='$reference' ";
        }

        $SQLsurname = "";
        if (trim($surname) != "") {
            $SQLsurname = " AND (surname='$surname' OR firstName='$surname')";
        }

        $SQLCarRegistration = "";
        if (trim($CarRegistration) != "") {
            $SQLCarRegistration = " AND carReg LIKE '$CarRegistration%' ";
            // $SQLCarRegistration = " AND carReg='$CarRegistration' ";
        }


        $SQLEmail = "";
        if (trim($Email) != "") {
            $SQLEmail = " AND email='$Email' ";
        }

        $SQLcontactNumber = "";
        if (trim($contactNumber) != "") {
            $SQLcontactNumber = " AND contactNumber='$contactNumber' ";
        }

        $SQLwebsite = "";
        if (!empty($website) && $website != "*") {
            // $websiteList = explode(',', $website); // Convert to array

            $websiteList = array_map(function($a) {
                return "'" . trim(addslashes($a)) . "'";
            }, $website);

            $SQLwebsite = " AND source IN (" . implode(',', $websiteList) . ")";  
            // $SQLwebsite = " AND source='$website' ";
        }

        $SQLsource = "";
        if (!empty($source) && $source != "*") 
        {
            $SQLwebsite = " AND traffic_source='$source' ";
            // $sourceList = array_map(function($a) {
            //     return "'" . trim(addslashes($a)) . "'";
            // }, $source);

            // $SQLsource = " AND traffic_source IN (" . implode(',', $sourceList) . ")";
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            
            $SQLstatus = " AND status='$status' ";
            
        }
        // if ($AUTH['role_name'] == 'DRT') {
        //     $SQLstatus = " AND status='1' ";
        // }
        $SQLairport = "";
        if ($this->Airport != "*") {  
            $airportList = explode(',', $this->Airport); // Convert to array

            $airportList = array_map(function($a) {
                return "'" . trim(addslashes($a)) . "'";
            }, $airportList);

            $SQLairport = " AND airport IN (" . implode(',', $airportList) . ")";  
            // $SQLairport = " AND b.airport='" . $this->Airport. "'";
        }
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND airport='" . $airport . "'";
        }

        $SQLFilterDate = "";
        if ($SQLsurname != '' || $SQLCarRegistration != '' || $SQLEmail != '' || $SQLreference != '' || $SQLcontactNumber != '') {

        } else {
            if ($filter_date == "booking_at") {
                $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "departure_at") {
                $SQLFilterDate = "AND date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "return_at") {
                $SQLFilterDate = "AND date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
                $SQLstatus .= "AND show_status = 1";
            }
        }
        /////////////////////
        $SQLoperator = "";
        if ($AUTH['role_id'] == 1 && trim($operator) != '*') {
            $SQLoperator = " AND operator_id='$operator'";
        }else{
            if ($this->operator_id != "*") {
                $SQLoperator = " AND operator_id='" . $this->operator_id . "'";
            }
        }
        ///////////////////

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLreference $SQLsurname $SQLCarRegistration $SQLEmail $SQLstatus $SQLairport $SQLwebsite $SQLcontactNumber $SQLoperator $SQLsource";
        $sql_data = "SELECT * FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLreference $SQLsurname $SQLCarRegistration $SQLEmail $SQLstatus $SQLairport $SQLwebsite $SQLcontactNumber $SQLoperator $SQLsource";

        if ($AUTH['role_name'] == 'SourceBase')
        {
            $SQLproduct = " AND product_id=260";
            // $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $sql_data = "SELECT * FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLreference $SQLsurname $SQLCarRegistration $SQLEmail $SQLstatus $SQLairport $SQLwebsite $SQLcontactNumber $SQLoperator $SQLproduct";
        }
        

        //exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'created_at') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }

        $sql_count = $sql_count . $condition;

        $sql_data = $sql_data . $condition;
        
        // exit($sql_data);

        $this->AirportType = get_website_type($this->Airport);

        $total_count = $this->db->query($sql_count)->getRow();
        if (strval($role_id) > 1 &&  $AUTH['role_name'] != 'DRT' &&  $AUTH['role_name'] != 'CSR' && $AUTH['role_name'] != 'SourceBase') {
            $OrderBy = " ORDER BY depart_at asc";
            if ($filter_date == "booking_at") {
                $OrderBy = " ORDER BY created_at asc";
            } else if ($filter_date == "departure_at") {
                $OrderBy = " ORDER BY depart_at asc";
            } else if ($filter_date == "return_at") {
                $OrderBy = " ORDER BY return_at asc";
            } else if ($filter_date == "driver_at") {
                $OrderBy = " ORDER BY depart_at asc";
            }
        } else {
            $OrderBy = " ORDER BY id desc";
        } 
        //" ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        if (strval($role_id) > 1 && $AUTH['role_name'] != 'DRT') {

            $sql_data .= $OrderBy . $SortBy;

        } else {
            $sql_data .= $OrderBy . $SortBy . $Limit;

        }
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        $totalPrice = 0;
        $totalll_count = 0;
        $grand_totall = array();
        // if (strval($role_id) == 7){
        //     pre($sql_data);
        // }
        // pre($sql_data);
        
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = "";
            if ($result2) {
                $operator_name = $result2->description;
            }

            $getvalue_operators = session()->get('AUTH');

            if (isset($getvalue_operators['operator_id'])) {
                $operator_id_val = $getvalue_operators['operator_id'];
            } else {
                $operator_id_val = 0;
            }


            if (strval($role_id) > 1 && $AUTH['role_name'] != 'DRT' && $AUTH['role_name'] != 'CSR' && $AUTH['role_name'] !='Operator' && $operator_id_val != 0 && $AUTH['role_name'] != 'SourceBase') {
                $seperation = "operator_id='$operator_id_val' and operator_id_show=1";
            } else {
                $seperation = "1=1";
            }
            $sql_data3 = "SELECT product_code,name, product_type FROM `tbl_products` where id='$value->product_id' and $seperation LIMIT 1";
            $result3 = $this->db->query($sql_data3)->getRow();
            $product_code = "";
            $product_name = "";
            $product_type = "";
            $seperation_check = 0;

            if ($result3) {
                $product_code = $result3->product_code;
                $product_name = $result3->name;
                // $product_type = $result3->product_type;

                if($result3->product_type == 'Meet & Greet'):
                    $product_type= 'VALET';
                elseif ($result3->product_type == 'Park & Ride'):
                    $product_type='PR';
                elseif($result3->product_type == 'Station'):
                    $product_type= 'TRAIN';
                else:
                    $product_type= $result3->product_type;
                endif;
            }
            $sql_query = "SELECT bad.*, pad.addon_name FROM `tbl_booking_addons` bad LEFT JOIN `tbl_product_addons` pad ON bad.addon_id=pad.id WHERE bad.booking_id='$value->id'";
            $addons = $this->db->query($sql_query)->getRow();
            $addon_name='';
            if ($addons) {
                // foreach ($addons as $key => $ad) {
                    $addon_name .= $addons->addon_name;
                // }
            }


            if (strval($role_id) > 1 && $AUTH['role_name'] !='DRT' && $AUTH['role_name'] !='CSR') {

                // if ($value->booking_type == "Cash") {
                //     $badge = "badge badge-glow bg-success";
                //     $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                // } else {
                //     $badge = "badge badge-glow bg-warning";
                //     $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                // }                   
                //  echo $sql_data2;

                $start = $this->request->getVar('start');
                $length = $this->request->getVar('length');
                $end = $start + $length;

                // Check if totalll_count falls within the current page

                if ($product_code != "") {

                    if ($product_code != "" && $totalll_count >= $start && $totalll_count < $end) 
                    {
                        $grand_totall[] = 1;
                        $totalll_count = $totalll_count + 1;

                        $reference= $value->reference;
                        if ($AUTH['role_name'] =='Operator') {
                            $afterDash = preg_replace('/^.*-/', '', $reference);
                            $reference=  preg_replace('/^[A-Za-z]+/', '', $afterDash);
                        }
                         // Low price booking
                        $today = date('Y-m-d', strtotime($value->booked_at));
                        $price=$value->price;
                        $orignal_price = '';
                        if ($value->airport=='BRS' || $value->airport=='LTN' || $value->airport=='STN')
                        {
                            if ( $value->source=='CTAP' && date('Y-m-d') == $today) 
                            {
                                $resp = identify_low_price($value->depart_at, $value->return_at,$value->product_id,$value->price);
                                // print_r($resp);echo4ZawJVTHLMYB2YQ'<br>';
                                if ($resp && $resp[2] > 3) 
                                {
                                    $row['DT_RowClass'] = $resp[0];
                                    $orignal_price = ($resp[2] > 3)? ($resp[1]-1.95).'('. $resp[2] .')' : $resp[1]-1.95;
                                }
                            }
                        }
                        if ($value->airport=='DXB')
                        {
                            $row['DT_RowClass'] = 'row_dubai';
                        }
                        if ($value->email) {
                            $resp = identify_repeated_customer($value->email);
                            if ($resp) {
                                $row['DT_RowClass'] = $resp;
                            }
                        }
                        

                        $row[] = $product_code . "\n" . $product_name;

                        $row[] = $reference;
                        $row[] = $value->firstName . " " . $value->surname . "\n" . $value->contactNumber;
                        // $row[] = $value->contactNumber;
                        if ($AUTH['airport'] == 'BHX'):
                            $row[] = date("d-M-Y", strtotime($value->booked_at)) . "\n" . date("H:i:s", strtotime($value->booked_at));
                        endif;
                        $row[] = date("d-M-Y", strtotime($value->depart_at)) . "\n" . date("H:i:s", strtotime($value->depart_at));
                        // $row[] = date("H:i:s", strtotime($value->depart_at));
                        $row[] = date("d-M-Y", strtotime($value->return_at)) . "\n" . date("H:i:s", strtotime($value->return_at));
                        // $row[] = date("H:i:s", strtotime($value->return_at));

                        $row[] = $value->carMake . "\n" . $value->carModel . "\n" . $value->carColour . "\n" . $value->carReg;
                        // $row[] = $value->carReg;
                        $row[] = $product_type;
                        $row[] = $value->passenger;
                        if ($AUTH['airport'] == 'BHX' || $AUTH['airport'] == 'BFS' || $AUTH['airport'] == 'MAN'):

                            $bookingType = ($value->booking_type)? $value->booking_type:'Online';
                            if ($bookingType == "Arrival") {
                                $badge = "badge badge-glow bg-warning";
                                $row[] = "<span class='$badge'>" . $bookingType . "</span>";
                            } else {
                                $badge = "badge badge-glow bg-success";
                                $btype = '';
                                if($AUTH['airport'] == 'BFS' || $AUTH['airport'] == 'BHX' || $AUTH['airport'] == 'MAN'):
                                    $btype = "<span class='$badge'>" . $bookingType . "</span>";
                                endif;
                                $row[]= $btype;
                                // $row[] = "<span class='$badge'>" . $bookingType . "</span>";
                            }
                        endif;
                        if ($this->AirportType == "AIRPORT" || $AUTH['role_name'] == 'SourceBase') {
                            $row[] = $value->airport . " - " . $value->OutTerminal;
                            $row[] = $value->OutFltNo;
                            $row[] = $value->InFltNo;
                        } else {
                            $row[] = $value->OutTerminal;
                            $row[] = $value->RetTerminal;
                        }
                        if ($AUTH['role_name'] !='Driver')  
                        {
                            $row[] = ($AUTH['role_name'] =='SourceBase') ? $value->price:number_format($value->price - 1.95, 2);
                        }
                        $row[] = $orignal_price;
                        $id = id_en($value->id);

                        $show_action='';
                        $showClass='btn-default';
                        $noShowClass='btn-default';
                        if ($value->show_status ==1) {
                            $showClass='btn-success';
                        }else{
                            $noShowClass='btn-danger';
                        }
                        $show_action .= "<div class=\"btn-group\"><a class=\"btn ".$noShowClass." btn-sm\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\">NoShow</a></div>";
                        $show_action .= "<div class=\"btn-group\"><a class=\"btn ".$showClass." btn-sm\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\">Show</a></div>";

                        if ($AUTH['airport'] != 'BHX'):
                            $row[] = $show_action;
                        endif;

                        $id = id_en($value->id);
                        $action = "<div class=\"btn-group\">
                            <a href=\"javascript:void(0);\" class=\"btn btn-primary dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                            Actions
                            </a>
                            <div class=\"dropdown-menu\">
                              <a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>
                              <a class=\"dropdown-item\" onclick=\"print_card(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> Print Card</a>
                              <a class=\"dropdown-item\" onclick=\"print_card_new(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> New Print Card</a>
                            </div>
                          </div>";
                        $row[] = $action;
                    }//seperation
                    else {
                        $seperation_check = 1;
                        $grand_totall[] = 1;
                        $totalll_count = $totalll_count + 1;
                    }
                }//limit check
                else {
                    $seperation_check = 1;
                    // echo 'product_code: '.$sql_data3;

                }

            }else {
                $ref= substr($value->reference, 0, 3);

                if ($value->operator_id == 42 && $value->airport =='BRS' 
                    && $value->is_emailsent != 1 && $value->status == 1 ) 
                {
                    $userEmail = 'bookings@bristoleliteparking.co.uk';
                    $sql_data="SELECT * FROM tbl_settings";
                    $settings=$this->db->query($sql_data)->getRow();
                    $from = $settings->smtpuser;

                    $sql_res = "SELECT * FROM tbl_websites WHERE `short_code`='$value->airport'";
                    $booking_airport = $this->db->query($sql_res)->getRow();

                    $booking_airport_webtype = $booking_airport->type;
                    $booking_airport_webtype = strtolower($booking_airport_webtype);

                    /////////////////////////////////////////////////////////
                    $response = send_email($userEmail, "Your Parking Booking", $value->id, $from, $booking_airport_webtype);

                    if ($response) 
                    {
                        $sql_res = "UPDATE tbl_booking SET `is_emailsent`= 1  WHERE `id`='$value->id'";
                        $booking_update = $this->db->query($sql_res);
                    }
                }
                if ($value->operator_id == 41 && $value->airport =='MAN'
                    && $value->is_emailsent != 1 && $value->status == 1 
                    && ($ref == 'GL-' || $ref == 'GO-') ) 
                {
                    $userEmail = 'avianparkingltd@gmail.com';
                    $sql_data="SELECT * FROM tbl_settings";
                    $settings=$this->db->query($sql_data)->getRow();
                    $from = $settings->smtpuser;

                    $sql_res = "SELECT * FROM tbl_websites WHERE `short_code`='$value->airport'";
                    $booking_airport = $this->db->query($sql_res)->getRow();

                    $booking_airport_webtype = $booking_airport->type;
                    $booking_airport_webtype = strtolower($booking_airport_webtype);

                    /////////////////////////////////////////////////////////
                    $response = send_email($userEmail, "Your Parking Booking", $value->id, $from, $booking_airport_webtype);

                    if ($response) 
                    {
                        $sql_res = "UPDATE tbl_booking SET `is_emailsent`= 1  WHERE `id`='$value->id'";
                        $booking_update = $this->db->query($sql_res);
                    }
                }

                if ($value->promocode =='FB10' || $value->promocode=="FB20" || $value->promocode=='FAB10') {
                    update_fb_source($value->promocode);
                }
                
                update_ref2($value->reference);
                $row[] = $value->reference;
                $sourceHtml = $value->source;
                if (empty($value->source)) {
                    $sourceHtml = $value->source. " <button class='webEdit btn btn-outline-warning ' data-id='$value->id'>Edit</button>";
                }
                // Low price booking
                $today = date('Y-m-d', strtotime($value->booked_at));
                $price=$value->price;
                $totalPrice+= $price;
                // $value->source=='CTAP'
                if ($value->airport=='BHX' || $value->airport=='LTN' || $value->airport=='STN' || $value->source =='Park&Fly' && date('Y-m-d') == $today && empty($value->promocode)) 
                {
                // if ($value->airport=='DUB' && $value->source =='Park&Fly' && empty($value->promocode)) 
                // {
                    // $ref= substr($value->reference, 0, 3);
                    if ($ref !== 'GL-')  
                    { 
                        $resp = identify_low_price($value->depart_at, $value->return_at,$value->product_id,$value->price);
                        // print_r($resp);echo'<br>';
                        if ($resp) 
                        {
                            $row['DT_RowClass'] = $resp[0];
                            // $price = $value->price.' ('.$resp[2].')';
                            $sql_query="SELECT * FROM `tbl_booking_prices` WHERE booking_id='$value->id'";
                            $exist = $this->db->query($sql_query)->getRow();
                            if (!$exist && $resp[2] > 0) 
                            {
                                $sql_query = "INSERT INTO `tbl_booking_prices`(`booking_id`, `booking_price`, `orignal_price`) VALUES ('$value->id','$value->price','$resp[1]')";
                                $this->db->query($sql_query);
                            }
                        }
                    }
                }
                if ($value->airport=='DXB')
                {
                    $row['DT_RowClass'] = 'row_dubai';
                }

                if ($value->email) 
                {
                    $resp = identify_repeated_customer($value->email); 
                    if ($resp) {
                        $row['DT_RowClass'] = $resp;
                    }
                }
                    

                $row[] = $sourceHtml;
                $row[] = $value->airport . "\n" . $product_name;
                $row[] = $value->firstName . " " . $value->surname;
                $row[] = date("d-M-Y H:i:s", strtotime($value->booked_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
                $row[] = $value->carReg;
                $row[] = $value->contactNumber;
                $row[] = $value->traffic_source;//$addon_name
                $code = substr($value->airport, 0, 3);
                $row[] = $price;

                // $row[] = $value->price;

                if ($value->status == 1) {
                    $badge = "badge badge-glow bg-success";
                    $row[] = "<span class='$badge'>Completed</span>";
                } elseif ($value->status == 0) {
                    $badge = "badge badge-glow bg-warning";
                    $row[] = "<span class='$badge'>Pending</span>";
                } elseif ($value->status == 2) {
                    $badge = "badge badge-glow bg-danger";
                    $row[] = "<span class='$badge'>Cancelled</span>";
                } elseif ($value->status == 3) {
                    $badge = "badge badge-glow bg-info";
                    $row[] = "<span class='$badge'>No Show</span>";
                } elseif ($value->status == 4) {
                    if ($value->price == $value->refund_amount) {
                        $badge = "badge badge-glow bg-primary";
                        $row[] = "<span class='$badge'>Refund</span>";
                    }else{
                        $badge = "badge badge-glow bg-primary";
                        $row[] = "<span class='$badge'>Partial Refund</span>";
                    }
                }
                $bookingType = ($value->booking_type)? $value->booking_type:'Online';
                if ($bookingType == "Arrival") {
                    $badge = "badge badge-glow bg-warning";
                    $row[] = "<span class='$badge'>" . $bookingType . "</span>";
                } else {
                    $badge = "badge badge-glow bg-success";
                    $row[] = "<span class='$badge'>" . $bookingType . "</span>";
                }
                $row[] = $value->promocode;
                $row[] = $operator_name;

                $id = id_en($value->id);
                $other_actions = '';
                if ($value->status == 0) {

                    $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";

                    $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('complete_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> Complete Booking</a>";

                } else if ($value->status == 1 || $value->status == 2) {
                    // $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('cancel_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='minus-circle'></i> Cancel Booking</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('make_refund',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Make a Refund</a>";
                    // if ($value->status == 1) {
                    //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('move_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='corner-up-left'></i> Move Booking</a>";
                    //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('edit_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather=\"edit\"></i> Amend Booking</a>";
                    // }
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('resend_email',`$id`);\" href=\"javascript:void(0)\"><i data-feather='send'></i> Resend Booking Confirmation</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" target=\"_blank\" href=".base_url('bookings/booking_pdf?id='.$value->id)."><i data-feather='file'></i> Booking PDF</a>";
                }

                if ($value->status == 0) {
                    $action = "<div class=\"btn-group\">
                    <a href=\"javascript:void(0);\" class=\"btn btn-primary dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Actions
                    </a>
                    <div class=\"dropdown-menu\">                      
                      $other_actions
                    </div>
                  </div>";
                } else {
                    $action = "<div class=\"btn-group\">
                    <a class=\"dropdown-item btn btn-primary  \" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\">
                    view Booking
                    </a>
                    <div class=\"dropdown-menu\">                      
                      $other_actions
                    </div>
                  </div>";
                }

                $row[] = $action;
            }

            if ($seperation_check === 0) {
                $data[] = $row;

            }
            // $totalll_count=sizeof($data);
        }
        if ($role_id == 1) {
            $tfooter = array_merge(
                array_fill(0, 9, ""),         // columns 1..10 (9 empty)
                ["Total"],
                [number_format($totalPrice, 2)], // column 11 with formatted total
                array_fill(0, 5, "")          // columns 12..16 (5 empty)
            );
            if($data):
                array_push($data, $tfooter);
            endif;
        }

        if (strval($role_id) > 1 && $AUTH['role_name'] !='DRT') 
        {
            $totalll_count_result = sizeof($grand_totall);
        } else {
            $totalll_count_result = $total_count->total;
        }
        // pre($total_count);
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function bookings_report_driver()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = (isset($_GET['reference'])) ? $_GET['reference'] : '';
        $surname = (isset($_GET['surname'])) ? $_GET['surname'] : '';
        $CarRegistration = (isset($_GET['CarRegistration']))? $_GET['CarRegistration'] : '';
        $contactNumber = (isset($_GET['contactNumber'])) ? $_GET['contactNumber'] : '';
        $airport = (isset($_GET['airport']))? $_GET['airport'] : '';
        $website = (isset($_GET['website'])) ? $_GET['website'] : '';

        $DateFrom = (isset($_GET['DateFrom'])) ? $_GET['DateFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $filter_date = (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '';
        $role_id = $_GET['role_id'];


        $condition = "";
        $table_map = [
            0 => 'b.reference',
            1 => 'b.surname',
            2 => 'b.carReg',
            3 => 'b.depart_at',
            4 => 'b.return_at',
            5 => 'b.contactNumber',
        ];

        $SQLreference = "";
        if (trim($reference) != "") {
            $SQLreference = " AND reference='$reference' ";
        }

        $SQLsurname = "";
        if (trim($surname) != "") {
            $SQLsurname = " AND (surname='$surname' OR firstName='$surname')";
        }

        $SQLCarRegistration = "";
        if (trim($CarRegistration) != "") {
            $SQLCarRegistration = " AND carReg LIKE '$CarRegistration%' ";
        }

        $SQLcontactNumber = "";
        if (trim($contactNumber) != "") {
            $SQLcontactNumber = " AND contactNumber='$contactNumber' ";
        }

        $SQLwebsite = "";
        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND b.source='$website' ";
        }

        $SQLstatus = " AND b.status=1 ";
        
        $SQLairport = "";
        if ($this->Airport != "*") {  
            $airportList = explode(',', $this->Airport); // Convert to array

            // Optional: sanitize and quote each value to prevent SQL injection (if not using prepared statements)
            $airportList = array_map(function($a) {
                return "'" . trim(addslashes($a)) . "'";
            }, $airportList);

            $SQLairport = " AND b.airport IN (" . implode(',', $airportList) . ")";  
            // $SQLairport = " AND b.airport='" . $this->Airport. "'";
        }
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND b.airport='" . $airport . "'";
        }

        $SQLFilterDate='';
        if ($SQLsurname != '' || $SQLCarRegistration != '' || $SQLreference != '' || $SQLcontactNumber != '') {

        } elseif ($filter_date == "departure_at") {
            $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 1";
        }else if ($filter_date == "return_at") {
            $SQLFilterDate = "and date(b.return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 1";
        }else if ($filter_date == "noshow") {
            $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 0";
        }
        
        /////////////////////
        $SQLoperator = "";
        if ($this->operator_id != "*") {
            $SQLoperator = " AND b.operator_id='" . $this->operator_id . "'";
        }
        
        /////////////////// 
        $sql_count = "SELECT COUNT(*) AS total FROM tbl_booking AS b WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite ";
        $sql_data = "SELECT b.* FROM `tbl_booking` b WHERE 1=1 $SQLFilterDate $SQLstatus $SQLreference $SQLsurname $SQLCarRegistration $SQLcontactNumber $SQLairport $SQLwebsite ";
        if ($filter_date == 'collected') {
            // bc.booking_id IS NOT NULL
            $sql_count = "SELECT COUNT(*) AS total FROM tbl_booking AS b LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id WHERE bc.booking_id IS NOT NULL $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite ";
            $sql_data = "SELECT b.*,bc.id as bc_id, bc.driver_id,bc.late_charges,bc.date_added as d_date,bc.status as c_status, d.name 
                FROM `tbl_booking` b 
                LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id 
                LEFT JOIN `tbl_drivers` d ON bc.driver_id=d.id 
                WHERE bc.booking_id IS NOT NULL
                $SQLFilterDate $SQLstatus $SQLreference $SQLsurname $SQLCarRegistration $SQLcontactNumber $SQLairport $SQLwebsite";
        }

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'b.reference') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }

        $sql_count = $sql_count . $condition;
        $orderBy='';
        if ($filter_date == 'collected') {
            $OrderBy = 'ORDER BY return_at,depart_at DESC';
        }
        $sql_data = $sql_data. $orderBy . $condition;
        $this->AirportType = get_website_type($this->Airport);

        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy='';
        if ($filter_date == "departure_at") {
            $OrderBy = " ORDER BY depart_at asc";
        } else if ($filter_date == "return_at") {
            $OrderBy = " ORDER BY return_at asc";
        }

        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $OrderBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        // pre($sql_data);
        $data = array();
        $totalll_count = 0;
        $grand_totall = array();

        // pre($sql_data);
        if ($filter_date == "departure_at" || $filter_date == "return_at") {
            $result = filter_uncollected_bookings($result,$filter_date);
        }

        foreach ($result as $value) 
        {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = "";
            if ($result2) {
                $operator_name = $result2->description;
            }

            $getvalue_operators = session()->get('AUTH');

            if (isset($getvalue_operators['operator_id'])) {
                $operator_id_val = $getvalue_operators['operator_id'];
            } else {
                $operator_id_val = 0;
            }

            $seperation = "1=1";
            
            $sql_data3 = "SELECT product_code,name, product_type FROM `tbl_products` where id='$value->product_id' and $seperation LIMIT 1";
            $result3 = $this->db->query($sql_data3)->getRow();
            $product_code = "";
            $product_name = "";
            $product_type = "";
            $seperation_check = 0;

            if ($result3) {
                $product_code = $result3->product_code;
                $product_name = $result3->name;
                // $product_type = $result3->product_type;

                if($result3->product_type == 'Meet & Greet'):
                    $product_type= 'VALET';
                elseif ($result3->product_type == 'Park & Ride'):
                    $product_type='PR';
                elseif($result3->product_type == 'Station'):
                    $product_type= 'TRAIN';
                else:
                    $product_type= $result3->product_type;
                endif;
            }

            $id = id_en($value->id);
            $driver_id='';
            $driver_name='';
            $collect_date='';
            $collect_status='';
            $printBtn='';
            $delBtn='';
            // if ($dresult) {
            if (isset($value->driver_id)) {
                $driver_id = $value->driver_id;
                $driver_name = $value->name;
                $collect_date = $value->d_date;
                $collect_status = $value->c_status;
                $delBtn=$value->bc_id;
                if ($value->late_charges >0) {
                    $printBtn = "<a class=\"dropdown-item btn-sm print-btn\" data-id='".$value->id."' href=\"javascript:void(0);\"><i data-feather='printer'></i> Print</a>";
                }
                
            }

            $row[] = $value->reference;
            $row[] = $value->source;
            $row[] = $value->airport . "\n" . $product_name;
            $row[] = $value->firstName . " " . $value->surname;
            $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
            $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
            $row[] = $value->carReg;
            $row[] = $value->contactNumber;
            $row[] = $value->price;
            if ($filter_date == 'collected') {
                $row[] = $driver_name.' <span class="badge badge-glow bg-success">'.$collect_status.'</span><br>'.$collect_date;
            }else{
                $showClass='btn-default';
                $noShowClass='btn-default';
                if ($value->show_status ==1) {
                    $showClass='btn-success';
                }else{
                    $noShowClass='btn-danger';
                }
                $show_action = "<a class=\"dropdown-item btn ".$noShowClass." btn-sm\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='x'></i></a>";
                $show_action .= "<a class=\"dropdown-item btn ".$showClass." btn-sm\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='check'></i></a>";

                
                $row[] = $show_action;
            }

            $late_charges=0;
                
            if ($filter_date == "departure_at") {
                // if ($value->airport == 'BHX' || $value->airport =='DUB') {
                    $row['DT_RowClass'] = getDepartRowClass($value->depart_at);
                // }
            }elseif ($filter_date == "return_at") {
                $res = getReturnRowClass($value->return_at);
                $row['DT_RowClass'] =$res[0];
                $late_charges = $res[1];
            }
            if ($value->airport=='DXB')
            {
                $row['DT_RowClass'] = 'row_dubai';
            }

            $id = id_en($value->id);
            $markBtn='';
            if ($value->show_status == 1) {
                $markBtn ="<a class=\"dropdown-item btn-sm collect-btn\" href=\"javascript:void(0);\" data-airport='".$value->airport."' data-id='".$value->id."' data-type='".$filter_date."' data-late='".$late_charges."' data-driver='".$driver_id."' data-delbtn='".$delBtn."'><i data-feather='check'></i> Mark Collect</a>";
            }
            $actionBtn = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-primary btn-sm dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item btn-sm copy-btn\" href=\"javascript:void(0);\"><i data-feather='copy'></i> Copy</a>
                  ".$markBtn."
                  <a class=\"dropdown-item btn-sm sms-btn\" href=\"javascript:void(0);\" data-phone='".$value->contactNumber."'><i data-feather='message-circle'></i> SMS</a>";
            $actionBtn.=$printBtn;
            $actionBtn.="</div></div>";
          
            // $actionBtn = "<a class=\"btn btn-primary btn-sm copy-btn\" href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='copy'></i></a>";
            // $actionBtn .= "<a class=\"btn btn-success btn-sm collect-btn\" data-airport='".$value->airport."' data-id='".$value->id."' data-type='".$filter_date."' data-late='".$late_charges."' data-driver='".$driver_id."' data-delbtn='".$delBtn."' href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='check'></i></a>";
            // $actionBtn .= $printBtn;
            $row[] = $actionBtn;
                    
            

            if ($seperation_check === 0) {
                $data[] = $row;

            }
            // $totalll_count=sizeof($data);

        }

        $totalll_count_result = $total_count->total;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function bookings_report_driverb()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $airport = (isset($_GET['airport']))? $_GET['airport'] : '';
        $website = (isset($_GET['website'])) ? $_GET['website'] : '';

        $DateFrom = (isset($_GET['DateFrom'])) ? $_GET['DateFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $filter_date = (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '';
        $role_id = $_GET['role_id'];


        $condition = "";
        $table_map = [
            0 => 'b.reference',
            1 => 'b.surname',
            2 => 'b.carReg',
            3 => 'b.depart_at',
            4 => 'b.return_at',
            5 => 'b.contactNumber',
        ];

        $SQLwebsite = "";
        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND b.source='$website' ";
        }

        $SQLstatus = " AND b.status='1' ";
        
        $SQLairport = "";
        if ($this->Airport != "*") {    
            $SQLairport = " AND b.airport='" . $this->Airport. "'";
        }
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND b.airport='" . $airport . "'";
        }

        $SQLFilterDate='';
        if ($filter_date == "departure_at") {
            $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 1";
        }else if ($filter_date == "return_at") {
            $SQLFilterDate = "and date(b.return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 1";
        }else if ($filter_date == "noshow") {
            $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 0";
        }
        
        /////////////////////
        $SQLoperator = "";
        if ($this->operator_id != "*") {
            $SQLoperator = " AND b.operator_id='" . $this->operator_id . "'";
        }
        $SQLNull='';
        if (empty($search)) {
            $SQLNull="AND bc.booking_id IS NULL";
        }
        ///////////////////
        $sql_count = "SELECT COUNT(*) AS total FROM tbl_booking AS b LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id WHERE 1=1 $SQLNull  $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite ";
        $sql_data = "SELECT b.* FROM `tbl_booking` b LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id WHERE 1=1 $SQLNull $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite ";
        if ($filter_date == 'collected') {
            // bc.booking_id IS NOT NULL
            $sql_count = "SELECT COUNT(*) AS total FROM tbl_booking AS b LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id WHERE bc.booking_id IS NOT NULL $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite ";
            $sql_data = "SELECT b.*,bc.id as bc_id, bc.driver_id,bc.late_charges,bc.date_added as d_date, d.name 
                FROM `tbl_booking` b 
                LEFT JOIN tbl_booking_collect bc ON bc.booking_id = b.id 
                LEFT JOIN `tbl_drivers` d ON bc.driver_id=d.id 
                WHERE bc.booking_id IS NOT NULL
                $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite";
        }

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'b.reference') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }

        $sql_count = $sql_count . $condition;
        $orderBy='';
        if ($filter_date == 'collected') {
            $OrderBy = 'ORDER BY return_at,depart_at DESC';
        }
        $sql_data = $sql_data. $orderBy . $condition;
        $this->AirportType = get_website_type($this->Airport);

        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy='';
        if ($filter_date == "departure_at") {
            $OrderBy = " ORDER BY depart_at asc";
        } else if ($filter_date == "return_at") {
            $OrderBy = " ORDER BY return_at asc";
        }

        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $OrderBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        $totalll_count = 0;
        $grand_totall = array();

        // pre($sql_data);

        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = "";
            if ($result2) {
                $operator_name = $result2->description;
            }

            $getvalue_operators = session()->get('AUTH');

            if (isset($getvalue_operators['operator_id'])) {
                $operator_id_val = $getvalue_operators['operator_id'];
            } else {
                $operator_id_val = 0;
            }

            $seperation = "1=1";
            
            $sql_data3 = "SELECT product_code,name, product_type FROM `tbl_products` where id='$value->product_id' and $seperation LIMIT 1";
            $result3 = $this->db->query($sql_data3)->getRow();
            $product_code = "";
            $product_name = "";
            $product_type = "";
            $seperation_check = 0;

            if ($result3) {
                $product_code = $result3->product_code;
                $product_name = $result3->name;
                // $product_type = $result3->product_type;

                if($result3->product_type == 'Meet & Greet'):
                    $product_type= 'VALET';
                elseif ($result3->product_type == 'Park & Ride'):
                    $product_type='PR';
                elseif($result3->product_type == 'Station'):
                    $product_type= 'TRAIN';
                else:
                    $product_type= $result3->product_type;
                endif;
            }

            $sql_query = "SELECT bad.*, pad.addon_name FROM `tbl_booking_addons` bad LEFT JOIN `tbl_product_addons` pad ON bad.addon_id=pad.id WHERE bad.booking_id='$value->id'";
            $addons = $this->db->query($sql_query)->getRow();
            $addon_name='';
            if ($addons) {
                $addon_name = $addons->addon_name;
            }

            $id = id_en($value->id);
            $driver_id='';
            $driver_name='';
            $collect_date='';
            $printBtn='';
            $delBtn='';
            // if ($dresult) {
            if (isset($value->driver_id)) {
                $driver_id = $value->driver_id;
                $driver_name = $value->name;
                $collect_date = $value->d_date;
                $delBtn=$value->bc_id;
                if ($value->late_charges >0) {
                    $printBtn = "<a class=\"dropdown-item btn-sm print-btn\" data-id='".$value->id."' href=\"javascript:void(0);\"><i data-feather='printer'></i> Print</a>";
                }
                
            }

            $row[] = $value->reference;
            $row[] = $value->source;
            $row[] = $value->airport . "\n" . $product_name;
            $row[] = $value->firstName . " " . $value->surname;
            $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
            $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
            $row[] = $value->carReg;
            $row[] = $value->contactNumber;
            $row[] = $value->price;
            if ($filter_date == 'collected') {
                $row[] = $driver_name.'<br>'.$collect_date;
            }else{
                $showClass='btn-default';
                $noShowClass='btn-default';
                if ($value->show_status ==1) {
                    $showClass='btn-success';
                }else{
                    $noShowClass='btn-danger';
                }
                $show_action = "<a class=\"dropdown-item btn ".$noShowClass." btn-sm\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='x'></i></a>";
                $show_action .= "<a class=\"dropdown-item btn ".$showClass." btn-sm\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\" style=\"margin-bottom: 5px\"><i data-feather='check'></i></a>";

                
                $row[] = $show_action;
            }

            $late_charges=0;
                
            if ($filter_date == "departure_at") {
                // if ($value->airport == 'BHX' || $value->airport =='DUB') {
                    $row['DT_RowClass'] = getDepartRowClass($value->depart_at);
                // }
            }elseif ($filter_date == "return_at") {
                $res = getReturnRowClass($value->return_at);
                $row['DT_RowClass'] =$res[0];
                $late_charges = $res[1];
            }
            
            $id = id_en($value->id);
            $actionBtn = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-primary btn-sm dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item btn-sm copy-btn\" href=\"javascript:void(0);\"><i data-feather='copy'></i> Copy</a>
                  <a class=\"dropdown-item btn-sm collect-btn\" href=\"javascript:void(0);\" data-airport='".$value->airport."' data-id='".$value->id."' data-type='".$filter_date."' data-late='".$late_charges."' data-driver='".$driver_id."' data-delbtn='".$delBtn."'><i data-feather='check'></i> Mark Collect</a>
                  <a class=\"dropdown-item btn-sm sms-btn\" href=\"javascript:void(0);\" data-phone='".$value->contactNumber."'><i data-feather='message-circle'></i> SMS</a>";
            $actionBtn.=$printBtn;
            $actionBtn.="</div></div>";
          
            $action = "<div class=\"btn-group\">
            <a class=\"dropdown-item btn btn-primary  \" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_booking_modal('view_driver_booking',`$id`);\" href=\"javascript:void(0);\">
            view Booking
            </a>
          </div>";
            
            if ($filter_date == 'collected' || $filter_date == 'noshow') {
                $row[] = $actionBtn;
            }else{
                $row[] = $action;
            }
            
                
            

            if ($seperation_check === 0) {
                $data[] = $row;

            }
            // $totalll_count=sizeof($data);

        }

        $totalll_count_result = $total_count->total;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function booking_prices_get()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = (isset($_GET['reference'])) ? $_GET['reference'] : '';
        $airport = (isset($_GET['airport']))? $_GET['airport'] : '';

        $DateFrom = (isset($_GET['DateFrom'])) ? $_GET['DateFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $filter_date = (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '';
        $website = (isset($_GET['website'])) ? $_GET['website'] : '';
        $role_id = $_GET['role_id'];


        $condition = "";
        $table_map = [
            0 => 'b.source',
            1 => 'b.reference',
            3 => 'b.firstName',
            4 => 'b.surname',
            5 => 'b.price',
        ];

        $SQLreference = " AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GL %'";
        if (trim($reference) != "") {
            $SQLreference .= " AND b.reference='$reference' ";
        }

        // $SQLwebsite = "AND b.source='CTAP'";
        $SQLwebsite = "";
        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite .= " AND b.source='$website'";
        }

        $SQLstatus = " AND b.status='1' ";
            
        
        $SQLairport = "";
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND b.airport='" . $airport . "'";
        }

        $SQLFilterDate = "";
        
        if ($filter_date == "booking_at") {
            $SQLFilterDate = "and date(b.booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "departure_at") {
            $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "return_at") {
            $SQLFilterDate = "and date(b.return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= "AND b.show_status = 1";
        }
        

        $sql_count = "SELECT count(bp.id) as total FROM `tbl_booking_prices` bp LEFT JOIN `tbl_booking` b ON b.id=bp.booking_id WHERE 1=1  $SQLFilterDate $SQLreference $SQLstatus $SQLairport $SQLwebsite AND bp.booking_price != bp.orignal_price";
        $sql_data = "SELECT bp.*,b.reference, b.product_id, b.airport, b.source, b.firstName, b.surname,b.booked_at, b.depart_at, b.return_at,b.refund_amount,b.status FROM `tbl_booking_prices` bp LEFT JOIN `tbl_booking` b ON b.id=bp.booking_id WHERE 1=1  $SQLFilterDate $SQLreference $SQLstatus $SQLairport $SQLwebsite AND bp.booking_price != bp.orignal_price";

        // exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'b.source') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
            // pre($condition);
        }

        $sql_count = $sql_count . $condition;

        $sql_data = $sql_data . $condition;
        

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $OrderBy . $SortBy . $Limit;

        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        $totalll_count = 0;
        $grand_totall = array();
        
        // pre($sql_data);
        
        foreach ($result as $value) 
        {
            // if ($value->booking_price !== $value->orignal_price) 
            // {
                $row = array();

                $sql_data3 = "SELECT product_code,name, product_type FROM `tbl_products` where id='$value->product_id' LIMIT 1";
                $result3 = $this->db->query($sql_data3)->getRow();

                $product_name = "";

                if ($result3) {
                    $product_name = $result3->name;
                }
                $priceDiff=round($value->orignal_price-$value->booking_price,2);
                if ($priceDiff < 0) {
                    $row['DT_RowClass'] = 'high-price';
                }
            
                $row[] = $value->reference;
                $row[] = $value->source;
                $row[] = $value->airport . "\n" . $product_name;
                $row[] = $value->firstName . " " . $value->surname;
                $row[] = date("d-M-Y H:i:s", strtotime($value->booked_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
                $row[] = $value->booking_price;
                $row[] = $value->orignal_price;
                $row[] = $priceDiff; 

                $data[] = $row;
            // }
            
        }

        $totalll_count_result = $total_count->total;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function update_source()
    {
        $data = $this->request->getVar();
        $booking_id = $_GET['booking_id'];
        $source = $_GET['source'];
        $sql_query = "UPDATE `tbl_booking` SET source='$source' WHERE id='$booking_id' ";
        $result = $this->db->query($sql_query);

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'message' => 'Source updated successfully'
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function update_note()
    {
        $data = $this->request->getVar();
        $booking_id = $_GET['booking_id'];
        // $reason = date("d M, Y h:i:s A").': '.$_GET['reason'];
        $sql_query = "SELECT id, note_desc FROM `tbl_booking` WHERE id='$booking_id' ";
        $result = $this->db->query($sql_query)->getRow();
        // $reason = $_GET['reason'];
        $note_desc = ($result->note_desc)? (($_GET['note_desc'])? $result->note_desc.'<br>'.$_GET['note_desc']: $result->note_desc): $_GET['note_desc'];
        
        $sql_query = "UPDATE `tbl_booking` SET note_desc='$note_desc' WHERE id='$booking_id' ";
        $result = $this->db->query($sql_query);

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'message' => 'Note has been added successfully'
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    ////////////////////////////////////////

    public function bookings_report_supplier()
    {

        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = $_GET['reference'];
        $surname = $_GET['surname'];
        $CarRegistration = $_GET['CarRegistration'];
        $Email = $_GET['Email'];
        $status = $_GET['status'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $filter_date = $_GET['filter_date'];
        $website = $_GET['website'];
        $contactNumber = $_GET['contactNumber'];
        $role_id = $_GET['role_id'];

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

        $SQLreference = "";
        if (trim($reference) != "") {
            $SQLreference = " AND reference='$reference' ";
        }

        $SQLsurname = "";
        if (trim($surname) != "") {
            $SQLsurname = " AND (surname='$surname' OR firstName='$surname')";
        }

        $SQLCarRegistration = "";
        if (trim($CarRegistration) != "") {
            $SQLCarRegistration = " AND carReg='$CarRegistration' ";
        }


        $SQLEmail = "";
        if (trim($Email) != "") {
            $SQLEmail = " AND email='$Email' ";
        }

        $SQLcontactNumber = "";
        if (trim($contactNumber) != "") {
            $SQLcontactNumber = " AND contactNumber='$contactNumber' ";
        }

        $SQLwebsite = "";
        if (trim($website) != "") {
            $SQLwebsite = " AND source='$website' ";
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            $SQLstatus = " AND status='$status' ";
        }

        $SQLairport = "";
        if ($this->Airport != "*") {
            $SQLairport = " AND airport='" . $this->Airport . "'";
        }

        $SQLFilterDate = "";
        if ($SQLsurname != '' || $SQLCarRegistration != '' || $SQLEmail != '' || $SQLreference != '' || $SQLcontactNumber != '') {

        } else {
            if ($filter_date == "booking_at") {
                $SQLFilterDate = "and date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "departure_at") {
                $SQLFilterDate = "and date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "return_at") {
                $SQLFilterDate = "and date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            }
        }



        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE  third_party_rec='1'  $SQLFilterDate $SQLreference $SQLsurname $SQLCarRegistration $SQLEmail $SQLstatus $SQLairport $SQLwebsite $SQLcontactNumber ";
        $sql_data = "SELECT * FROM `tbl_booking`  WHERE third_party_rec='1'  $SQLFilterDate $SQLreference $SQLsurname $SQLCarRegistration $SQLEmail $SQLstatus $SQLairport $SQLwebsite $SQLcontactNumber ";

        // echo"$sql_data";

        //exit($sql_data);


        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'created_at') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }

        $sql_count = $sql_count . $condition;

        //exit($sql_count);

        $sql_data = $sql_data . $condition;


        //exit($sql_data);

        // return json_encode($sql_data);

        // exit;
        $this->AirportType = get_website_type($this->Airport);

        $total_count = $this->db->query($sql_count)->getRow();
        if (strval($role_id) > 1) {
            $OrderBy = " ORDER BY depart_at asc";
            if ($filter_date == "booking_at") {
                $OrderBy = " ORDER BY created_at asc";
            } else if ($filter_date == "departure_at") {
                $OrderBy = " ORDER BY depart_at asc";
            } else if ($filter_date == "return_at") {
                $OrderBy = " ORDER BY return_at asc";
            }
        } else {
            $OrderBy = " ORDER BY id desc";
        } //" ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = "";
            if ($result2) {
                $operator_name = $result2->description;
            }

            $sql_data2 = "SELECT product_code,name FROM `tbl_products` where id='$value->product_id' LIMIT 1";
            $result2 = $this->db->query($sql_data2)->getRow();
            $product_code = "";
            $product_name = "";
            if ($result2) {
                $product_code = $result2->product_code;
                $product_name = $result2->name;
            }

            if (strval($role_id) > 1) {

                if ($value->booking_type == "Cash") {
                    $badge = "badge badge-glow bg-success";
                    $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                } else {
                    $badge = "badge badge-glow bg-warning";
                    $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                }

                $row[] = $product_code . "\n" . $product_name;
                $row[] = $value->reference;
                $row[] = $value->firstName . " " . $value->surname;
                $row[] = $value->contactNumber;
                $row[] = date("d-M-Y", strtotime($value->depart_at));
                $row[] = date("H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y", strtotime($value->return_at));
                $row[] = date("H:i:s", strtotime($value->return_at));

                $row[] = $value->carMake . "\n" . $value->carModel . "\n" . $value->carColour;
                $row[] = $value->carReg;
                if ($this->AirportType == "AIRPORT") {
                    $row[] = $value->airport . " - " . $value->OutTerminal;
                    $row[] = $value->OutFltNo;
                    $row[] = $value->InFltNo;
                    $row[] = number_format($value->price - 1.95, 2);

                } else {
                    $row[] = $value->OutTerminal;
                    $row[] = $value->RetTerminal;
                    $row[] = number_format($value->price - 1.95, 2);
                }

                $row[] = $value->passenger;
                // $row[] = $value->show_status;

                $id = id_en($value->id);
                if ($value->show_status == 1) {
                    $show_action .= "<div class=\"btn-group\"><a class=\"btn btn-primary\" style=\"background: #7367F0;color: #fff;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\"> No Show</a></div>";
                }else{
                    $show_action = "<div class=\"btn-group\"><a class=\"btn btn-primary\" style=\"background: #7367F0;color: #fff;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\">Show</a></div>";
                }
                
                $row[] = $show_action;

                $id = id_en($value->id);
                $action = "<div class=\"btn-group\">
                    <a href=\"javascript:void(0);\" class=\"btn btn-primary   dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Actions
                    </a>
                    <div class=\"dropdown-menu\">
                      <a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>
                      <a class=\"dropdown-item\" onclick=\"print_card(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> Print Card</a>
                      <a class=\"dropdown-item\" onclick=\"print_card_new(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> New Print Card</a>
                    </div>
                  </div>";
                $row[] = $action;
            } else {
                $row[] = $value->reference;
                $row[] = $value->source;
                $row[] = $value->airport . "\n" . $product_name;
                $row[] = $value->firstName . " " . $value->surname;
                $row[] = date("d-M-Y H:i:s", strtotime($value->booked_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
                $row[] = $value->carReg;
                $row[] = $value->price;

                if ($value->status == 1) {
                    $badge = "badge badge-glow bg-success";
                    $row[] = "<span class='$badge'>Completed</span>";
                } elseif ($value->status == 0) {
                    $badge = "badge badge-glow bg-warning";
                    $row[] = "<span class='$badge'>Pending</span>";
                } elseif ($value->status == 2) {
                    $badge = "badge badge-glow bg-danger";
                    $row[] = "<span class='$badge'>Cancelled</span>";
                } elseif ($value->status == 3) {
                    $badge = "badge badge-glow bg-info";
                    $row[] = "<span class='$badge'>No Show</span>";
                }

                if ($value->booking_type == "Online") {
                    $badge = "badge badge-glow bg-success";
                    $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                } else {
                    $badge = "badge badge-glow bg-warning";
                    $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                }
                $row[] = $value->promocode;
                $row[] = $operator_name;
                // $row[] = $value->passenger;
                // $row[] = $value->show_status;

                $id = id_en($value->id);
                // $show_action = "<div class=\"btn-group\"><a class=\"btn btn-primary\" style=\"background: #7367F0;color: #fff;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\">Show</a></div>";
                // $show_action .= "<div class=\"btn-group\"><a class=\"btn btn-primary\" style=\"background: #7367F0;color: #fff;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\"> No Show</a></div>";
                // $row[] = $show_action;


                $id = id_en($value->id);
                $other_actions = '';
                if ($value->status == 0) {

                    $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";

                    $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('complete_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> Complete Booking</a>";

                } else if ($value->status == 1 || $value->status == 2) {
                    // $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('cancel_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='minus-circle'></i> Cancel Booking</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('make_refund',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Make a Refund</a>";
                    // if ($value->status == 1) {
                    //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('move_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='corner-up-left'></i> Move Booking</a>";
                    //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('edit_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather=\"edit\"></i> Amend Booking</a>";
                    // }
                    // $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('resend_email',`$id`);\" href=\"javascript:void(0)\"><i data-feather='send'></i> Resend Booking Confirmation</a>";
                    // $other_actions .= "<a class=\"dropdown-item\" target=\"_blank\" href=".base_url('bookings/booking_pdf?id='.$value->id)."><i data-feather='file'></i> Booking PDF</a>";
                }

                if ($value->status == 0) {
                    $action = "<div class=\"btn-group\">
                        <a href=\"javascript:void(0);\" class=\"btn btn-primary   dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                        Actions
                        </a>
                        <div class=\"dropdown-menu\">                      
                          $other_actions
                        </div>
                      </div>";
                        } else {
                            $action = "<div class=\"btn-group\">
                        <a class=\"dropdown-item btn btn-primary  \" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\">
                        view Booking
                        </a>
                        <div class=\"dropdown-menu\">                      
                          $other_actions
                        </div>
                    </div>";
                }

                $row[] = $action;
            }

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

    public function details()
    {
        $data = $this->request->getVar();
        $id = "";
        if (isset($data['id'])) {
            $id = $data['id'];
            $id = id_de($id);
        }
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id'";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }
        $data = [
            "page_title" => "Bookings",
            "booking" => $booking,
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Bookings", "status" => "active", "link" => true],
                ["href" => base_url('bookings/details'), "title" => "Booking Details", "status" => "", "link" => false]
            ]
        ];
        return view('booking/details', $data);
    }

    public function update_status()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $status = $data['status'];
        $price = $data['price'];
        $receipt_number = $data['receipt_number'];
        $booking_type = $data['booking_type'];
        if (trim($id) == "") {
            $output = ['status' => false, "message" => "invalid booking id"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($status) == "") {
            $output = ['status' => false, "message" => "invalid booking status"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($receipt_number) == "") {
            $output = ['status' => false, "message" => "Please enter payment transaction id"];
            return $this->setResponseFormat('json')->respond($output);
        }
        $sql = "UPDATE tbl_booking SET status='$status',price='$price',receipt_number='$receipt_number', booking_type='$booking_type' WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            $output = ['status' => true, "message" => "booking status successfully completed"];
        } else {
            $output = ['status' => false, "message" => "unexpected error on booking completed"];
        }

        return $this->setResponseFormat('json')->respond($output);
    }


    public function show_status()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $id = id_de($id);
        $status = $data['status'];
        $show_status = $data['show_status'];
        if ($status) {
            $sql_query = "UPDATE tbl_booking SET status='$status' WHERE id='$id'";
        }else{
            $sql_query = "UPDATE tbl_booking SET show_status='$show_status' WHERE id='$id'";
        }
        // pre($sql_query);
        $result = $this->db->query($sql_query);

        if ($result) {
            logActivity($this->user_id, $id ,'Booking status change', 'Status change successfully');
            $output = ['status' => true, "message" => "Status change successfully"];
        } else {
            $output = ['status' => false, "message" => "Unexpected error on status change"];
        }

        return $this->setResponseFormat('json')->respond($output);
    }


    public function upload()
    {

        echo "testing";


        $file = $this->request->getFile('fileToUpload');

        // Check if a file was actually uploaded
        if ($file == null) {
            return "No file uploaded.";
        }

        // Check if the file is valid
        if (!$file->isValid()) {
            return "Invalid file.";
        }

        // Check if the file has been moved successfully
        if (!$file->hasMoved()) {
            // Move the uploaded file to the writable/uploads directory
            $file->move(WRITEPATH . 'uploads');
            $filePath = WRITEPATH . 'uploads/' . $file->getName();

            // Read the CSV file and insert data into the database
            // $this->readCSVAndInsert($filePath);
            print_r($filePath);
            // return "File uploaded successfully.";
        } else {
            return "Error moving the file.";
        }


        $handle = fopen($filePath, 'r');

        // Connect to your database
        $db = \Config\Database::connect();

        // Assuming the first row is the header, read it and ignore
        $header = fgetcsv($handle, 1000, ',');

        // Loop through the rest of the rows
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Assuming a table structure where the first column is name and the second column is age
            // $name = $data[0];
            // $age = $data[1];

            // print_r($data);
            $producr_code = $data[1];


            //  $sql_data_tbl_booking="select id,operator_id from tbl_booking where product_code='$producr_code'";
            //  $result = $this->db->query($sql_data)->getRow();


            $sql_data = "select id,operator_id from tbl_products where product_code='$producr_code'";
            $result = $this->db->query($sql_data)->getRow();

            if (!empty($result)) { 

                $csv_ref = $data[3];
                $sql_data_tbl_booking = "select reference from tbl_booking where reference='$csv_ref'";
                $sql_data_tbl_booking_result = $this->db->query($sql_data_tbl_booking)->getRow();

                if (isset($sql_data_tbl_booking_result->reference) && !empty($sql_data_tbl_booking_result->reference)) {
                    $reference = $sql_data_tbl_booking_result->reference;
                } else {
                    $reference = "";

                }
                $product_id = $result->id;
                $operator_id_r = $result->operator_id;
                echo "<br>";
                echo $reference;
                echo "<br>";
                echo $csv_ref;


                // print_r($data);

                if ($csv_ref != $reference) {

                    // echo "not match";

                    // $string_to_replace = "\xEF\xBF\xBD";
                    // $replacement_string = "";

                    // // Use a loop to replace the string in each element
                    // for ($i = 0; $i < count($data); $i++) {
                    //     $data[$i] = str_replace($string_to_replace, $replacement_string, $data[$i]);
                    // }



                    print_r($data);
                    $departTime = $data[11];
                    $returnTime = $data[12];
                    $book_at = date("Y-m-d h:i:s", strtotime($data[8]));
                    // $depart_at = date("Y-m-d", strtotime($data[9]));
                    // $return_at = date("Y-m-d", strtotime($data[10]));


                    $depart_at = DateTime::createFromFormat('d/m/Y', $data[9]);
                    $depart_at = $depart_at->format('Y-m-d');
                    $return_at = DateTime::createFromFormat('d/m/Y', $data[10]);
                    $return_at = $return_at->format('Y-m-d');
                    // pre($depart_at);
                    $dataa = [
                        'price' => $data[13],
                        'reference' => $data[3],
                        'carReg' => $data[14],
                        'carMake' => $data[15],
                        'carModel' => $data[16],
                        'carColour' => $data[17],
                        'OutTerminal' => $data[18],
                        'RetTerminal' => $data[19],
                        'OutFltNo' => $data[20],
                        'InFltNo' => $data[21],
                        'firstName' => $data[4],
                        'surname' => $data[5],
                        'email' => $data[6],
                        'contactNumber' => $data[7],
                        'booked_at' => $book_at,
                        'depart_at' => $depart_at.' '.$departTime,
                        'return_at' => $return_at.' '.$returnTime,
                        'agent_id' => $data[2],
                        'airport' => $data[0],
                        'product_id' => $product_id,
                        'operator_id' => $operator_id_r,
                        'source' => 'File Import',
                        'booking_type' => 'Online',
                        'source' => 'CTAP'

                    ];
                    // pre($dataa);
                    // Insert data into the database
                    // $db->table('tbl_booking')->insert(['name' => $name, 'age' => $age]);

                    $db->table('tbl_booking')->insert($dataa);

                } else {

                    $sql = "delete from tbl_booking where reference='$reference' ";
                    $booking = $this->db->query($sql);
                    echo "<br>";
                    echo "del";

                }



            } //only valid data is inserted


        }

        // Close the file
        fclose($handle);

        unlink($filePath);

        exit;
        $db = db_connect();

        $validationRules = [
            // 'filename' => 'uploaded[filename]|max_size[filename,500]|ext_in[filename,csv,xlsx]',
            'myfile' => 'uploaded[myfile]|max_size[myfile,500]|ext_in[myfile,csv,xlsx]',
        ];

        if ($this->validate($validationRules)) {
            $file = $this->request->getFile('filename');

            try {
                $reader = ($file->getExtension() === 'csv') ? new Csv() : new Excel();
                $spreadsheet = $reader->load($file->getTempName());
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                $db->transStart();

                foreach ($sheetData as $rowData) {
                    // Sanitize and validate data
                    $data = [
                        'price' => $rowData[0],
                        'reference' => $rowData[1],
                        'carReg' => $rowData[2],
                        'carMake' => $rowData[3],
                        'carModel' => $rowData[4],
                        'carColour' => $rowData[5],
                        'OutTerminal' => $rowData[6],
                        'RetTerminal' => $rowData[7],
                        'OutFltNo' => $rowData[8],
                        'InFltNo' => $rowData[9],
                        'firstName' => $rowData[10],
                        'surname' => $rowData[11],
                        'email' => $rowData[12],
                        'contactNumber' => $rowData[13],
                        'booked_at' => $rowData[14],
                        'depart_at' => $rowData[15],
                        'return_at' => $rowData[16],
                        'agent_id' => $rowData[17],
                        'airport' => $rowData[18],
                        'product_id' => $rowData[19],
                        'source' => 'Dashboard',
                        'booking_type' => 'Online'

                    ];

                    $db->table('tbl_booking')->insert($data);
                }


                $db->transComplete();

                if ($db->transStatus() === false) {

                    return redirect()->back()->withInput()->with('error', 'Transaction failed.');
                }

                // return redirect()->with('success', 'CSV file imported successfully.');
                log_message('debug', 'File processed successfully.');
            } catch (\Exception $e) {

                // return redirect()->back()->withInput()->with('error', 'Error processing CSV file.');
                log_message('error', 'Exception during CSV processing: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Error processing CSV file.');
            }
        } else {

            echo "nothing";
            exit;

            // return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

    }


    public function print_card_new()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $id = id_de($id);
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id' LIMIT 1";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }

        //echo "<pre>";
        //print_r($booking);


        $website = "select * from tbl_websites WHERE `short_code` = '$booking->airport'";
        $website_details = $this->db->query($website)->getRow();
        //print_r($website_details);        


        $sql2 = "SELECT * FROM tbl_products WHERE `id`='$booking->product_id'";
        $product_details = $this->db->query($sql2)->getRow();
        //print_r($product_details);

        // $sql_query = "SELECT bad.*, pad.addon_name FROM `tbl_booking_addons` bad LEFT JOIN `tbl_product_addons` pad ON bad.addon_id=pad.id WHERE bad.booking_id='$id'";
        // $addons = $this->db->query($sql_query)->getResult();
        // $addon_name='';
        // if ($addons) {
        //     foreach ($addons as $key => $ad) {
        //         $addon_name .= $ad->addon_name.', ';
        //     }
        // }

        $html='<!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>'.$website_details->web_name.'</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
            <style>
            @page {
                size: A4 landscape;
                margin: 5m;
            }

            body {
                font-family: Arial, sans-serif;
                font-family: "Roboto", sans-serif;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 297mm;  /* A4 Landscape width */
                height: 200mm; /* A4 Landscape height */
                margin: 0 auto;
                padding: 2px;
                display: flex;
                flex-wrap: wrap;
                overflow: hidden; /* Prevents page overflow */
            }

            h2, h3 {
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                /* border: 1px solid #000; */
            }
            .declaration {
                margin-top: 20px;
                font-size: 12px;
            }
            </style>
            </head>

            <body>
            <div class="container">
              <table width="100" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                
                <tr>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%"><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                      <tbody>
                        <tr>
                          <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;text-transform: uppercase;">'.$website_details->web_name.'</td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;text-transform: uppercase;">Customer Details</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">NAME:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->firstName . ' ' . $booking->surname .'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MOBILE:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->contactNumber.'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->reference.'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">PRODUCT:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$product_details->product_code.'/'.$product_details->name.'</span></td>
                        </tr>
                        <tr style="display:none;">
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">VEHICLE DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="33.333%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                    <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->carReg.'</span></td>
                                  <td width="30.333%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MILEAGE</label>
                                    <input type="text"  style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="36.333%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN MILEAGE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                    <input type="text" value="'.$booking->carMake.'/'.$booking->carModel .'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                    <input type="text" value="'.$booking->carColour.'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">PARKED AT ZONE:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                        </tr>
                        <tr style="display:none;">
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">FLIGHT DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">DEPARTURE DATE:</label>
                                    <input type="text" value="'.date("d-M-Y", strtotime($booking->depart_at)).'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" value="'.date("H:i:s", strtotime($booking->depart_at)).'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.$booking->RetTerminal.'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN DATE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("d-M-Y", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("H:i:s", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.$booking->OutTerminal.'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding:5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN FLIGHT Number:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.$booking->OutFltNo.'"></td>
                        </tr>

                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">DECLARATION</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><p style="font-size: 12px;margin: 0;padding: 0;">By signing this agree that I have read and willing to be bound by the terms and  conditions of <strong>'.$website_details->web_name.'</strong></p>
                            <p style="font-size: 12px;margin: 0;padding: 0;"><strong>SIGN1:</strong> We confirm that no items valuables have been left in the vehicle</p>
                            <p style="font-size: 12px;margin: 0;padding: 0;"><strong>SIGN2:</strong> This is to confirm <strong>'.$website_details->web_name.'</strong> has delivered the VEHICLE without any damage.</p></td>
                        </tr>
                        
                        <tr>
                          <td style="padding:5px;padding-bottom:25px;"><label style="margin: 0; padding: 0; font-size: 12px;text-align: left;font-weight: 500;text-transform: uppercase;">SIGNATURE 1:</label>
                        </tr>
                        <tr>
                          <td style="padding:5px;padding-bottom:25px;"><label style="margin: 0; padding: 0; font-size: 12px;text-align: left;font-weight: 500;text-transform: uppercase;">SIGNATURE 2:</label>
                        </tr>
                      </tbody>
                    </table></td>
                  <td width="2%"></td>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%" ><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                        <tbody>
                      
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> DATE OF ARRIVAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.date("d-M-Y", strtotime($booking->return_at)).'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> TIME OF ARRIVAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.date("H:i:s", strtotime($booking->return_at)).'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> TERMINAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.$booking->RetTerminal.'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;text-align: left;"> DETAILS </td>
                      </tr>
                      <tr valign="top" align="left">
                        <td valign="top" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                              <tr>
                                <td width="50%" valign="top" align="center" style="padding: 5px;border-right: 1px solid #000;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                                  <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;;text-align:center;" value="'.$booking->reference.'"></td>
                                <td width="50%" valign="top" align="center" style="padding: 5px;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                  <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carReg.'"></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr valign="top" align="left">
                        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <td width="50%" valign="top" align="center" style="padding: 5px;border-right: 1px solid #000;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carMake .' '.$booking->carModel.'"></td>
                              <td width="50%" valign="top" align="center" style="padding: 5px;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carColour.'"></td>
                              </tbody>
                            
                          </table></td>
                      </tr>


                        

                        </tbody>
                      
                    </table></td>
                  <td width="2%"></td>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%;"><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                      <tbody>
                        <tr>
                          <td align="center" valign="middle" style="padding: 5px;font-size:15px;font-weight: 600;text-transform: uppercase;"> '.$website_details->web_name.' </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" ><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;">ONCE YOU HAVE COLLECTED ALL YOUR LUGGAGE PLEASE CALL US ON</h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 15px;font-weight: 600;text-align: center;text-transform: uppercase;">'.$product_details->driver_contact.'</h3></td>
                        </tr>
                        <tr height="30px" style="display:none;">
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;"></h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 15px;font-weight: 400;text-align: center;text-transform: uppercase;"> FOR AMMENDMENTS & CANCELLATIONS</h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;">'.$website_details->email.'</h3></td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">BOOKING DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.$booking->reference.'"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carReg).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carMake). '/'.htmlspecialchars($booking->carModel).'"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carColour).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN DATE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("d-M-Y", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("H:i:s", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.htmlspecialchars($booking->OutTerminal).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN FLIGHT Number:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->OutFltNo).'"></td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">PRESENT THIS VOUCHER ON YOUR RETURN</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;height: 100px;" align="left" valign="top"><p style="font-size: 16px;margin: 0;padding: 0;">FOR ANY CHANGES WHILE YOU ARE ABROAD, PLEASE EMAIL TO <a href="#" style="color: #000;clear: both;padding: 0;margin: 0;display: block;text-decoration: none;">'.htmlspecialchars($website_details->email).'</a></p></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;height: 90px;"  align="left" valign="top"><p style="font-size: 12px;margin: 0;padding: 0;">PLEASE NOTE THAT THERE WILL BE AN EXTRA CHARGE FOR ANY AMENDMENTS AND EXTRA DAYS PARKING. @ £20 PER DAY.</p></td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
                  </tbody>    
              </table>
            </div>
            </body>
            </html><script>
            {   
                window.print();    
            }
            </script>';
    

        echo $html;
        exit();

    }


    public function print_card()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $id = id_de($id);
        
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id'";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }

        $sql2 = "SELECT * FROM tbl_products WHERE `id`='$booking->product_id'";
        $product_details = $this->db->query($sql2)->getRow();

        $sql_query = "SELECT bad.*, pad.addon_name FROM `tbl_booking_addons` bad LEFT JOIN `tbl_product_addons` pad ON bad.addon_id=pad.id WHERE bad.booking_id='$id'";
        $addons = $this->db->query($sql_query)->getResult();
        $addon_name='';
        if ($addons) {
            foreach ($addons as $key => $ad) {
                $addon_name .= $ad->addon_name.', ';
            }
        }

        //         $html = '<div id="printCard">
        //         <style>
        //         hr {
        //             margin: 0 !important;
        //         }
        //         .print-card-check-box:before {
        //             content: "   ";
        //             padding:4px 10px 4px 10px;
        //             border: solid 1px black;
        //             margin-right:4px;
        //             margin-left:10px;
        //         }
        //         p {
        //                 margin: 5px 0px 5px 0px !important;
        //                 font-size: 15px;
        //         }
        //         @media print {
        //                         .page {page-break-after: always;}
        //                 }
        //         </style>
        // <div class="page">
        //     <div class="row">
        //         <div class="col-sm-12">

        //                 <p>Booking Reference: ' . $booking->reference . '</p>

        //             <hr>

        //                 <p>Dates: ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . ' to ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>

        //             <hr>

        //                 <p>Customer Name: ' . $booking->firstName . ' ' . $booking->surname . '</p>

        //             <hr>

        //                 <p>Phone Number: ' . $booking->contactNumber . '</p>

        //             <hr>

        //                 <p>Vehicle Registration: ' . $booking->carReg . '</p>

        //             <hr>

        //                 <p>Vehicle: '. $booking->carMake .' '. $booking->carModel .' '. $booking->carColour .'</p>

        //             <hr>

        //                 <p>Airport Terminal: '. $booking->OutTerminal .' </p>

        //             <hr>

        //                 <p>Return Flight Number: '. $booking->OutFltNo .'</p>

        //             <hr>

        //                 <p>Created: '.date("d-M-Y H:i:s", strtotime($booking->created_at)).' </p>

        //             <hr>

        //                 <p>Car Mileage: </p>

        //             <hr>

        //                 <p>Valuables Left In Car: <span class="print-card-check-box">SatNav</span> <span class="print-card-check-box">Mobile</span> <span class="print-card-check-box">Sunglasses</span> <span class="print-card-check-box">Other</span> </p>
        //             </br>

                //             <hr>

                //                 <p>Comments: </p>
        //                 <br>


                //             </br>

                //             <hr>
        //         </div>
        //     </div>
        //         <div class="row">           
        //                 <div class="col-sm-6" style="width:50%; float:left; display:inline;">
        //                     <img src="' . base_url('assets/car.jpg') . '" style=\"width:90%;padding:5%;\" />
        //                 </div>
        //                 <div class="col-sm-6" style="width:50%; float:left; display:inline;">
        //                     <br>
        //                         <h4>Delivery</h4>
        //                         <hr>
        //                         <p>Signed By:</p>
        //                         <br>
        //                         <hr>
        //                         <p>Driver:</p>
        //                         <br>
        //                         <hr>

                //                         <br>
        //                         <h4>Delivery</h4>
        //                         <hr>
        //                         <p>Signed By:</p>
        //                         <br>
        //                         <hr>
        //                         <p>Driver:</p>
        //                         <br>
        //                         <hr>
        //                         <br>
        //                         <p>Scratch = S<br>
        //                             Dent = D<br>
        //                             Crack = C<br>
        //                             Broken = B<br>
        //                             Hole = H<br>
        //                         </p>
        //                     </div>
        //                 </div>
        //         <div class="col-sm-12">
        //             <div class="row">
        //                 <p>*By booking online and signing this form, I agree to the terms and conditions</p>

                //             </div>
        //         </div>
        //     </div>
        //     <div class="page">
        //         <style>
        //         .rotate {
        //             font-size:150px;
        //             text-align: center;
        //         }
        //         </style>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <br>
        //         <p class="rotate">' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>
        //     </div>
        // </div>
        // <script>
        // {   
        //     window.print();    
        // }
        // </script>';

        $html = '<div id="printCard">
            <style>
            hr {
                margin: 0 !important;
            }
            .print-card-check-box:before {
                content: "   ";
                padding:4px 10px 4px 10px;
                border: solid 1px black;
                margin-right:4px;
                margin-left:10px;
            }
            p {
                    margin: 5px 0px 5px 0px !important;
                    font-size: 15px;
            }
            @media print {
                            .page {page-break-after: always;}
                    }
            </style>
            <div class="page">
            <div class="row">
            <div class="col-sm-12">

                    <p>Booking Reference: ' . $booking->reference . '</p>

                <hr>

                    <p>Dates: ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . ' to ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>

                <hr>

                    <p>Customer Name: ' . $booking->firstName . ' ' . $booking->surname . '</p>

                <hr>

                    <p>Phone Number: ' . $booking->contactNumber . '</p>

                <hr>

                    <p>Vehicle Registration: ' . $booking->carReg . '</p>

                <hr>

                <p>Vehicle: ' . $booking->carMake . ' ' . $booking->carModel . ' ' . $booking->carColour . '</p>

                <hr>
                
                    <p>Airport Terminal: ' . $booking->OutTerminal . '</p>

                <hr>

                    <p>Return Flight Number: ' . $booking->OutFltNo . ' </p>
                

                <hr>

                    <p>Created: ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '</p>

                <hr>

                    <p>Car Mileage: </p>

                <hr>
                    <p>Addons: '.$addon_name.'</p>

                <hr>

                    <p>Valuables Left In Car: <span class="print-card-check-box">SatNav</span> <span class="print-card-check-box">Mobile</span> <span class="print-card-check-box">Sunglasses</span> <span class="print-card-check-box">Other</span> </p>
                </br>

                <hr>

                    <p>Comments: '.$product_details->product_code.'/'.$product_details->name.'</p>
                    <br>


                </br>

                <hr>
            </div>
            </div>
            <div class="row">           
                <div class="col-sm-6" style="width:50%; float:left; display:inline;">
                    <img src="' . base_url('assets/car.jpg') . '" style="width:90%; padding:5%;" />
                </div>
                <div class="col-sm-6" style="width:50%; float:left; display:inline;">
                    <br>
                        <h4>Customer Vehicle Delivery</h4>
                        <hr>
                        <p>Signed By:</p>
                        <br>
                        <hr>
                        <p>Driver:</p>
                        <br>
                        <hr>

                        <br>
                        <h4>Customer Vehicle Collection</h4>
                        <hr>
                        <p>Signed By:</p>
                        <br>
                        <hr>
                        <p>Driver:</p>
                        <br>
                        <hr>
                        <br>
                        <p>Scratch = S<br>
                            Dent = D<br>
                            Crack = C<br>
                            Broken = B<br>
                            Hole = H<br>
                        </p>
                    </div>
                </div>
            <div class="col-sm-12">
                <div class="row">
                    <p>*By booking online and signing this form, I agree to the terms and conditions</p>

                </div>
            </div>
            </div>
            <div class="page">
            <style>
            .rotate {
                font-size:150px;
                text-align: center;
            }
            </style>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p class="rotate">' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>
            </div>
            </div>
            <script>
            {   
                window.print();    
            }
            </script>';

        echo $html;
        exit();
        // $output=['status'=>true,"html"=>$html];
        // return $this->setResponseFormat('json')->respond($output);

    } 


    public function print_cards_new()
    {
        $data = $this->request->getVar();
        $DateFrom = $_GET['datefrom'] ? $_GET['datefrom'] : '';
        $DateTo = $_GET['dateto'] ? $_GET['dateto'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $filter_date = $_GET['filter_date'];
        $SQLairport = "";
        if ($this->Airport != "*") {
            $SQLairport = " AND airport='" . $this->Airport . "'";
        }
        $SQLFilterDate = "";
        $SQLstatus = " AND status='1'";
        if ($filter_date == "booking_at") {
            $SQLFilterDate = "date(created_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "departure_at") {
            $SQLFilterDate = "date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "return_at") {
            $SQLFilterDate = "date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= " AND show_status='1'";
        }        
        $SQLoperator='';
        if ($this->operator_id != "*") {
            $SQLoperator = " AND operator_id='" . $this->operator_id . "'";
        }
        
        $SQLorderby = " ORDER BY depart_at asc";
        $sql = "SELECT id FROM `tbl_booking`  WHERE $SQLFilterDate $SQLairport $SQLoperator $SQLstatus $SQLorderby";
        $result = $this->db->query($sql)->getResult();
        $cards = "";
        for ($i = 0; $i < sizeof($result); $i++) {
            $id = $result[$i]->id;
            $cards .= $this->print_card_in_bulk_new($id);
        }
        // $cards .= "<script>{window.print();}</script>";

        $cards .= "<script>
            window.print();
            if (window.parent && window.parent.$) {
                window.parent.$('#print_cards_new').prop('disabled', false).text('Print New Cards');
            }
        </script>";

        exit($cards);
    }

    private function print_card_in_bulk_new($id)
    {
        $SQLoperator='';
        if ($this->operator_id != "*") {
            $SQLoperator = " AND operator_id='" . $this->operator_id . "'";
        }
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id' $SQLoperator ORDER BY depart_at asc";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) 
        {
            $booking = $booking[0];
        }
        $website = "select * from tbl_websites WHERE `short_code` = '$booking->airport'";
        $website_details = $this->db->query($website)->getRow();

        $sql2 = "SELECT * FROM tbl_products WHERE `id`='$booking->product_id'";
        $product_details = $this->db->query($sql2)->getRow();

        $html='<!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>'.$website_details->web_name.'</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
            <style>
            @page {
                size: A4 landscape;
                margin: 5m;
            }

            body {
                font-family: Arial, sans-serif;
                font-family: "Roboto", sans-serif;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 297mm;  /* A4 Landscape width */
                height: 200mm; /* A4 Landscape height */
                margin: 0 auto;
                padding: 2px;
                display: flex;
                flex-wrap: wrap;
                overflow: hidden; /* Prevents page overflow */
            }

            h2, h3 {
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                /* border: 1px solid #000; */
            }
            .declaration {
                margin-top: 20px;
                font-size: 12px;
            }
            </style>
            </head>

            <body>
            <div class="container">
              <table width="100" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                
                <tr>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%"><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                      <tbody>
                        <tr>
                          <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;text-transform: uppercase;">'.$website_details->web_name.'</td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;text-transform: uppercase;">Customer Details</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">NAME:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->firstName . ' ' . $booking->surname .'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MOBILE:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->contactNumber.'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->reference.'</span></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">PRODUCT:</label>
                            <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$product_details->product_code.'/'.$product_details->name.'</span></td>
                        </tr>
                        <tr style="display:none;">
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">VEHICLE DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="33.333%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                    <span style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;">'.$booking->carReg.'</span></td>
                                  <td width="30.333%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MILEAGE</label>
                                    <input type="text"  style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="36.333%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN MILEAGE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                    <input type="text" value="'.$booking->carMake.'/'.$booking->carModel .'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                    <input type="text" value="'.$booking->carColour.'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">PARKED AT ZONE:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                        </tr>
                        <tr style="display:none;">
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">FLIGHT DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">DEPARTURE DATE:</label>
                                    <input type="text" value="'.date("d-M-Y", strtotime($booking->depart_at)).'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" value="'.date("H:i:s", strtotime($booking->depart_at)).'" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.$booking->RetTerminal.'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN DATE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("d-M-Y", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("H:i:s", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.$booking->OutTerminal.'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding:5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN FLIGHT Number:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.$booking->OutFltNo.'"></td>
                        </tr>

                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">DECLARATION</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><p style="font-size: 12px;margin: 0;padding: 0;">By signing this agree that I have read and willing to be bound by the terms and  conditions of <strong>'.$website_details->web_name.'</strong></p>
                            <p style="font-size: 12px;margin: 0;padding: 0;"><strong>SIGN1:</strong> We confirm that no items valuables have been left in the vehicle</p>
                            <p style="font-size: 12px;margin: 0;padding: 0;"><strong>SIGN2:</strong> This is to confirm <strong>'.$website_details->web_name.'</strong> has delivered the VEHICLE without any damage.</p></td>
                        </tr>
                        
                        <tr>
                          <td style="padding:5px;padding-bottom:25px;"><label style="margin: 0; padding: 0; font-size: 12px;text-align: left;font-weight: 500;text-transform: uppercase;">SIGNATURE 1:</label>
                        </tr>
                        <tr>
                          <td style="padding:5px;padding-bottom:25px;"><label style="margin: 0; padding: 0; font-size: 12px;text-align: left;font-weight: 500;text-transform: uppercase;">SIGNATURE 2:</label>
                        </tr>
                      </tbody>
                    </table></td>
                  <td width="2%"></td>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%" ><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                        <tbody>
                      
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> DATE OF ARRIVAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.date("d-M-Y", strtotime($booking->return_at)).'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> TIME OF ARRIVAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.date("H:i:s", strtotime($booking->return_at)).'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;"> TERMINAL </td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="75" style="margin: 0; padding: 5px;font-size: 20px;font-weight: 600;text-transform: uppercase;">'.$booking->RetTerminal.'</td>
                      </tr>
                      <tr>
                        <td align="center" valign="middle" height="30" style="padding: 5px;font-size: 15px;font-weight: 600;text-align: left;"> DETAILS </td>
                      </tr>
                      <tr valign="top" align="left">
                        <td valign="top" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                              <tr>
                                <td width="50%" valign="top" align="center" style="padding: 5px;border-right: 1px solid #000;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                                  <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;;text-align:center;" value="'.$booking->reference.'"></td>
                                <td width="50%" valign="top" align="center" style="padding: 5px;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                  <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carReg.'"></td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr valign="top" align="left">
                        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                            <td width="50%" valign="top" align="center" style="padding: 5px;border-right: 1px solid #000;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carMake .' '.$booking->carModel.'"></td>
                              <td width="50%" valign="top" align="center" style="padding: 5px;height: 76px;"><label style="margin: 0; padding: 0; font-size: 16px;text-align: center;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                <input type="text" style="border: none; outline: none; padding: 8px; font-size: 16px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;text-align:center;" value="'.$booking->carColour.'"></td>
                              </tbody>
                            
                          </table></td>
                      </tr>


                        

                        </tbody>
                      
                    </table></td>
                  <td width="2%"></td>
                  <td style="border:1px solid #fff;padding: 1px;" valign="top" width="32%;"><table width="100%" border="1" cellspacing="0" cellpadding="0" >
                      <tbody>
                        <tr>
                          <td align="center" valign="middle" style="padding: 5px;font-size:15px;font-weight: 600;text-transform: uppercase;"> '.$website_details->web_name.' </td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" ><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;">ONCE YOU HAVE COLLECTED ALL YOUR LUGGAGE PLEASE CALL US ON</h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 15px;font-weight: 600;text-align: center;text-transform: uppercase;">'.$product_details->driver_contact.'</h3></td>
                        </tr>
                        <tr height="30px" style="display:none;">
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;"></h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 15px;font-weight: 400;text-align: center;text-transform: uppercase;"> FOR AMMENDMENTS & CANCELLATIONS</h3></td>
                        </tr>
                        <tr>
                          <td align="center" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: center;text-transform: uppercase;">'.$website_details->email.'</h3></td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">BOOKING DETAILS</h3></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">BOOKING REF:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.$booking->reference.'"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">CAR REG:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carReg).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="50%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">MAKE/MODEL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carMake). '/'.htmlspecialchars($booking->carModel).'"></td>
                                  <td width="50%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">COLOUR:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->carColour).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tbody>
                                <tr>
                                  <td width="40%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN DATE:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("d-M-Y", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;border-right: 1px solid #000;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TIME:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.date("H:i:s", strtotime($booking->return_at)).'"></td>
                                  <td width="30%" style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">TERMINAL:</label>
                                    <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.6;font-weight: 400;" value="'.htmlspecialchars($booking->OutTerminal).'"></td>
                                </tr>
                              </tbody>
                            </table></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;"><label style="margin: 0; padding: 0; font-size: 13px;text-align: left;font-weight: 500;text-transform: uppercase;">RETURN FLIGHT Number:</label>
                            <input type="text" style="border: none; outline: none; padding: 8px; font-size: 13px; width: 100%; background: transparent;padding: 0;opacity: 0.7;font-weight: 400;" value="'.htmlspecialchars($booking->OutFltNo).'"></td>
                        </tr>
                        <tr>
                          <td align="left" valign="middle" height="25"><h3 style="margin: 0; padding: 5px;font-size: 12px;font-weight: 600;text-align: left;">PRESENT THIS VOUCHER ON YOUR RETURN</h3></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;height: 100px;" align="left" valign="top"><p style="font-size: 16px;margin: 0;padding: 0;">FOR ANY CHANGES WHILE YOU ARE ABROAD, PLEASE EMAIL TO <a href="#" style="color: #000;clear: both;padding: 0;margin: 0;display: block;text-decoration: none;">'.htmlspecialchars($website_details->email).'</a></p></td>
                        </tr>
                        <tr>
                          <td style="padding: 5px;height: 90px;"  align="left" valign="top"><p style="font-size: 12px;margin: 0;padding: 0;">PLEASE NOTE THAT THERE WILL BE AN EXTRA CHARGE FOR ANY AMENDMENTS AND EXTRA DAYS PARKING. @ £20 PER DAY.</p></td>
                        </tr>
                      </tbody>
                    </table></td>
                </tr>
                  </tbody>    
              </table>
            </div>
            </body>
            </html>';
        return $html;
    }

    function print_dards()
    {
        $data = $this->request->getVar();
        $DateFrom = $_GET['datefrom'] ? $_GET['datefrom'] : '';
        $DateTo = $_GET['dateto'] ? $_GET['dateto'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $filter_date = $_GET['filter_date'];


        $SQLairport = "";
        if ($this->Airport != "*") {
            $SQLairport = " AND airport='" . $this->Airport . "'";
        }

        $SQLFilterDate = "";
        $SQLstatus = " AND status='1'";
        if ($filter_date == "booking_at") {
            $SQLFilterDate = "date(created_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "departure_at") {
            $SQLFilterDate = "date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        } else if ($filter_date == "return_at") {
            $SQLFilterDate = "date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            $SQLstatus .= " AND show_status='1'";
        }

        $SQLoperator='';
        if ($this->operator_id != "*") {
            $SQLoperator = " AND operator_id='" . $this->operator_id . "'";
        }
        
        $SQLorderby = " ORDER BY depart_at asc";

        $sql = "SELECT id FROM `tbl_booking`  WHERE $SQLFilterDate $SQLairport $SQLoperator $SQLstatus $SQLorderby";
        // print_r($sql);die;
        $result = $this->db->query($sql)->getResult();
        // pre($result);
        $cards = "";
        for ($i = 0; $i < sizeof($result); $i++) {
            $id = $result[$i]->id;
            $cards .= $this->print_card_in_bulk($id);
        }
        // foreach ($result as $key => $r) 
        // {
        //     $id = $r->id;
        //     $cards .= $this->print_card_in_bulk($id);
        //     print_r($cards);die;
        // }

        // echo'print cards<pre>';print_r($cards);die;
        $cards .= "<script>{window.print();}</script>";
        exit($cards);
    }

    private function print_card_in_bulk($id)
    {
        $AUTH=session()->get('AUTH');

        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id' ORDER BY depart_at asc";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }

        $sql2 = "SELECT * FROM tbl_products WHERE `id`='$booking->product_id'";
        $product_details = $this->db->query($sql2)->getRow();
        // echo'print card booking<pre>';print_r($booking);die;
        

        $html = '<div id="printCard">
        <style>
        hr {
            margin: 0 !important;
        }
        .print-card-check-box:before {
            content: "   ";
            padding:4px 10px 4px 10px;
            border: solid 3px black;
            margin-right:4px;
            margin-left:10px;
        }
        p {
                margin: 5px 0px 5px 0px !important;
                font-size: 15px;
        }
        @media print {
                        .page {page-break-after: always;}
                }
        </style>
        <div class="page">
            <div class="row">
                <div class="col-sm-12">

                        <p>Booking Reference: ' . $booking->reference . '</p>

                    <hr>

                        <p>Dates: ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . ' to ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>

                    <hr>

                        <p>Customer Name: ' . $booking->firstName . ' ' . $booking->surname . '</p>

                    <hr>

                        <p>Phone Number: ' . $booking->contactNumber . '</p>

                    <hr>

                        <p>Vehicle Registration: ' . $booking->carReg . '</p>

                    <hr>

                    <p>Vehicle: ' . $booking->carMake . ' ' . $booking->carModel . ' ' . $booking->carColour . '</p>

                    <hr>
                    
                        <p>Airport Terminal: ' . $booking->OutTerminal . '</p>

                    <hr>

                        <p>Return Flight Number: ' . $booking->OutFltNo . ' </p>
                    

                    <hr>

                        <p>Created: ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '</p>

                    <hr>

                        <p>Car Mileage: </p>

                    <hr>

                        <p>Valuables Left In Car: <span class="print-card-check-box">SatNav</span> <span class="print-card-check-box">Mobile</span> <span class="print-card-check-box">Sunglasses</span> <span class="print-card-check-box">Other</span> </p>
                    </br>

                    <hr>

                    <p>Comments: '.$product_details->product_code.'/'.$product_details->name.'</p>                <br>


                    </br>

                    <hr>
                </div>
            </div>
                <div class="row">           
                    <div class="col-sm-6" style="width:50%; float:left; display:inline;">
                        <img src="' . base_url('assets/car.jpg') . '" style="width:90%; padding:5%;" />
                    </div>
                    <div class="col-sm-6" style="width:50%; float:left; display:inline;">
                        <br>
                            <h4>Customer Vehicle Delivery</h4>
                            <hr>
                            <p>Signed By:</p>
                            <br>
                            <hr>
                            <p>Driver:</p>
                            <br>
                            <hr>

                            <br>
                            <h4>Customer Vehicle Collection</h4>
                            <hr>
                            <p>Signed By:</p>
                            <br>
                            <hr>
                            <p>Driver:</p>
                            <br>
                            <hr>
                            <br>
                            <p>Scratch = S<br>
                                Dent = D<br>
                                Crack = C<br>
                                Broken = B<br>
                                Hole = H<br>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <p>*By booking online and signing this form, I agree to the terms and conditions</p>

                        </div>
                    </div>
            </div>
            <div class="page">
                <style>
                .rotate {
                    font-size:150px;
                    text-align: center;
                }
                </style>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <p class="rotate">' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '</p>
            </div>';
            if ($AUTH['airport'] == 'SOP'):
            $html .='<div class="page">
                <style>
                .carinfo {
                    font-size:20px;
                    text-align: center;
                    width: 30%;
                    float: left;
                    padding: 10px;
                    border: 2px dashed #000;
                }
                </style>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
                <p class="carinfo">' . $booking->firstName . ' ' . $booking->surname . '<br><br>'. $booking->carMake . ' ' . $booking->carModel . '<br><br>
                ' . $booking->carReg . '<br>' . date("d/m", strtotime($booking->return_at))  . '</p>
            </div>';
            endif;

        echo'</div>';
        return $html;
    }

    function print_collected_slip()
    {
        $data = $this->request->getVar();
        $id = $_GET['booking_id'];
        // $id = id_de($id);
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id'";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }

        $sql2 = "SELECT * FROM tbl_products WHERE `id`='$booking->product_id'";
        $product_details = $this->db->query($sql2)->getRow();

        $sql3 = "SELECT * FROM tbl_booking_collect WHERE `booking_id`='$id'";
        $charges = $this->db->query($sql3)->getRow();

        $sql4 = "SELECT * FROM tbl_websites WHERE `short_code`='$booking->airport'";
        $website = $this->db->query($sql4)->getRow();
        // date("d-M-Y H:i:s", strtotime($charges->date_added))
        $html ='<style>
            @media print {
                body {
                    margin: 0;
                    padding: 20px;
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                .print-button {
                    display: none;
                }
            }

            .container {
                max-width: 800px;
                margin: 0 auto;
                border: 2px solid #000;
                padding: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .logo {
                max-width: 200px;
                margin-bottom: 10px;
            }

            .section {
                margin-bottom: 25px;
            }

            .bold {
                font-weight: bold;
            }

            .grid-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            p, span{
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }

            td, th {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            .footer {
                text-align: center;
                margin-top: 30px;
                font-size: 10px;
                color: #666;
            }
        </style>
        <div class="container">
            <div class="header">
                <img src="'. base_url('logos/'.$website->logo) .'" alt="Logo" class="logo">
                <h1>'. $website->web_name .'</h1>
                '.$website->footer.'
                <p>Phone: '. $website->customer_service.' | Email: '. $website->email.'</p>
            </div>

            <div class="grid-container">
                <div class="section">
                    <h2>Customer Information</h2>
                    <p><span class="bold">Name:</span> '. $booking->firstName . ' ' . $booking->surname .'<br>
                    <span class="bold">Email:</span> '. $booking->email .'<br>
                    <span class="bold">Phone:</span> '. $booking->contactNumber .'<br>
                    <span class="bold">Vehicle:</span> '. $booking->carMake . ' ' . $booking->carModel . ' ' . $booking->carColour . '</p>
                </div>

                <div class="section">
                    <h2>Booking Details</h2>
                    <p><span class="bold">Booking Reference:</span> '. $booking->reference .'<br>
                    <span class="bold">Departure Date:</span> ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) .'<br>
                    <span class="bold">Return Date:</span> ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '<br>
                    <span class="bold">Produc Detail:</span> '.$product_details->product_code.'/'.$product_details->name.'</p>
                </div>
            </div>
            <div class="section">
                <p style="text-align: right">Date: '.date("d-M-Y H:i:s") .'</p>
                <h2>Payment Information</h2>
                <table>
                    <tr>
                        <th>Late Charges</th>
                        <th>'.$website->cur.$charges->late_charges.'</th>
                    </tr>
                </table>
            </div>
            <div class="footer">
                <p>Thank you for choosing '. $website->legal_name .' | '. $website->domain.'</p>
            </div>
        </div>';
        $html .= "<script>{window.print();}</script>";
        exit($html);
    } 

    public function get_record()
    {
        $data = $this->request->getVar();
        $action = $data['action'];
        $id = "";
        if (isset($data['id'])) {
            $id = $data['id'];
            $id = id_de($id);
        }
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id'";
        $booking = $this->db->query($sql)->getResult();
        if ($booking) {
            $booking = $booking[0];
        }
        $modal = true;
        $response = "";


        if (isset($_GET['from'])) {
            $from = $_GET['from'];
        } else {
            $sql_data="SELECT * FROM tbl_settings";
            $settings=$this->db->query($sql_data)->getRow();
            $from = $settings->smtpuser;//no_reply@parkingmanagment.com
        }
        if (isset($_GET['webtype'])) {
            $webtype = $_GET['webtype'];
        } else {
            $webtype = "Cruise Ports";
        }

        if ($action == "cancel_booking") {
            $details = $this->get_cancel_booking_html($booking);
        } else if ($action == "complete_booking") {
            $details = $this->get_booking_details($booking);
        } else if ($action == "make_refund") {
            $details = $this->get_make_refund_html($booking);
        } else if ($action == "edit_booking") {
            $details = $this->get_edit_booking_html($booking);
        } else if ($action == "move_booking") {
            $details = $this->create_booking2($booking);
        } else if ($action == "resend_email") {
            /////////////////////////////////////////////////////////
            $sql_res = "SELECT * FROM tbl_websites WHERE `short_code`='$booking->airport'";
            $booking_airport = $this->db->query($sql_res)->getRow();

            $booking_airport_webtype = $booking_airport->type;
            $booking_airport_webtype = strtolower($booking_airport_webtype);

            /////////////////////////////////////////////////////////
            $response = send_email($booking->email, "Your Parking Booking Confirmation", $booking->id, $from, $booking_airport_webtype);
            $modal = false;
            if ($response) {
                $response = "Email successfully sent " . $booking->email;
            } else {
                $response = "unable to send email " . $booking->email;
            }
            $details = "";
        } else if ($action == "view_booking") {
            $details = $this->get_view_booking($booking);
        } else if ($action == "view_driver_booking") {
            $details = $this->get_view_driver_booking($booking);
        }
        $output = ['status' => true, 'details' => $details, "action" => $action, "message" => $response, "modal" => $modal];
        return $this->setResponseFormat('json')->respond($output);
    }


    public function cancel_booking()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $id = id_de($id);
        $cancellation_fee = $data['cancellation_fee'];
        // $refund_amount = $data['refund_amount'];
        // $reverse_transfer = $data['reverse_transfer'];
        $reason = $data['reason'];
        $status = "2";

        if (trim($id) == "") {
            $output = ['status' => false, "message" => "invalid booking id"];
            return $this->setResponseFormat('json')->respond($output);
        } else{
            // if (trim($refund_amount) == "") {
            //     $output = ['status' => false, "message" => "Please enter refund amount"];
            //     return $this->setResponseFormat('json')->respond($output);
            // } else 
            if (trim($cancellation_fee) == "") {
                $output = ['status' => false, "message" => "Please enter cancellation fee"];
                return $this->setResponseFormat('json')->respond($output);
            } else if (trim($reason) == "") {
                $output = ['status' => false, "message" => "please enter cancellation reason"];
                return $this->setResponseFormat('json')->respond($output);
            }
        }
        $sql = "UPDATE tbl_booking SET status='$status',cancellation_fee='$cancellation_fee',reason='$reason' WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result) {
            logActivity($this->user_id, $id ,'Canceled Booking', 'booking successfully canceled');
            $output = ['status' => true, "message" => "booking successfully canceled"];
        } else {
            $output = ['status' => false, "message" => "unexpected error on booking canceled"];
        }
        return $this->setResponseFormat('json')->respond($output);
    }


    public function make_refund()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $id = id_de($id);
        $refund_amount = $data['refund_amount'];
        $reason = $data['reason'];
        $status = "4";

        if (trim($id) == "") {
            $output = ['status' => false, "message" => "invalid booking id"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($refund_amount) == "") {
            $output = ['status' => false, "message" => "Please enter refund amount"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($reason) == "") {
            $output = ['status' => false, "message" => "please enter refund reason"];
            return $this->setResponseFormat('json')->respond($output);
        }
        $sql = "UPDATE tbl_booking SET refund_amount='$refund_amount',reason='$reason', status='$status' WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            logActivity($this->user_id, $id ,'Refund Booking', 'booking successfully refund');
            $output = ['status' => true, "message" => "booking successfully refund"];
        } else {
            $output = ['status' => false, "message" => "unexpected error on booking refund"];
        }
        return $this->setResponseFormat('json')->respond($output);
    }


    public function edit_booking()
    {
        $data = $this->request->getVar();
        // pre($data);
        $id = $data['id'];
        $id = id_de($id);
        $data['id'] = $id;
        $data['depart_at'] = date("Y-m-d H:i:s", strtotime($data['depart_at']));
        $data['return_at'] = date("Y-m-d H:i:s", strtotime($data['return_at']));
        extract($data);
        if (trim($id) == "") {
            $output = ['status' => false, "message" => "invalid booking id"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($firstName) == "") {
            $output = ['status' => false, "message" => "Please enter firstName"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($surname) == "") {
            $output = ['status' => false, "message" => "please enter surname"];
            return $this->setResponseFormat('json')->respond($output);
        }
        unset($data['csrf_test_name']);
        $sql = "UPDATE tbl_booking SET " . implode(', ', array_map(function ($key, $value) {
            return "$key = '$value'";
        }, array_keys($data), $data)) . " WHERE id = '$id' LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            logActivity($this->user_id, $id ,'Amend Booking', 'booking successfully amend');
            $output = ['status' => true, "message" => "booking successfully amend"];
        } else {
            $output = ['status' => false, "message" => "unexpected error on booking refund"];
        }
        return $this->setResponseFormat('json')->respond($output);
    }


    private function get_edit_booking_html($booking)
    {
        $product = get_product($booking->product_id);
        $operator_name = "";
        if ($product) {
            $operator_name = $product->name;
        }
        $agents = get_agents();
        $agentHtml='';
        foreach ($agents as $code => $name) {
            // $source = $agent.' - Dashboard';
            $code1 = $code.' - Dashboard';
            if ($booking->source == 'Dashboard' || $booking->source =='CPD' || $booking->source =='P4U'
                || $booking->source =='Holiday Extras' || $booking->source =='FreeToMove' || $booking->source =='ParkVia' || $booking->source =='CTAP' || $booking->source == 'Park&Fly' || $booking->source == 'JBF' || $booking->source == 'YTE' || $booking->source == 'HCP' || $booking->source == 'goairportparking.com') {
                $code1 = $code;
            }
            $selected = ($booking->source == $code1) ? 'selected="selected"' : '';
            $agentHtml .='<option value="'.$code.'" '.$selected.'>'.$name.'</option>';
        }
        $bookingTypeHtml='';
        $btypes = get_booking_type();
        foreach ($btypes as $key => $btype) {
            $selected = ($booking->booking_type == $key) ? 'selected="selected"' : '';
            $bookingTypeHtml .='<option value="'.$key.'" '.$selected.'>'.$btype.'</option>';
        }

        $trafficSourceHtml='';
        $tsources = get_traffic_sources();
        foreach ($tsources as $key => $tsource) {
            $selected = ($booking->traffic_source == $key) ? 'selected="selected"' : '';
            $trafficSourceHtml .='<option value="'.$key.'" '.$selected.'>'.$tsource.'</option>';
        }
        $sql = "SELECT * from `tbl_websites`";
        $websites = $this->db->query($sql)->getResult();
        foreach ($websites as $key => $w) {
            $selected = ($booking->source == $w->domain) ? 'selected="selected"' : '';
            $agentHtml .='<option value="'.$w->domain.'" '.$selected.'>'.$w->web_name.'</option>';
        }
        // <div class="col-md-6">
        //     <div class="mb-1">
        //         <label>Agent</label>
        //         <select class="form-select select2" name="source">
        //             '.$agentHtml.'
        //         </select>
        //     </div>
        // </div>
        $Availability = '<section id="outline-button">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Car Park Availability</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">   

                                <div class="col-md-3">
                                    <div class="mb-1">
                                        <label>Reference</label>
                                        <input type="text" name="reference" class="form-control" value="' . $booking->reference . '">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-1"> 
                                        <label>Airport</label>
                                        <input type="text" readonly disabled class="form-control" value="' . $booking->airport . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Car Park</label>
                                        <input type="text" readonly disabled class="form-control" value="' . $operator_name . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Arrival Date</label>
                                        <input type="text" name="depart_at" class="form-control flatpickr-date-time" value="' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Departure Date</label>
                                        <input type="text" name="return_at" class="form-control flatpickr-date-time" value="' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-1">
                                        <label>Price</label>
                                        <input type="text" name="price" class="form-control" value="' . $booking->price . '">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-1">
                                        <label>Agent</label>
                                        <select class="form-select select2" name="source">
                                            '.$agentHtml.'
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-1">
                                        <label>Booking Type</label>
                                        <select class="form-select" name="booking_type">
                                            '.$bookingTypeHtml.'
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-1">
                                        <label>Traffic Source</label>
                                        <select class="form-select select2" name="traffic_source">
                                            '.$trafficSourceHtml.'
                                        </select>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>                          
                </section>';

        $Customer = '<section id="outline-button">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Customer Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">   

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>First Name</label>
                                        <input type="text" name="firstName" class="form-control" value="' . $booking->firstName . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Last Name</label>
                                        <input type="text" name="surname" class="form-control" value="' . $booking->surname . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Email</label>
                                        <input type="text" name="email" class="form-control" value="' . $booking->email . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Telephone</label>
                                        <input type="text" name="contactNumber" class="form-control" value="' . $booking->contactNumber . '">
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>                          
                </section>';
        $Vehicle = '<section id="outline-button">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Vehicle Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">   

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Registraion</label>
                                        <input type="text" name="carReg" class="form-control" value="' . $booking->carReg . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Model</label>
                                        <input type="text" name="carModel" class="form-control" value="' . $booking->carModel . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Make</label>
                                        <input type="text" name="carMake" class="form-control" value="' . $booking->carMake . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Colour</label>
                                        <input type="text" name="carColour" class="form-control" value="' . $booking->carColour . '">
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>                          
                </section>';
        $flight = '<section id="outline-button">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Flight Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">   

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Return Terminal</label>
                                        <input type="text" name="RetTerminal" class="form-control" value="' . $booking->RetTerminal . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1"> 
                                        <label>Departure Terminal</label>
                                        <input type="text" name="OutTerminal" class="form-control" value="' . $booking->OutTerminal . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Return Flight Number</label>
                                        <input type="text" name="InFltNo" class="form-control" value="' . $booking->InFltNo . '">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label>Departure Flight Number</label>
                                        <input type="text" name="OutFltNo" class="form-control" value="' . $booking->OutFltNo . '">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-1">
                                        <label>Internal Note</label>
                                        <textarea name="reason" class="form-control" row="5">' . $booking->reason . '</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>                          
                </section>';
        $html = $Availability . $Customer . $Vehicle . $flight;
        return $html;
    }

    private function get_make_refund_html($booking) 
    {
        $html = '<div class="row">
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Price Paid</label>
                    <input type="text" value="' . $booking->price . '" name="price" class="form-control" readonly="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Refund Amount (£)</small></label>
                    <input type="number" name="refund_amount" class="form-control" value="'. $booking->price .'" placeholder="0.00">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Reason for refund?</label>
                    <textarea name="reason" class="form-control"></textarea>
                </div>
            </div>
        </div>';
        return $html;
    }

    private function get_cancel_booking_html($booking)
    {
        $html = '<div class="row">
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Price Paid</label>
                    <input type="text" value="' . $booking->price . '" name="price" class="form-control" readonly="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Cancellation Fee <small>(Max cancellation fee: £15)</small></label>
                    <input type="number" name="cancellation_fee" class="form-control" value="15.00" data-max-cancellation-fee="15.00" min="0.00" max="15.00">
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="mb-1">
                    <label>Reason for cancelling?</label>
                    <textarea name="reason" class="form-control"></textarea>
                </div>
            </div>
        </div>';
        // <div class="col-md-12 cancel-refund">
        //         <div class="mb-1">
        //             <label>Enter Refund Amount <small>(Max refund amount: £' . $booking->price . ')</small></label>
        //             <input type="number" name="refund_amount" class="form-control" value="' . $booking->price . '" placeholder="0.00" id="refund-amount">
        //         </div>
        //     </div>
        return $html;
    }

    //     private function get_view_booking($booking)
//     {
//         $operator = get_operator($booking->operator_id);
//         $airports = get_airports();
//         return '<section id="outline-button">
//   <div class="row">
//     <div class="col-12">
//       <div class="card">
//         <div class="card-header">
//           <h4 class="card-title">Booking Details</h4>
//         </div>
//         <div class="card-body">

    //           <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Operator</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             <span class="label label-info text-uppercase badge badge-glow bg-warning">
//                                 ' . $operator->description . '
//                             </span>
//                         </div>
//                                                 <div class="col-md-3">
//                                 <strong>Location</strong>
//                             </div>
//                             <div class="col-md-3 mb-1">
//                             <span class="label label-info text-uppercase badge badge-glow bg-warning">
//                                 United Kingdom                            
//                             </span>
//                             </div>
//                                             </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Reference</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             <b>' . $booking->reference . '</b>
//                         </div>
//                                                 <div class="col-md-3">
//                             <strong>Customer</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                            ' . $booking->firstName . " " . $booking->surname . '                            
//                         </div>

    //                     </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Booked On</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '                        
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             <strong>Car Park</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             <span class="label label-info text-uppercase badge badge-glow bg-info">
//                             ' . $airports[$booking->airport] . '         
//                             </span>                  
//                         </div>
//                     </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Arrival</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . '                             
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             <strong>Departure</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '                        
//                         </div>
//                     </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Vehicle Reg</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->carReg . '                       
//                         </div>

    //                         <div class="col-md-3 mb-1">
//                             <strong>Telephone Number</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->contactNumber . '                        
//                         </div>
//                     </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Email</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->email . '                       
//                         </div>

    //                         <div class="col-md-3 mb-1">
//                             <strong>Transaction ID</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->transaction_id . '                        
//                         </div>
//                     </div>
//                     <div class="row">
//                         <div class="col-md-3 mb-1">
//                             <strong>Car Modal</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->carModel . '                       
//                         </div>

    //                         <div class="col-md-3 mb-1">
//                             <strong>Car Colour</strong>
//                         </div>
//                         <div class="col-md-3 mb-1">
//                             ' . $booking->carColour . '                        
//                         </div>
//                     </div>
//                 <div class="row">
//                     <div class="col-md-3 mb-1">
//                         <strong>Car Manufacturer</strong>
//                     </div>
//                     <div class="col-md-3 mb-1">
//                         ' . $booking->carMake . '                       
//                     </div>

    //                     <div class="col-md-3 mb-1">
//                         <strong>Departure Terminal</strong>
//                     </div>
//                     <div class="col-md-3 mb-1">
//                         ' . $booking->OutTerminal . '                        
//                     </div>
//                 </div>
//                 <div class="row">
//                     <div class="col-md-3 mb-1">
//                         <strong>Return Terminal</strong>
//                     </div>
//                     <div class="col-md-3 mb-1">
//                         ' . $booking->RetTerminal . '                       
//                     </div>

    //                     <div class="col-md-3 mb-1">
//                         <strong>Departure Flight Number</strong>
//                     </div>
//                     <div class="col-md-3 mb-1">
//                         ' . $booking->OutFltNo . '                        
//                     </div>
//                 </div>
//                 <div class="row">
//                     <div class="col-md-3 mb-1">
//                         <strong>Return Flight Number</strong>
//                     </div>
//                     <div class="col-md-3 mb-1">
//                         ' . $booking->InFltNo . '                       
//                     </div>
//                 </div>




    //                 </div>

    //         </div>
//       </div>
//     </div>
//   </div>';
//     }



    private function get_view_booking($booking)
    {
        $operator = get_operator($booking->operator_id);
        $opt_desc = '';
        if ($operator) {
            $opt_desc = $operator->description;
        }

        $show_status = ($booking->show_status == 1)? 'Show' : 'No Show';

        $airports = get_airports();
        $product = get_product($booking->product_id);
        $product_name = ( $product)? $product->name:'';

        $output = '<section id="outline-button">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="py-1">
                                                    <h4 class="card-title">Booking Details</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Reference</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <b>' . $booking->reference . '</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Arrival</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . '                             
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Departure</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '                        
                                            </div>
                                        </div>
                                       
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Operator</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                                    ' . $opt_desc . '
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Booked On</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '                        
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Car Park</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <span class="label label-info text-uppercase badge badge-glow bg-info">
                                                ' . $booking->airport . " " . $product_name . '         
                                                </span>                  
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Transaction ID</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->transaction_id . '                        
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>AMOUNT</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->price . '                        
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="py-1">
                                                    <h4 class="card-title">Customer Detail</h4>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Customer</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                ' . $booking->firstName . " " . $booking->surname . '                            
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <strong>Email</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    ' . $booking->email . '                       
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <strong>Telephone Number</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    ' . $booking->contactNumber . '                        
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Location</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                                    United Kingdom                            
                                                </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <strong>Passenger</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    ' . $booking->passenger . '                        
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <strong>Show Status</strong>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    ' . $show_status . '                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="py-1">
                                                    <h4 class="card-title">Vehicle Detail</h4>
                                                </div>
                                            </div>
                                        </div>
                        
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Car Manufacturer</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->carMake . '                       
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Car Modal</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->carModel . '                       
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Car Colour</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->carColour . '                        
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Vehicle Reg</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                ' . $booking->carReg . '                       
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <strong>Internal Note</strong>
                                            </div>
                                            <div class="col-md-6 mb-1">
                                                <button type="button" class="btn btn-primary  addNote" data-id="'. $booking->id .'" data-reason="'. $booking->note_desc .'">Add Note</button>
                                            </div>
                                            <div class="col-md-12 mb-1">
                                                ' . $booking->note_desc . '                       
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <strong>Reason Of Cancellation/Refund</strong>
                                            </div>
                                            <div class="col-md-12 mb-1">
                                                ' . $booking->reason . '                       
                                            </div>
                                        </div>

                                    </div>
                                </div>
                    
                    ';

        // Adding an if-else condition
        if (stripos($opt_desc, 'PORT') !== false && stripos($opt_desc, 'AIRPORT') === false) {
            $output .= '
                    <div class="col-4">
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Cruise Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutTerminal . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Name of Cruise Ship</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->RetTerminal . '                        
                            </div>
                        </div>
                    </div>';
        } else {
            $output .= ' 
                    <div class="col-4">
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Return Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->RetTerminal . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Departure Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutTerminal . '                        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Return Flight Number</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutFltNo . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Departure Flight Number</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->InFltNo . '                        
                            </div>
                        </div>
                    </div>
                </div>
                </div>';
        }


        // Append the rest of the HTML
        $output .= ' </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

        $id = id_en($booking->id);
        $other_actions = "";

        $AUTH=session()->get('AUTH');
        if ($AUTH['role_id'] != 3) 
        {
           
            if ($booking->status == 1 || $booking->status == 2) {
                // $other_actions = "<a class=\"dropdown-item\" style=\"display: inline;\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('cancel_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='minus-circle'></i> Cancel Booking</a>";
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('make_refund',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Make a Refund</a>";
                if ($booking->status == 1) {
                    $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('move_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='corner-up-left'></i> Move Booking</a>";
                    $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('edit_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather=\"edit\"></i> Amend Booking</a>";

                }
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('resend_email',`$id`);\" href=\"javascript:void(0)\"><i data-feather='send'></i> Resend Booking Confirmation</a>";
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" target=\"_blank\" href=" . base_url('bookings/booking_pdf?id=' . $booking->id) . "><i data-feather='file'></i> Booking PDF</a>";
            }
            if ($booking->status == 2 || $booking->status == 4 || $booking->status == 5)  
            {
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"change_status(`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Change Status</a>";
            }
        }

        if ($booking->show_status == 1) {
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\">No Show</a>";
        }else{
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\">Show</a>";
        }
        if ($AUTH['role_id'] != 3) 
        {
            $other_actions .="<a class=\"dropdown-item btn-sm sms-btn\" href=\"javascript:void(0);\" data-phone='".$booking->contactNumber."' style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\">SMS</a>";
            $other_actions .="<a class=\"dropdown-item btn-sm history-btn\" href=\"javascript:void(0);\" data-email='".$booking->email."' style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\">History</a>";
        }
        $output .= $other_actions;

        $id = id_en($booking->id);

        return $output;
    }

    private function get_view_driver_booking($booking)
    {
        $operator = get_operator($booking->operator_id);
        $opt_desc = '';
        if ($operator) {
            $opt_desc = $operator->description;
        }

        $show_status = ($booking->show_status == 1)? 'Show' : 'No Show';

        $airports = get_airports();
        $product = get_product($booking->product_id);

        $depart_text='<div class="col-md-12 mb-1">
                        <b>' . check_departReturn_status(date('Y-m-d',strtotime($booking->return_at))) . '</b>
                    </div>';
        $depart_css='';
        $return_text='<div class="col-md-12 mb-1">
                        <b>' . check_departReturn_status(date('Y-m-d',strtotime($booking->depart_at))). '</b>
                    </div>';
        $return_css='';
        if (date('d-M-Y') == date("d-M-Y", strtotime($booking->return_at))) 
        {
            $depart_css='style="box-shadow: 0 0 10px 3px red;border-radius: 10px"';
            // $depart_text = '<div class="col-md-4 mb-1">
            //                     <strong>Today</strong>
            //                 </div>
            //                 <div class="col-md-8 mb-1">
            //                     <b>' . date("H:i:s A", strtotime($booking->return_at)) . '</b>
            //                 </div>';
        }
        // elseif (date('d-m-Y') > date("d-m-Y", strtotime($booking->return_at))) 
        // {
        //     $depart_text = '<div class="col-md-12 mb-1">
        //                         <b>Passed ' . getDaysBetweenDates(date('Y-m-d'), $booking->return_at) . ' days</b>
        //                     </div>';
        // }
        if (date('d-M-Y') == date("d-M-Y", strtotime($booking->depart_at))) 
        {
            $return_css='style="box-shadow: 0 0 10px 3px red;border-radius: 10px"';
            // $return_text = '<div class="col-md-4 mb-1">
            //                     <strong>Today</strong>
            //                 </div>
            //                 <div class="col-md-8 mb-1">
            //                     <b>' . date("H:i:s A", strtotime($booking->depart_at)) . '</b>
            //                 </div>';
        }
        // elseif (date('d-m-Y') > date("d-m-Y", strtotime($booking->depart_at))) 
        // {
        //     $return_text = '<div class="col-md-12 mb-1">
        //                         <b>Passed ' . getDaysBetweenDates(date('Y-m-d'), $booking->depart_at) . ' days</b>
        //                     </div>';
        // }

        $output = '<section id="outline-button">
                    <div class="row driver">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-9">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="py-1">
                                                            <h4 class="card-title">Booking Details</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Reference</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <b>' . $booking->reference . '</b>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Arrival</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . '                             
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Departure</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '                        
                                                    </div>
                                                </div>
                                               
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Operator</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                                            ' . $opt_desc . '
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Booked On</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>AMOUNT</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        ' . $booking->price . '                        
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <strong>Car Park</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <span class="label label-info text-uppercase badge badge-glow bg-info" style="font-size: 80%">
                                                        ' . $booking->airport . "<br> " . $product->name . '         
                                                        </span>                  
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="py-1">
                                                            <h4 class="card-title">Customer Detail</h4>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Customer</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                        ' . $booking->firstName . " " . $booking->surname . '                            
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Email</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->email . '                       
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Telephone Number</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->contactNumber . '                        
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Location</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                        <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                                            United Kingdom                            
                                                        </span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Passenger</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->passenger . '                        
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Show Status</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $show_status . '                        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="py-1">
                                                            <h4 class="card-title">Vehicle Detail</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Car Manufacturer</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->carMake . '                       
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Car Modal</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->carModel . '                       
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Car Colour</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->carColour . '                        
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Vehicle Reg</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            ' . $booking->carReg . '                       
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-1">
                                                            <strong>Internal Note</strong>
                                                        </div>
                                                        <div class="col-md-6 mb-1">
                                                            <button type="button" class="btn btn-primary  addNote" data-id="'. $booking->id .'" data-reason="'. $booking->note_desc .'">Add Note</button>
                                                        </div>
                                                        <div class="col-md-12 mb-1">
                                                            ' . $booking->note_desc . '                       
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 mb-1">
                                                            <strong>Reason Of Cancellation/Refund</strong>
                                                        </div>
                                                        <div class="col-md-12 mb-1">
                                                            ' . $booking->reason . '                       
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="row">
                                            <div class="col-6 return-box" '.$return_css.'>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="py-1">
                                                            <h4 class="card-title">Departure</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    ' .$return_text. '
                                                </div>
                                            </div>
                                            <div class="col-6 departure-box" '.$depart_css.'>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="py-1">
                                                            <h4 class="card-title">Return</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    ' .$depart_text. '
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                    ';

        // Adding an if-else condition
        if (stripos($opt_desc, 'PORT') !== false && stripos($opt_desc, 'AIRPORT') === false) {
            $output .= '
                    <div class="col-4">
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Cruise Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutTerminal . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Name of Cruise Ship</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->RetTerminal . '                        
                            </div>
                        </div>
                    </div>';
        } else {
            $output .= ' 
                    <div class="col-4">
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Return Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->RetTerminal . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Departure Terminal</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutTerminal . '                        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Return Flight Number</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->InFltNo . '                       
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <strong>Departure Flight Number</strong>
                            </div>
                            <div class="col-md-6 mb-1">
                                ' . $booking->OutFltNo . '                        
                            </div>
                        </div>
                    </div>
                </div>
                </div>';
        }


        // Append the rest of the HTML
        $output .= ' 
                        </div>
                    </div>
                </div>
            </div>
        </section>';

        $id = id_en($booking->id);
        $other_actions = "";
        if ($booking->status == 1 || $booking->status == 2) {
            // $other_actions = "<a class=\"dropdown-item\" style=\"display: inline;\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>";
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('cancel_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='minus-circle'></i> Cancel Booking</a>";
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('make_refund',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Make a Refund</a>";
            if ($booking->status == 1) {
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('move_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='corner-up-left'></i> Move Booking</a>";
                $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('edit_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather=\"edit\"></i> Amend Booking</a>";

            }
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_booking_modal('resend_email',`$id`);\" href=\"javascript:void(0)\"><i data-feather='send'></i> Resend Booking Confirmation</a>";
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" target=\"_blank\" href=" . base_url('bookings/booking_pdf?id=' . $booking->id) . "><i data-feather='file'></i> Booking PDF</a>";
        }
        if ($booking->status == 2 || $booking->status == 4 || $booking->status == 5)  
        {
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"change_status(`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Change Status</a>";
        }

        if ($booking->show_status == 1) {
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\">No Show</a>";
        }else{
            $other_actions .= "<a class=\"dropdown-item btn-sm\" style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\">Show</a>";
        }
        $other_actions .="<a class=\"dropdown-item btn-sm sms-btn\" href=\"javascript:void(0);\" data-phone='".$booking->contactNumber."' style=\"display: inline;background: #7367F0;color: #fff;margin: 0 10px 0 10px;\">SMS</a>";
        $output .= $other_actions;

        $id = id_en($booking->id);

        return $output;
    }


    private function get_booking_details($booking)
    {
        $operator = get_operator($booking->operator_id);
        $airports = get_airports();
        $description = ($operator)? $operator->description:'';
    
        return '<section id="outline-button">
            <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h4 class="card-title">Booking Details</h4>
                    </div>
                    <div class="card-body">
                      
                      <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <strong>Operator</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                            ' . $description  . '
                                        </span>
                                    </div>
                                                            <div class="col-md-3">
                                            <strong>Location</strong>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                        <span class="label label-info text-uppercase badge badge-glow bg-warning">
                                            United Kingdom                            
                                        </span>
                                        </div>
                                                        </div>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <strong>Reference</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <b>' . $booking->reference . '</b>
                                    </div>
                                                            <div class="col-md-3">
                                        <strong>Customer</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                       ' . $booking->firstName . " " . $booking->surname . '                            
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <strong>Booked On</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        ' . date("d-M-Y H:i:s", strtotime($booking->created_at)) . '                        
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <strong>Car Park</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <span class="label label-info text-uppercase badge badge-glow bg-info">
                                        ' . $airports[$booking->airport] . '         
                                        </span>                  
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <strong>Arrival</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        ' . date("d-M-Y H:i:s", strtotime($booking->depart_at)) . '                             
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <strong>Departure</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        ' . date("d-M-Y H:i:s", strtotime($booking->return_at)) . '                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <strong>Vehicle Reg</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        ' . $booking->carReg . '                       
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <strong>Telephone Number</strong>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        ' . $booking->contactNumber . '                        
                                    </div>
                                </div>
                            </div>
                    </div>
                  </div>
                </div>
            </div>
            </section><section id="outline-button">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                   
                    <div class="card-body">

                     <div class="row">   
                        <div class="col-md-6" style="display: none;">
                            <div class="mb-1"> 
                                <label>Parking Status</label>
                                <select class="select2" id="status">
                                    <option value="0">Pending</option>
                                    <option selected value="1">Completed</option>                                
                                    <option value="2">Cancelled</option>
                                    <option value="3">No Show</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-1"> 
                                <label>Amount Charged</label>
                                <input type="text" class="form-control" name="price" id="price" value="' . $booking->price . '">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-1"> 
                                <label>Transaction Id</label>
                                <input type="text" class="form-control" name="receipt_number" id="receipt_number" value="" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-1"> 
                                <label>Select Type</label>
                                <select name="booking_type" class="form-control">
                                    <option value="Online">Online</option>
                                    <option value="Arrival">Arrival</option>
                                </select>
                            </div>
                        </div>

                        

                        <div class="col-md-6">
                            <div class="mb-1"> 
                                <a href="javascript:void(0);" onclick="updateParkingStatus(' . $booking->id . ')" class="btn btn-primary  waves-float waves-light">Complete Booking</a>
                            </div>
                        </div>      
                     </div>
                        
                    </div>
                  </div>
                </div>
              </div>
            </section>';
    }


    public function update_move_booking()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $price = $data['price'];
        $product_id = $data['product_id'];
        if (trim($id) == "") {
            $output = ['status' => false, "message" => "invalid booking id"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($price) == "") {
            $output = ['status' => false, "message" => "invalid booking price"];
            return $this->setResponseFormat('json')->respond($output);
        } else if (trim($product_id) == "") {
            $output = ['status' => false, "message" => "invalid booking product id"];
            return $this->setResponseFormat('json')->respond($output);
        }
        $sql="SELECT id,product_code,name,operator_id FROM `tbl_products` WHERE id='$product_id'";
        $product = $this->db->query($sql)->getRow();

        $sql = "UPDATE tbl_booking SET price='$price',product_id='$product_id',operator_id='$product->operator_id' WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            logActivity($this->user_id, $id ,'Move Booking', 'move booking successfully completed');
            $output = ['status' => true, "message" => "move booking successfully completed"];
        } else {
            $output = ['status' => false, "message" => "unexpected error on move booking completed"];
        }

        return $this->setResponseFormat('json')->respond($output);
    }


    public function booking_pdf()
    {
        $data = $this->request->getVar();
        $id = trim($data['id']);
        $sql = "SELECT * FROM tbl_booking WHERE `id`='$id'";
        $booking = $this->db->query($sql)->getResult();

        if ($booking) {
            $booking = $booking[0];
        } else {
            echo "Invalid booking iD";
            exit();
        }

        $sql2 = "SELECT * FROM tbl_products where id = " . $booking->product_id;
        $product = $this->db->query($sql2)->getResult();
        if ($product) {
            $product = $product[0];
        } else {
            echo "Invalid product iD";
            exit();
        }
        logActivity($this->user_id, $id ,'PDF Booking', 'booking pdf generated successfully');

        $html = "<!DOCTYPE html>
            <html>
            <head>
                <title>Booking Confirmation</title>
            </head>
            <body>
                <h1>Booking Confirmation</h1>

                <h2>Booking Details</h2>
                <p><strong>Booking Reference:</strong>$booking->reference</p>
                <p><strong>Total Amount Paid:</strong> $booking->price</p>
                <p><strong>Name:</strong> $booking->firstName $booking->surname </p>
                <p><strong>Email:</strong> $booking->email </p>
                <p><strong>Contact Number:</strong> $booking->contactNumber</p>

                <h2>Car Information</h2>
                <p><strong>Car Reg:</strong> $booking->carReg </p>
                <p><strong>Car Make:</strong> $booking->carMake </p>
                <p><strong>Car Model:</strong> $booking->carModel </p>
                <p><strong>Car Colour:</strong> $booking->carColour </p>

                <h2>Car Park Information</h2>
                <p><strong>Airport:</strong> $booking->airport </p>
                <p><strong>Car Park Name:</strong> $booking->product_id </p>
                <p><strong>Telephone Number:</strong> $booking->contactNumber </p>
                <p><strong>Arrive at car park:</strong> $booking->depart_at </p>
                <p><strong>Return to car park:</strong> <$booking->return_at </p>


 

                <h2>Information</h2>
                <p><strong>Useful Information:</strong> $product->useful_information </p>
                <p><strong>Parking Facility Contact:</strong> $product->parking_facility_contact </p>
                <p><strong>What To Do When You Arrive:</strong> $product->what_to_do_when_you_arrive </p>
                <p><strong>What To Do When You Return:</strong> $product->what_to_do_when_you_return </p>
                <p><strong>Security Information:</strong> $product->security_information </p>

            </body>
            </html>";
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($booking->id . "-" . $booking->reference . "-" . time());
    }



    public function bookings_capacity()
    {
        $data = [
            "page_title" => "Bookings",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "View Booking Capacity", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select id,name from tbl_products";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['agents'] = $agent_result;

        // $sql_data = "select * from tbl_booking";
        // $promo_result = $this->db->query($sql_data)->getResult();
        // $data['promocode'] = $promo_result;
        return view('booking/reports/report', $data);
    }

    public function bookings_capacity_report()
    {
        $AUTH=session()->get('AUTH');
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        // $code = $_GET['code'];
        $operator = (isset($_GET['operator'])) ? $_GET['operator'] : '';
        $product = $_GET['product'];
        $get_limiter_time=$_GET['get_limiter_time'];
        // $promotional_name = $_GET['promotional_name'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'id',
            // 2 => 'agent',
            // 3 => 'promotional_name',
            // 4 => 'valid_from',
            // 5 => 'valid_to'
        ];

        $SQLproduct = "";
        $product_code = "";
        $get_limiter_time_val=$get_limiter_time;
        if (trim($product) != "") {
            $SQLproduct = " AND   `product_id`= '$product' ";
            $product_code = " AND   `id`= '$product' ";

            // $get_limiter_time="select get_limiter_time  from tbl_products where id='$product'";
            // $get_limiter_time_res = $this->db->query($get_limiter_time)->getRow();
            // $get_limiter_time_val=$get_limiter_time_res->get_limiter_time;

        }

        $SQLoperator = "";
        $operator_id = "";

        if (!empty($operator) && $operator != "*") {
            // $SQLoperator = " AND operator_id='$operator' ";
            // $operator_id = " AND operator_id='$operator' ";
            // $my_opertor_id=" AND id='$operator' ";

            $operatorList = array_map(function($a) {
                return "'" . trim(addslashes($a)) . "'";
            }, $operator);

            $SQLoperator = " AND operator_id IN (" . implode(',', $operatorList) . ")";  

            $operator_id = " AND operator_id IN (" . implode(',', $operatorList) . ")";  
            $my_opertor_id=" AND id IN (" . implode(',', $operatorList) . ")";  

            // $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE `id`= $operator";
            // $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
            // $get_limiter_time_val = $sql_data_operator_res->get_limiter_time;
        }

        // $SQLpromotional_name = "";
        // if (trim($promotional_name) != "") {
        //     $SQLpromotional_name = " AND promotional_name='$promotional_name' ";
        // }

        // $SQLwebsite = "";
        // if (trim($website) != "") {
        //     $SQLwebsite = " AND website='$website' ";
        // }
        $date1 = new \DateTime($DateFrom);
        $date2 = new \DateTime($DateTo);
        $interval = $date1->diff($date2);
        $number_of_days = $interval->format('%a') + 1;
        $data = array();

        $capacityLow=0;
        if ($AUTH['role_id'] == 11) 
        {
            $capacityLow = 30;
        }


        for ($i = 0; $i < $number_of_days; $i++) 
        {

            $SQLFilterDate = "";
            // $SQLFilterDate = "(valid_from>='$DateFrom' AND VALID_TO<='$DateTo')";
            // if ($filter_date == "valid_from") {
            // $SQLFilterDate = "(`depart_at` <= '$DateFrom 23:59:00' AND return_at>'$DateFrom $get_limiter_time_val:00')  AND status='1' AND show_status=1 ";
            $SQLFilterDate = "(`depart_at` < '$DateFrom 23:59:00' AND return_at>='$DateFrom $get_limiter_time_val:00')  AND status='1' AND show_status=1 ";
           // $SQLFilterDate = "'$DateFrom' AND '$DateTo'";

            // } else if ($filter_date == "valid_to") {
            //     $SQLFilterDate = "date(valid_to) BETWEEN '$DateFrom' AND '$DateTo'";
            // }

            $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE $SQLFilterDate  $SQLoperator $SQLproduct ";
            $sql_data = "SELECT count(*) as bookingCount FROM `tbl_booking` WHERE $SQLFilterDate $SQLoperator $SQLproduct  ";
            // exit($sql_data);


            if (!empty($search)) {
                foreach ($table_map as $key => $val) {
                    if ($table_map[$key] == 'created_at') {
                        $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                    } else {
                        $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                    }
                }
                $condition .= " )";
            }

            $sql_count = $sql_count . $condition;

            $sql_data = $sql_data . $condition;

            $total_count = $this->db->query($sql_count)->getRow();
            $OrderBy = " ORDER BY id DESC"; //" ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
            $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];
            $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
            $sql_data .= $OrderBy . $SortBy . $Limit;
            $result = $this->db->query($sql_data)->getRow();
            $row = array();
            // pre($sql_data);

                $sql_data_all_capacity = "SELECT sum(capacity) as all_capacity,capacity_threshold_one FROM `tbl_products` WHERE 1=1 $product_code $operator_id";
                // $sql_data_all_capacity = "SELECT sum(capacity) as all_capacity,capacity_threshold_one FROM `tbl_products`   WHERE  1=1  $product_code";

                $result_all_capacity = $this->db->query($sql_data_all_capacity)->getRow();

                $all_capacity = $result_all_capacity->all_capacity;
                $capacity_threshold_one = $result_all_capacity->capacity_threshold_one;

                ////////////////////////////////////////////////////
                if(!empty($my_opertor_id))
                {
                    $sql_data_operator = "SELECT  SUM(capacity) AS total_capacity FROM `tbl_operators` WHERE 1=1 $my_opertor_id";
                    $sql_data_operator_res = $this->db->query($sql_data_operator)->getRow();
                    // pre($sql_data_operator_res);
                    $all_capacity = $sql_data_operator_res->total_capacity;
                }

                ///////////////////////////////////////////

                if($all_capacity==0 or empty($all_capacity))
                {
                    $percentage=0;
                }else{
                    // print_r($result->bookingCount);
                    // pre($all_capacity);
                    $percentage = round(($result->bookingCount /$all_capacity)*100, 2);
                }
                $row = array();

                $row[] = date("m/d/Y", strtotime($DateFrom));
                // $row[] = date("m/d/Y", strtotime($DateTo));
                // per-".$percentage." thresh-".$capacity_threshold_one
                $res = $result->bookingCount - $capacityLow;
                $res = ($res > 0)? $res : $result->bookingCount;
                if ($percentage >= $capacity_threshold_one) 
                { 
                    $badge = "badge badge-glow bg-warning"; 
                    $row[] = "<span class='$badge'>" . $res . "</span>"; 
                }else { 
                    $badge = "badge badge-glow bg-success"; 
                    $row[] = "<span class='$badge'>" . $res . "</span>"; 
                }

                // $row[] = $result->bookingCount;
                $row[] = $all_capacity;
                $operatorStr = (!empty($operator)) ? implode(',', $operator): '';
                $downloadURL=base_url('bookings/capacity/download?date='.$DateFrom.'&time='.$get_limiter_time.'&product='.$product.'&operator='.$operatorStr);
                if ($AUTH['role_id'] == 11) 
                {
                    $downloadURL = '';
                }
                $row[] = '<a href="'.$downloadURL.'" class="btn btn-primary btn-sm"><i data-feather=\'download\'></i></a>';
                $data[] = $row;

            $DateFrom = new \DateTime($DateFrom);

            $DateFrom=$DateFrom->modify('+1 day');
            $DateFrom = $DateFrom->format('Y-m-d');
            if ($AUTH['role_id'] == 11) { $capacityLow += 30;}

        } // end of iteration
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_airport_websites()
    {
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $sql_data = "select id, short_code, domain from tbl_websites";
        if ($airport != '*') {
            $sql_data = "select id, short_code, domain from tbl_websites WHERE `short_code` = '$airport'";
        }
        
        $result = $this->db->query($sql_data)->getResult();

        echo'<option value="*">All</option>';
        foreach ($result as $key => $r) {
            echo '<option value="'.$r->domain.'">'.$r->domain.'</option>';
        }
        $agents = get_agents();
        foreach ($agents as $code => $name) {
            echo "<option value='".$code."'>".$name."</option>";
        }
    }

    public function get_drivers()
    {
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $driver_id = $_GET['driver_id'] ? $_GET['driver_id'] : '';

        $sql_data = "select id,name from tbl_drivers WHERE airport='$airport'";
        $result = $this->db->query($sql_data)->getResult();

        // echo'<option value="">Select Driver</option>';
        foreach ($result as $key => $r) {
            $selected =($r->id == $driver_id)? 'selected="selected"':'';
            echo '<option value="'.$r->id.'" '.$selected.'>'.$r->name.'</option>';
        }
    }

    public function mark_collected()
    {
        $booking_id = $_GET['booking_id'] ? $_GET['booking_id'] : '';
        $driver_id = $_GET['driver_id'] ? $_GET['driver_id'] : '';
        $late_charges = $_GET['late_charges'] ? $_GET['late_charges'] : '';
        $description = $_GET['description'] ? $_GET['description'] : '';
        $delete = $_GET['delete'] ? $_GET['delete'] : '';

        $sql_data = "select * from tbl_booking_collect WHERE id='$booking_id'";
        $exist = $this->db->query($sql_data)->getRow();
        if ($delete) {
            $sql_query="DELETE FROM `tbl_booking_collect` WHERE id='$booking_id'";
        }else{
            
            $sql_data = "select * from tbl_booking_collect WHERE booking_id='$booking_id'";
            $collected = $this->db->query($sql_data)->getRow();
            // pre($collected);
            if ($collected) 
            {
                // $sql_query = "UPDATE `tbl_booking_collect` SET `late_charges`='$late_charges', `description`='$description' WHERE booking_id='$booking_id'";
                $sql_query = "INSERT INTO `tbl_booking_collect`(`booking_id`, `driver_id`, `late_charges`, `description`,`status`) VALUES ('$booking_id', '$driver_id', '$late_charges', '$description','returned')";
            }else{
                $sql_query = "INSERT INTO `tbl_booking_collect`(`booking_id`, `driver_id`, `late_charges`, `description`,`status`) VALUES ('$booking_id', '$driver_id', '$late_charges', '$description','collected')";
            }
            
            // if ($exist) {
            //     $sql_query = "UPDATE `tbl_booking_collect` SET `driver_id`='$driver_id',`late_charges`='$late_charges', `description`='$description' WHERE booking_id='$booking_id'";
            // }
        }
        // print_r($sql_query);
        $result = $this->db->query($sql_query);
        logActivity($this->user_id, $id ,'Mark collect', 'Booking successfully mark collected');
        $json=['status'=>true,"message"=>"Booking successfully mark collected"];
         return $this->response->setJSON($json);
    }

    public function get_customer_history()
    {
        $email = $_GET['email'] ? $_GET['email'] : '';

        $sql_data = "SELECT id,reference,airport,booked_at, depart_at, return_at FROM tbl_booking WHERE email='$email' HAVING COUNT(email) > 1";
        $booking = $this->db->query($sql_data)->getResult();

        $response = "";
        $html = "";
        if($booking):
            foreach ($booking as $key => $r) {
                $html .= "<tr>
                    <td>$r->reference</td>                        
                    <td>$r->airport</td>                        
                    <td>$r->booked_at</td>
                    <td>$r->depart_at</td>
                    <td>$r->return_at</td>
                    </tr>";
            }
        else:
            $response='Did not find any history';
        endif;
        
        $output = ['status' => true, 'html' => $html, "message" => $response];
        return $this->setResponseFormat('json')->respond($output);
    }
}