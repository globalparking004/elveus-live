<?php
namespace App\Controllers;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;

class Reports extends BaseController
{   
    use ResponseTrait;
    public function __construct() 
    {    
    }

    public function index()
    {   
        $result=[];
        $data=[
            "page_title"=>"Users",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('users'),"title"=>"Users","status"=>"","link"=>false]]
        ];       
        return view('reports/index',$data);        
    }  

    public function exports()
    {   
        $result=[];
        $data=[
            "page_title"=>"Exports",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('exports'),"title"=>"Exports","status"=>"","link"=>false]]
        ];       
        return view('reports/exports',$data);        
    }  
    // Booking capacity
    public function bookings_capacity()
    {

        // echo "umair";
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

        // echo'<pre>';print_r($report_result);die;

        return view('reports/booking_capacity', $data);
    }

    public function bookings_capacity_report()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        // $code = $_GET['code'];
        $operator = $_GET['operator'];
        $product = $_GET['product'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $TimeFrom = $_GET['TimeFrom'] ? $_GET['TimeFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        // $TimeTo = $_GET['TimeTo'] ? $_GET['TimeTo'] : '';

        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $TimeFrom = date('H:i:s', strtotime($TimeFrom));
        // $TimeTo = date('H:i:s', strtotime($TimeTo));

        // print_r($TimeFrom);die;

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

        if (trim($product) != "") {
            $SQLproduct = " AND   `product_id`= '$product' ";
            $product_code = " AND   `id`= '$product' ";
        }

        $SQLoperator = "";
        $operator_id = "";

        if (trim($operator) != "") {
            $SQLoperator = " AND operator_id='$operator' ";
            $operator_id = " AND operator_id='$operator' ";
        }

        $date1 = new \DateTime($DateFrom);
        $date2 = new \DateTime($DateTo);
        $interval = $date1->diff($date2);
        $number_of_days = $interval->format('%a') + 1;
        $data = array();
        $labels = array();
        $bcount = array();


        for ($i = 0; $i < $number_of_days; $i++) {

            $SQLFilterDate = "";

            // $SQLFilterDate = "(valid_from>='$DateFrom' AND VALID_TO<='$DateTo')";
            // if ($filter_date == "valid_from") {
            $SQLFilterDate = "(`depart_at`<= '$DateFrom $TimeFrom' AND return_at>'$DateTo $TimeFrom')  and status='1' ";
            // $SQLFilterDate = "(`depart_at`<= '$DateFrom 00:00:00' AND return_at>'$DateFrom 00:00:00')  and status='1' ";
            // $SQLFilterDate = "'$DateFrom' AND '$DateTo'";
            // SELECT count(*) as bookingCount FROM `tbl_booking` WHERE depart_at <="2024-05-03 01:00:00" AND return_at >"2024-05-13 01:00:00" AND operator_id=5 AND status =1

            // } else if ($filter_date == "valid_to") {
            //     $SQLFilterDate = "date(valid_to) BETWEEN '$DateFrom' AND '$DateTo'";
            // }

            $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE $SQLFilterDate  $SQLoperator $SQLproduct ";
            $sql_data = "SELECT count(*) as bookingCount FROM `tbl_booking` WHERE  $SQLFilterDate $SQLoperator $SQLproduct  ";
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
            // foreach ($result as $value) {

                $sql_data_all_capacity = "SELECT sum(capacity) as all_capacity FROM `tbl_products`    WHERE  1=1  $product_code $operator_id  ";

                $result_all_capacity = $this->db->query($sql_data_all_capacity)->getRow();

                $all_capacity = $result_all_capacity->all_capacity;

                if($all_capacity==0 or empty($all_capacity))
                {
                    $percentage=0;
                }else{
                    $percentage = ($result->bookingCount /$all_capacity)*100;

                }
                $row = array();

                $row[] = date("m/d/Y", strtotime($DateFrom));
                $labels[] = date("m/d/Y", strtotime($DateFrom));
                // $row[] = date("m/d/Y", strtotime($DateTo));
                $bcount[] = $result->bookingCount;
                if ($percentage >=80) 
                { 
                    $badge = "badge badge-glow bg-warning"; $row[] = "<span class='$badge'>" . $result->bookingCount . "</span>"; 
                } else { 
                    $badge = "badge badge-glow bg-success"; $row[] = "<span class='$badge'>" . $result->bookingCount . "</span>"; 
                }

                
                $row[] = $all_capacity;
                $data[] = $row;
            //}

            $DateFrom = new \DateTime($DateFrom);

            $DateFrom=$DateFrom->modify('+1 day');
            $DateFrom = $DateFrom->format('Y-m-d');

        } // end of iteration
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data,
            'labels' => $labels,
            'bcount' => $bcount
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // All bookings
    public function all_booking()
    {
        $result=[];
        $data=[
            "page_title"=>"All Bookings",
            'roles'=>$result, 
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports'),"title"=>"Reports","status"=>"active","link"=>true],
                ["href"=>base_url('reports/all_bookings'),"title"=>"All Bookings","status"=>"","link"=>false]]
        ];
        $data['tot_depart'] = $this->get_totals('depart_at');
        // $data['tot_return'] = $this->get_totals('return_at');
        $data['totals'] = $this->get_totals('total');
        $data['tot_amount'] = $this->get_totals('price');
        // print_r($data['tot_return']);die;
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;
        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['operators'] = $agent_result;

        return view('reports/all_bookings',$data);  
    }

    public function get_bookings()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $status = $_GET['status'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', strtotime($DateTo));

        $filter_date = $_GET['filter_date'];
        $website_type= $_GET['website_type'];
        $website    = $_GET['website'];
        $agent      = $_GET['agent'];
        $airport    = $_GET['airport'];
        $operator   = $_GET['operator'];
        $role_id    = $_GET['role_id'];

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

        $SQLwebsiteType = "";
        if (trim($website_type) != "") {
            if(trim($website_type) == "GL"){
                $SQLwebsiteType = " AND reference Like '$website_type%' ";
            }else if (trim($website_type) == "2") {
                $SQLwebsiteType = " AND reference NOT Like 'GL%' ";
            }
        }

        $SQLwebsite = "";
        if (trim($website) != "") {
            $SQLwebsite = " AND source='$website' ";
        }

        $SQLagent = "";
        if (trim($agent) != "") {
            $SQLagent = " AND source='$agent' ";
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            $SQLstatus = " AND status='$status' ";
        }

        $SQLairport = "";
        if (trim($airport) != "") {
            $SQLairport = " AND airport='$airport' ";
        }

        $SQLoperator = "";
        if (trim($operator) != "") {
            $SQLoperator = " AND operator_id='$operator' ";
        }

        $SQLFilterDate = "";
        if ($SQLairport != '' || $SQLoperator != '' || $SQLwebsite != '' || $SQLstatus != '') 
        {
            if ($filter_date == "booking_at") {
                $SQLFilterDate = "and DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "departure_at") {
                $SQLFilterDate = "and DATE(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "return_at") {
                $SQLFilterDate = "and DATE(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            }

        } else {
            if ($filter_date == "booking_at") {
                $SQLFilterDate = "and DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "departure_at") {
                $SQLFilterDate = "and DATE(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
            } else if ($filter_date == "return_at") {
                $SQLFilterDate = "and DATE(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
            }
        }

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLoperator $SQLwebsite $SQLwebsiteType $SQLagent ";
        $sql_data = "SELECT * FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLoperator $SQLwebsite $SQLwebsiteType $SQLagent ";

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
        $this->AirportType = get_website_type($airport);

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
        $amount=0;
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            $result2 = $this->db->query($sql_data2)->getRow();
            $operator_name = "";
            if ($result2) {
                $operator_name = $result2->description;
            }

            /////////////////////////
            $sql_data2 = "SELECT product_code,parent, name FROM `tbl_products` where id='$value->product_id' LIMIT 1";
            $result2 = $this->db->query($sql_data2)->getRow();
            $product_code = "";
            $product_name = "";
            $product_airport = "";
            if ($result2) {
                $product_code = $result2->product_code;
                $product_name = $result2->name;
                $product_airport = $result2->parent;
            }

            if (strval($role_id) > 1) {

                // if ($value->booking_type == "Cash") {
                //     $badge = "badge badge-glow bg-success";
                //     $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                // } else {
                //     $badge = "badge badge-glow bg-warning";
                //     $row[] = "<span class='$badge'>" . $value->booking_type . "</span>";
                // }

                $row[] = $product_code . "\n" . $product_name;
                $row[] = $value->reference;
                $row[] = $value->firstName . " " . $value->surname;
                // $row[] = $value->contactNumber;
                $row[] = date("d-M-Y", strtotime($value->depart_at))."\n".date("H:i:s", strtotime($value->depart_at));
                // $row[] = date("H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y", strtotime($value->return_at))."\n".date("H:i:s", strtotime($value->return_at));
                // $row[] = date("H:i:s", strtotime($value->return_at));

                // $row[] = $value->carMake . "\n" . $value->carModel . "\n" . $value->carColour. "\n" . $value->carReg;
                // $row[] = $value->carReg;
                // $row[] = $value->passenger;

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

                // $row[] = $value->passenger;
                // $row[] = $value->show_status;

                $id = id_en($value->id);
                if ($value->show_status == 1) {
                    $show_action = "<div class=\"btn-group\">
                <a class=\"dropdown-item btn btn-outline-primary btn-sm waves-effect\" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\"> Show</a>
                  </div>";
                    $row[] = $show_action;
                } else {
                    $show_action = "<div class=\"btn-group\">
                <a class=\"dropdown-item btn btn-outline-primary btn-sm waves-effect\" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\"> No Show</a>
                  </div>";
                    $row[] = $show_action;
                }



                $id = id_en($value->id);
                $action = "<div class=\"btn-group\">
                    <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Actions
                    </a>
                    <div class=\"dropdown-menu\">
                      <a class=\"dropdown-item\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> View Booking</a>
                      <a class=\"dropdown-item\" onclick=\"print_card(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> Print Card</a>
                    </div>
                  </div>";
                $row[] = $action;
                $amount += $value->price;
            } else {
                $row[] = $value->reference;
                $row[] = $value->source;
                $row[] = $product_airport . "\n" . $product_name;
                // $row[] = $value->airport . "\n" . $product_name;
                $row[] = $value->firstName . " " . $value->surname;
                $row[] = date("d-M-Y H:i:s", strtotime($value->booked_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
                $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
                $code = substr($value->airport, 0, 3);
                // $row[] = get_currency($code,$value->price);
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
                // $row[] = $value->promocode;
                $row[] = $operator_name;
                // $row[] = $value->passenger;
                // $row[] = $value->show_status;

                $id = id_en($value->id);
                // if ($value->show_status == 1 ) {
                //     $show_action = "<div class=\"btn-group\">
                //     <a class=\"dropdown-item btn btn-outline-primary btn-sm waves-effect\" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_status(`$id`, 1);\" href=\"javascript:void(0);\"> Show</a>
                //       </div>";
                //     $row[] = $show_action;
                //     } else {
                //     $show_action = "<div class=\"btn-group\">
                //     <a class=\"dropdown-item btn btn-outline-primary btn-sm waves-effect\" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_status(`$id`, 0);\" href=\"javascript:void(0);\"> No Show</a>
                //       </div>";
                //     $row[] = $show_action;
                //     }


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
                    <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Actions
                    </a>
                    <div class=\"dropdown-menu\">                      
                      $other_actions
                    </div>
                  </div>";
                } else {
                    $action = "<div class=\"btn-group\">
                    <a class=\"dropdown-item btn btn-outline-primary btn-sm waves-effect\" style=\"border-top-right-radius: 5px; border-bottom-right-radius: 5px;\" onclick=\"show_booking_modal('view_booking',`$id`);\" href=\"javascript:void(0);\">
                    view Booking
                    </a>
                    <div class=\"dropdown-menu\">                      
                      $other_actions
                    </div>
                  </div>";
                }

                $row[] = $action;
                $amount += $value->price;
            }
            

            $data[] = $row;
        }
        $arr = array($total_count->total, $amount);
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data,
            'chartdata' =>$arr
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Performance
    public function performance()
    {   
        $result=[];
        $data=[
            "page_title"=>"Performance",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/performance'),"title"=>"Performance","status"=>"","link"=>false]]
        ];   
        // $sql2 = "UPDATE `tbl_booking` SET source='FreeToMove - Dashboard' WHERE source = 'Free2Move - Dashboard' ";
        // $result = $this->db->query($sql2);

    
        return view('reports/performance',$data);        
    } 

    public function get_performance()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $TimeFrom = $_GET['TimeFrom'] ? $_GET['TimeFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $TimeTo = $_GET['TimeTo'] ? $_GET['TimeTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'airport',
        ];

        $SQLstatus = " AND status='1'"; //AND (traffic_source IS NULL OR traffic_source ='' )

        $SQLFilterDate = "";
        $SQLWebsiteCondition = " AND source IS NOT NULL AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source !='CPD' AND source!='CTAP' AND source !='P4U' AND source!='Holiday Extras' AND source!='ParkVia' AND source!='Park&Fly' AND source!='FreeToMove' AND source!='Airport Parking With Us' AND source!='JBF' AND source!= 'Cash Booking' AND source!='YTE' AND source!='HCP' AND source!='CYP' AND source !='https://longtermparking.ie/' AND source !='Go Comparison' AND source!='goairportparking.com'";

        $SQLFilterDate = " AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = " AND DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = " AND (
        //     DATE(booked_at) BETWEEN '$DateFrom' ANDn '$DateTo' OR
        //     DATE(depart_at) BETWEEN '$DateFrom' AND '$DateTo' OR
        //     DATE(return_at) BETWEEN '$DateFrom' AND '$DateTo'
        //   ) ";
        
        
        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus";
        $sql_data = "SELECT airport, source, COUNT(*) as bookings, SUM(price) as price  FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus ";

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'airport') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $GroupBy = " GROUP BY source";

        $sql_count = $sql_count . $condition. $GroupBy;
        $sql_data = $sql_data . $condition;
        // exit($sql_count);

        // return json_encode($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY id desc";
        // $GroupBy = " GROUP BY source";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $GroupBy. $Limit;
        // exit($sql_data);
        // $sql_data .= $OrderBy . $GroupBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        // pre($sql_data);

        $data = array();
        $labels = array();
        $chartVal= array();
        $bgColors = array();
        $bookings=0;
        $amount=0;
        foreach ($result as $value) {
            $row = array();
            $source = "";
            if($value->source){
                $source = "<br>(".$value->source.")";
            }
            $row[] = $value->airport . $source;
            // $row[] = $value->airport . "\n" . $product_name;
            $code = substr($value->airport, 0, 3);
            // $row[] = get_currency($code,$value->price);
            $row[] = $value->bookings;
            $row[] = '';
            $row[] = $value->price;

            $amount += $value->price;
            $bookings += $value->bookings;

            $data[] = $row;
            // chart data
            $labels[] = $value->source;
            $chartVal[] = $value->price;
            $bgColors[] = generateRandomColor();
        }
        
        $tfooter = array("Total",$bookings,"",round($amount,2));
        if($data):
            array_push($data, $tfooter);
        endif;

        // $arr = array($bookings, $amount);
        $total =0;
        if($total_count):
            $total = $total_count->total;
        endif;
        $output = [ 
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
            'chartlabels' =>$labels,
            'chartdata' =>$chartVal,
            'bgColors' =>$bgColors
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Affiliate performance
    public function aff_performance()
    {   
        $result=[];
        $data=[
            "page_title"=>"Affiliate Performance",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/aff-performance'),"title"=>"Affiliate Performance","status"=>"","link"=>false]]
        ];   
        // $sql2 = "UPDATE `tbl_booking` SET source='FreeToMove - Dashboard' WHERE source = 'Free2Move - Dashboard' ";
        // $result = $this->db->query($sql2);

    
        return view('reports/performance_aff',$data);        
    }

    public function get_aff_performance()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $TimeFrom = $_GET['TimeFrom'] ? $_GET['TimeFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $TimeTo = $_GET['TimeTo'] ? $_GET['TimeTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'airport',
        ];

        $SQLstatus = " AND status='1' AND traffic_source IS NOT NULL AND traffic_source !=''"; 

        $SQLFilterDate = "";
        $SQLWebsiteCondition = " AND source IS NOT NULL AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source !='CPD' AND source!='CTAP' AND source !='P4U' AND source!='Holiday Extras' AND source!='ParkVia' AND source!='Park&Fly' AND source!='FreeToMove' AND source!='Airport Parking With Us' AND source!='JBF' AND source!= 'Cash Booking' AND source!='YTE' AND source!='HCP' AND source!='CYP' AND source !='https://longtermparking.ie/' AND source !='Go Comparison' AND source!='goairportparking.com'";

        $SQLFilterDate = " AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        
        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus";
        $sql_data = "SELECT airport, source, traffic_source, COUNT(*) as bookings, SUM(price) as price  FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus ";

        // echo"$sql_data";

        // exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'airport') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $GroupBy = " GROUP BY traffic_source, source";

        $sql_count = $sql_count . $condition. $GroupBy;
        $sql_data = $sql_data . $condition;
        // exit($sql_count);

        // return json_encode($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY id desc";
        // $GroupBy = " GROUP BY source";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $GroupBy. $Limit;
        // exit($sql_data);
        // $sql_data .= $OrderBy . $GroupBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        // pre($sql_data);

        $data = array();
        $labels = array();
        $chartVal= array();
        $bgColors = array();
        $bookings=0;
        $amount=0;
        $bookingFee=0;
        foreach ($result as $value) {
            $row = array();
            $source = "";
            if($value->source){
                $source = "<br>(".$value->source.")";
            }
            $vat = ($value->price/100)*20;
            $booking_fee = round($value->price-($value->bookings*1.95),2);

            $row[] = $value->airport;
            // $row[] = $value->airport . "\n" . $product_name;
            $code = substr($value->airport, 0, 3);
            $row[] = $value->traffic_source;
            $row[] = $value->bookings;
            $row[] = $value->price;
            $row[] = $booking_fee;

            $amount += $value->price;
            $bookings += $value->bookings;
            $bookingFee += round($booking_fee,2);

            $data[] = $row;
            // chart data
            $labels[] = $value->source;
            $chartVal[] = $value->price;
            $bgColors[] = generateRandomColor();
        }
        
        $tfooter = array("Total","",$bookings,round($amount,2), $bookingFee);
        if($data):
            array_push($data, $tfooter);
        endif;

        // $arr = array($bookings, $amount);
        $total =0;
        if($total_count):
            $total = $total_count->total;
        endif;
        $output = [ 
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
            'chartlabels' =>$labels,
            'chartdata' =>$chartVal,
            'bgColors' =>$bgColors
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    
    public function get_aff_webperformance()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $TimeFrom = $_GET['TimeFrom'] ? $_GET['TimeFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $TimeTo = $_GET['TimeTo'] ? $_GET['TimeTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'airport',
        ];

        $SQLstatus = " AND status='1' AND (traffic_source IS NULL OR traffic_source ='' )";

        $SQLFilterDate = "";
        $SQLWebsiteCondition = " AND source IS NOT NULL AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source !='CPD' AND source!='CTAP' AND source !='P4U' AND source!='Holiday Extras' AND source!='ParkVia' AND source!='Park&Fly' AND source!='FreeToMove' AND source!='Airport Parking With Us' AND source!='JBF' AND source!= 'Cash Booking' AND source!='YTE' AND source!='HCP' AND source!='CYP' AND source !='https://longtermparking.ie/' AND source !='Go Comparison' AND source!='goairportparking.com'";

        $SQLFilterDate = " AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = " AND DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = " AND (
        //     DATE(booked_at) BETWEEN '$DateFrom' ANDn '$DateTo' OR
        //     DATE(depart_at) BETWEEN '$DateFrom' AND '$DateTo' OR
        //     DATE(return_at) BETWEEN '$DateFrom' AND '$DateTo'
        //   ) ";
        
        
        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus";
        $sql_data = "SELECT airport, source, COUNT(*) as bookings, SUM(price) as price  FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus ";

        // echo"$sql_data";

        // exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'airport') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $GroupBy = " GROUP BY source";

        $sql_count = $sql_count . $condition. $GroupBy;
        $sql_data = $sql_data . $condition;
        // exit($sql_count);

        // return json_encode($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY id desc";
        // $GroupBy = " GROUP BY source";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $GroupBy. $Limit;
        // exit($sql_data);
        // $sql_data .= $OrderBy . $GroupBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        // pre($sql_data);

        $data = array();
        $labels = array();
        $chartVal= array();
        $bgColors = array();
        $bookings=0;
        $amount=0;
        $bookingFee=0;
        foreach ($result as $value) {
            $row = array();
            $source = "";
            if($value->source){
                $source = "<br>(".$value->source.")";
            }
            $booking_fee = round($value->price-($value->bookings*1.95),2);

            $row[] = $value->airport . $source;
            // $row[] = $value->airport . "\n" . $product_name;
            $code = substr($value->airport, 0, 3);
            // $row[] = get_currency($code,$value->price);
            $row[] = $value->bookings;
            $row[] = $value->price;
            $row[] = $booking_fee;

            $amount += $value->price;
            $bookings += $value->bookings;
            $bookingFee += round($booking_fee,2);

            $data[] = $row;
            // chart data
            $labels[] = $value->source;
            $chartVal[] = $value->price;
            $bgColors[] = generateRandomColor();
        }
        
        $tfooter = array("Total",$bookings,round($amount,2), $bookingFee);
        if($data):
            array_push($data, $tfooter);
        endif;

        // $arr = array($bookings, $amount);
        $total =0;
        if($total_count):
            $total = $total_count->total;
        endif;
        $output = [ 
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
            'chartlabels' =>$labels,
            'chartdata' =>$chartVal,
            'bgColors' =>$bgColors
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Supplier performance
    public function get_performance_supplier()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $Airport = $_GET['Airport'] ? $_GET['Airport'] : '';

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $TimeFrom = $_GET['TimeFrom'] ? $_GET['TimeFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $TimeTo = $_GET['TimeTo'] ? $_GET['TimeTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'airport',
        ];

        $SQLstatus = " AND status='1' ";

        $SQLFilterDate = "";
        $SQLWebsiteCondition = " AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP' OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='goairportparking.com') ";
        // $SQLWebsiteCondition = " AND source IS NULL OR source ='Dashboard' OR source ='CPD' OR source ='P4U' ";

        $SQLFilterDate = " AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = " AND DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
            
        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus";
        $sql_data = "SELECT airport, source, COUNT(id) as bookings, SUM(price) as price  FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLWebsiteCondition $SQLstatus ";

        // echo"$sql_data";

        // exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'airport') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $GroupBy = " GROUP BY source";
        $OrderBy = " ORDER BY source ASC";
        if ($Airport) {
            $GroupBy = " GROUP BY source,airport ";
            $OrderBy = " ORDER BY airport ASC";
        }
        

        $sql_count = $sql_count . $condition. $GroupBy;
        $sql_data = $sql_data . $condition;

        $total_count = $this->db->query($sql_count)->getRow();
        
        // $OrderBy = " ORDER BY airport ASC";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $GroupBy. $OrderBy. $Limit;
        // exit($sql_data);
        $result = $this->db->query($sql_data)->getResult();
        // pre($sql_data);

        $data = array();
        $labels = array();
        $chartVal= array();
        $bgColors = array();
        $bookings=0;
        $amount=0;
        // print_r($result);echo'<br>New array';

        $result2= $result;
        if (empty($Airport)) {
            $result2 = $this->mergeAndSumByName($result,'ParkVia');
            $result2 = $this->mergeAndSumByName($result,'CPD');
            $result2 = $this->mergeAndSumByName($result,'P4U');
            $result2 = $this->mergeAndSumByName($result,'FreeToMove');
            $result2 = $this->mergeAndSumByName($result,'Holiday Extras');       
            $result2 = $this->mergeAndSumByName($result,'Park&Fly');
            $result2 = $this->mergeAndSumByName($result,'YTE');
            $result2 = $this->mergeAndSumByName($result,'HCP');
            $result2 = $this->mergeAndSumByName($result,'CYP');
            $result2 = $this->mergeAndSumByName($result,'https://longtermparking.ie/');
            $result2 = $this->mergeAndSumByName($result,'Go Comparison');
            $result2 = $this->mergeAndSumByName($result,'goairportparking.com');
        }
        // pre($result2);
        foreach ($result2 as $value) 
        {

            $row = array();
            $source = urlencode($value->source);
            $source = "<a onclick=\"view_airport('`$source`','`$DateFrom`','`$DateTo`');\" href=\"javascript:void(0);\">".$value->source."</a>";
            
            $airport='';
            if ($Airport) {
                $source =  $value->source;
                $airport = $value->airport;
            }
            $row[] =  $source;
            ($Airport) ? $row[] = $value->airport: '';

            $code = substr($value->airport, 0, 3);
            $row[] = $value->bookings;
            $row[] = number_format($value->price, 2);

            $amount += $value->price;
            $bookings += $value->bookings;

            $data[] = $row;
        }
        $tfooter = array("Total",$bookings,round($amount,2));
        if ($Airport) {
            $tfooter = array("Total",'',$bookings,round($amount,2));
        }
        if($data):
            array_push($data, $tfooter);
        endif;

        $total =0;
        if($total_count):
            $total = $total_count->total;
        endif;
        $output = [ 
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Refunds
    public function refunds()
    {   
        $result=[];
        $data=[
            "page_title"=>"Refunds",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/refunds'),"title"=>"Refunds","status"=>"","link"=>false]]
        ];   
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

    
        return view('reports/refunds',$data);        
    } 

    public function get_refunds()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', strtotime($DateTo));

        $website_type= $_GET['website_type'];
        $website    = $_GET['website'];
        $airport    = $_GET['airport'];
        $role_id    = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'reference',
            2 => 'surname',
            3 => 'booked_at',
            4 => 'depart_at'
        ];

        $SQLwebsiteType = "";
        if (trim($website_type) != "") {
            if(trim($website_type) == "GL"){
                $SQLwebsiteType = " AND reference Like '$website_type%' ";
            }else if (trim($website_type) == "2") {
                $SQLwebsiteType = " AND reference NOT Like 'GL%' ";
            }
        }

        $SQLwebsite = "";
        if (trim($website) != "") {
            $SQLwebsite = " AND source='$website' ";
        }

        $SQLstatus = " AND status='4' ";

        $SQLairport = "";
        if (trim($airport) != "") {
            $SQLairport = " AND airport='$airport' ";
        }


        $SQLFilterDate = "";
      
        // if ($filter_date == "booking_at") {
            $SQLFilterDate = "and DATE(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "departure_at") {
        //     $SQLFilterDate = "and DATE(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "return_at") {
        //     $SQLFilterDate = "and DATE(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // }
        

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite $SQLwebsiteType ";
        $sql_data = "SELECT * FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLwebsite $SQLwebsiteType ";

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
        $this->AirportType = get_website_type($airport);

        $total_count = $this->db->query($sql_count)->getRow();
       
        $OrderBy = " ORDER BY id desc";
        $SortBy = "";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        $amount=0;
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));

            /////////////////////////
            $sql_data2 = "SELECT product_code,parent, name FROM `tbl_products` where id='$value->product_id' LIMIT 1";
            $result2 = $this->db->query($sql_data2)->getRow();
            $product_code = "";
            $product_name = "";
            $product_airport = "";
            if ($result2) {
                $product_code = $result2->product_code;
                $product_name = $result2->name;
                $product_airport = $result2->parent;
            }

            $row[] = $value->reference;
            $row[] = $value->source;
            $row[] = $product_airport . "\n" . $product_name;
            // $row[] = $value->airport . "\n" . $product_name;
            $row[] = $value->firstName . " " . $value->surname;
            $row[] = date("d-M-Y H:i:s", strtotime($value->booked_at));
            $row[] = date("d-M-Y H:i:s", strtotime($value->depart_at));
            $row[] = date("d-M-Y H:i:s", strtotime($value->return_at));
            $row[] = $value->price;
            $row[] = $value->refund_amount;

            $id = id_en($value->id);
        
            $amount += $value->price;

            $data[] = $row;
        }
        $arr = array($total_count->total, $amount);
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data,
            'chartdata' =>$arr
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Passenger report
    public function passenger()
    {   
        $result=[];
        $data=[
            "page_title"=>"Passenger",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/passenger'),"title"=>"Passenger","status"=>"","link"=>false]]
        ];   
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select id,description from tbl_operators";
        $data['operators'] = $this->db->query($sql_data)->getResult();
    
        return view('reports/driver/passenger',$data);        
    } 

    public function get_passenger()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $operator_id = $_GET['operator_id'] ? $_GET['operator_id'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);

        $SQLoperator='';
        if (!empty($operator_id) && $operator_id !='*') {
            $SQLoperator =" AND operator_id='$operator_id' ";
        }

        $SQLairport='';
        if (!empty($airport) && $airport !='*') {
            $SQLairport =" AND airport='$airport' ";
        }

        $data = array();
  
        // pre($sql_data);
        $result = generateTimeIntervals($DateFrom);
   
        foreach ($result as $key => $interval) 
        {  
            $time = date("H:i:s", strtotime($interval));
            $toDate = date('Y-m-d H:i:s', strtotime($interval.' +30 minutes'));

            $sql_queryD="SELECT SUM(passenger) as totPassenger FROM tbl_booking WHERE depart_at BETWEEN '$interval' AND '$toDate' AND status='1' $SQLairport $SQLoperator";
            $resD = $this->db->query($sql_queryD)->getRow();

            $sql_queryR="SELECT SUM(passenger) as totPassenger FROM tbl_booking WHERE return_at BETWEEN '$interval' AND '$toDate'  AND status='1' $SQLairport $SQLoperator";
            $resR = $this->db->query($sql_queryR)->getRow();

            $sql_queryDE = "SELECT COUNT(*) as totPassenger FROM tbl_booking WHERE depart_at BETWEEN '$interval' AND '$toDate' AND status='1' AND passenger IS NULL $SQLairport $SQLoperator";
            $resDE = $this->db->query($sql_queryDE)->getRow();

            $sql_queryRE = "SELECT COUNT(*) as totPassenger FROM tbl_booking WHERE return_at BETWEEN '$interval' AND '$toDate' AND status='1' AND passenger IS NULL $SQLairport $SQLoperator";
            $resRE = $this->db->query($sql_queryRE)->getRow();

            $totD_passenger = $resD->totPassenger+($resDE->totPassenger*3);
            $totR_passenger = $resR->totPassenger+($resRE->totPassenger*3);

            $row = array();
            $row[] =$time;
            $row[] = "<a onclick=\"view_bookings('`depart_at`','`$airport`','`$interval`');\" href=\"javascript:void(0);\">".$totD_passenger."</a>";
            $row[] = $resDE->totPassenger;
            $row[] = "<a onclick=\"view_bookings('`return_at`','`$airport`','`$interval`');\" href=\"javascript:void(0);\">".$totR_passenger."</a>";
            $row[] = $resRE->totPassenger;
            $data[] = $row;

        } // end of iteration
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function bookings_count()
    {   
        $result=[];
        $data=[
            "page_title"=>"Bookings",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/bookings_count'),"title"=>"Bookings","status"=>"","link"=>false]]
        ];   
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

    
        return view('reports/driver/bookings',$data);        
    } 

    public function get_bookings_count()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);

        $SQLairport='';
        if (!empty($airport) && $airport !='*') {
            $SQLairport =" AND airport='$airport' ";
        }

        $data = array();
  
        // pre($sql_data);
        $result = generateTimeIntervals($DateFrom);
   
        foreach ($result as $key => $interval) 
        {  
            $time = date("H:i:s", strtotime($interval));
            $toDate = date('Y-m-d H:i:s', strtotime($interval.' +30 minutes'));
            $sql_query="SELECT COUNT(*) as totBookings FROM tbl_booking WHERE booked_at BETWEEN '$interval' AND '$toDate' AND status='1' $SQLairport ";
            $res = $this->db->query($sql_query)->getRow();

            $sql_queryR="SELECT COUNT(*) as totBookings FROM tbl_booking WHERE return_at BETWEEN '$interval' AND '$toDate'  AND status='1' $SQLairport ";
            $resR = $this->db->query($sql_queryR)->getRow();

            $sql_queryD = "SELECT COUNT(*) as totBookings FROM tbl_booking WHERE depart_at BETWEEN '$interval' AND '$toDate' AND status='1' $SQLairport ";
            $resD = $this->db->query($sql_queryD)->getRow();
            // print_r($toDate);
            $row = array();
            $row[] =$time;
            $row[] = "<a onclick=\"view_bookings('`booked_at`','`$airport`','`$interval`');\" href=\"javascript:void(0);\">".$res->totBookings."</a>";
            $row[] = "<a onclick=\"view_bookings('`depart_at`','`$airport`','`$interval`');\" href=\"javascript:void(0);\">".$resD->totBookings."</a>";
            $row[] = "<a onclick=\"view_bookings('`return_at`','`$airport`','`$interval`');\" href=\"javascript:void(0);\">".$resR->totBookings."</a>";
            $data[] = $row;

        } // end of iteration
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // extra funtions
    function mergeAndSumByName(&$array, $searchKey) 
    {
        $mergedData = [];
        
        foreach ($array as $item) {
            // Check if the name starts with 'ParkVia'
            if (stripos($item->source, $searchKey) === 0) {
                if (!isset($mergedData[$searchKey])) {
                    // Initialize the merged entry
                    $mergedData[$searchKey] = (object) ['airport'=> $item->airport,'source' => $searchKey, 'bookings' => 0, 'price' => 0];
                }
                // Sum the values
                $mergedData[$searchKey]->bookings += $item->bookings;
                $mergedData[$searchKey]->price += $item->price;
            } else {
                // Keep other items unchanged
                $mergedData[] = $item;
            }
        }
        
        // Convert associative array to indexed array for final result
        $array = array_values($mergedData);
        return $array;
    }
    // Departure Return
    public function departure_return()
    {   
        $result=[];
        $data=[
            "page_title"=>"Departure Return",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reports/departure_return'),"title"=>"Departure Return","status"=>"","link"=>false]]
        ]; 

        $data['tot_depart'] = $this->get_totals('depart_at');
        // $data['tot_return'] = $this->get_totals('return_at');
        $data['totals'] = $this->get_totals('total');
        $data['tot_amount'] = $this->get_totals('price');
        // print_r($data['tot_return']);die;
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;
        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['operators'] = $agent_result;      

        $sql_query = "SELECT count(*)as totBookings,SUM(price)as totPrice FROM `tbl_booking`WHERE 1=1 AND DATE(booked_at)='2024-09-01' AND status='1' ";
        $res = $this->db->query($sql_query)->getRow();
        // pre($res);
        return view('reports/departure_return',$data);        
    } 

    public function get_departure_return()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $status = 1;

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        // $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        // $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', strtotime($DateTo));

        $booking_at = $_GET['booking_at']? $_GET['booking_at']:'';
        $departure_at = $_GET['departure_at'] ? $_GET['departure_at']: '';
        $return_at = $_GET['return_at'] ? $_GET['return_at']: '';
        $website_type= $_GET['website_type'];
        $website    = $_GET['website'];
        $agent      = $_GET['agent'];
        $airport    = $_GET['airport'];
        $operator   = $_GET['operator'];
        $role_id    = $_GET['role_id'];

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

        $SQLwebsiteType = "";
        if (trim($website_type) != "") {
            if (trim($website_type) == "GL") 
            {
                $SQLwebsiteType = " AND reference Like '$website_type%' ";
            }else if (trim($website_type) == "2"){
                $SQLwebsiteType = " AND reference NOT Like 'GL%' ";
            }
            
        }

        $SQLwebsite = "";
        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND source='$website' ";
        }

        $SQLagent = "";
        if (trim($agent) != "" && trim($agent) != "*") {
            $SQLagent = " AND source='$agent' ";
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            $SQLstatus = " AND status='$status' ";
        }

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND airport='$airport' ";
        }

        $SQLoperator = "";
        if (trim($operator) != "" && trim($operator) != "*") {
            $SQLoperator = " AND operator_id='$operator' ";
        }

        $SQLBookedDate = "";
        $SQLDepartureDate = "";
        $SQLReturnDate = "";

        $dates = getDaysList($DateFrom,$DateTo);

        $data = array();
        $totalBookings= 0;
        $totalBprice= 0;
        $totalDeparts= 0;
        $totalDprice= 0;
        $totalReturns= 0;
        $totalRprice= 0;
        // pre($dates);
        foreach ($dates as $key => $d) 
        {
            if ($booking_at) {
                $SQLBookedDate = " AND DATE(booked_at) = '$d' ";
            }
            if ($departure_at) {
                $SQLDepartureDate = " AND DATE(depart_at) = '$d' ";
            } 
            if ($return_at) {
                $SQLReturnDate = " AND DATE(return_at) = '$d' ";
            }

            $sql_data = "SELECT count(*) as totBookings, SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1  $SQLBookedDate $SQLstatus $SQLairport $SQLoperator $SQLwebsite $SQLwebsiteType $SQLagent ";
            $sql_dataD = "SELECT count(*) as totDeparts, SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1 $SQLDepartureDate $SQLstatus $SQLairport $SQLoperator $SQLwebsite $SQLwebsiteType $SQLagent ";
            $sql_dataR = "SELECT count(*) as totReturns,SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1 $SQLReturnDate $SQLstatus $SQLairport $SQLoperator $SQLwebsite $SQLwebsiteType $SQLagent ";
            // pre($sql_dataD);

            // if (!empty($search)) {
            //     foreach ($table_map as $key => $val) {
            //         if ($table_map[$key] == 'created_at') {
            //             $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
            //         } else {
            //             $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
            //         }
            //     }
            //     $condition .= " )";
            // }


            $sql_data = $sql_data;
            $sql_dataD = $sql_dataD;
            $sql_dataR = $sql_dataR;
            $this->AirportType = get_website_type($airport);

        
            $result = $this->db->query($sql_data)->getRow();
            $resultD = $this->db->query($sql_dataD)->getRow();
            $resultR = $this->db->query($sql_dataR)->getRow();
            // $result = $this->db->query($sql_data)->getResult();
            // print_r($sql_data);echo'Bookings<br>';

            $totBookings=($booking_at) ? $result->totBookings: 0;
            $totBprice=($booking_at) ? $result->totPrice: 0;
            $totDeparts=($departure_at) ? $resultD->totDeparts: 0;
            $totDprice=($departure_at) ? $resultD->totPrice: 0;
            $totReturns=($return_at) ? $resultR->totReturns:0;
            $totRprice=($return_at) ? $resultR->totPrice:0;

            if ($totBookings || $totDeparts || $totReturns) 
            {
                $totalBookings += $totBookings;
                $totalBprice += $totBprice;
                $totalDeparts += $totDeparts;
                $totalDprice += $totDprice;
                $totalReturns += $totReturns;
                $totalRprice += $totRprice;
                $row = array();
                $row[] = $d;
                $row[] = $totBookings;
                $row[] = $totBprice;
                $row[] = $totDeparts;
                $row[] = $totDprice;
                $row[] = $totReturns;
                $row[] = $totRprice;

                $data[] = $row;
            }
        }
        $newRow = array();
        $newRow[] = "Total";
        $newRow[] = $totalBookings;
        $newRow[] = round($totalBprice, 2);
        $newRow[] = $totalDeparts;
        $newRow[] = round($totalDprice, 2);
        $newRow[] = $totalReturns;
        $newRow[] = round($totalRprice, 2);
        array_push($data, $newRow);

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            // 'recordsTotal' => $total_count->total,
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            // 'recordsFiltered' => $total_count->total,
            'data' => $data,
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    
    public function get_airport_by_supplier()
    {
        $data = $this->request->getVar();
        $source = "";
        $DateFrom=$data['DateFrom'];
        $DateTo=$data['DateTo'];

        if (isset($data['source'])) {
            $source = urldecode($data['source']);
        }
        $sql = "SELECT airport,COUNT(*) as bookings, SUM(price)as price FROM tbl_booking WHERE (`source` LIKE CONCAT('$source', '%') OR `source`='$source')  AND (booked_at BETWEEN '$DateFrom' AND '$DateTo') AND status='1' GROUP BY airport ORDER BY airport";
        $booking = $this->db->query($sql)->getResult();
        // pre($sql);
        
        $modal = true;
        $response = "";
        $html = "";
        if($booking):
            foreach ($booking as $key => $r) {
                $html .= "<tr>
                    <td>$r->airport</td>                        
                    <td>$r->bookings</td>
                    <td>$r->price</td>
                    </tr>";
            }
        endif;
        
        $output = ['status' => true, 'html' => $html, "message" => $response, "modal" => $modal];
        return $this->setResponseFormat('json')->respond($output);
    }
    
    public function get_totals($column)
    {
        // $date = date('Y-m-d', strtotime('-1 month'));
        $date = date('Y-m-d');
        $sql_query = '';
        if ($column == 'price') {
            $sql_query="SELECT SUM(price) as total FROM tbl_booking WHERE status >1 ";
        }elseif ($column == 'depart_at') {
            $sql_query="SELECT count(*) as total FROM tbl_booking WHERE `depart_at` > $date AND status >1";
        }else{
            $sql_query="SELECT count(*) as total FROM tbl_booking  WHERE status >1";
        }
        return $this->db->query($sql_query)->getRow()->total;
    }

    public function get_websites()
    {
        $val = $_GET['val'] ? $_GET['val'] : '';
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $sql_data = "select * from tbl_websites";
        if ($val == 2) {
            $sql_data = "select * from tbl_websites WHERE `short_code` = 'SUP'";
        }
        if ($airport) {
            $sql_data = "select * from tbl_websites WHERE `short_code` = '$airport'";
        }
        
        $result = $this->db->query($sql_data)->getResult();
        echo'<option value="">All</option>';
        foreach ($result as $key => $r) {
            echo '<option value="'.$r->domain.'">'.$r->domain.'</option>';
        }
    }

    public function get_booking_by_time()
    {
        $data = $this->request->getVar();
        $type = $data['type'];
        $airport = $data['airport'];
        $DateFrom=$data['DateFrom'];
        $SQLairport = '';
        if ($airport != '*') {
            $SQLairport = "AND airport='$airport'";
        }
        $toDate = date('Y-m-d H:i:s', strtotime($DateFrom.' +30 minutes'));
        $SQLdate = "AND $type BETWEEN '$DateFrom' AND '$toDate'";
        $sql = "SELECT id, reference, airport, passenger, depart_at, return_at FROM tbl_booking WHERE 1=1 $SQLdate $SQLairport AND status='1'";
        $booking = $this->db->query($sql)->getResult();
        // pre($sql);
        
        $modal = true;
        $response = "";
        $html = "";
        if($booking):
            foreach ($booking as $key => $r) {
                $date = $r->depart_at;
                if ($type == 'return_at') {
                    $date = $r->return_at;
                }
                $html .= "<tr>
                    <td>$r->reference</td>                        
                    <td>$r->airport</td>                        
                    <td>$r->passenger</td>
                    </tr>";
            }
        endif;
        
        $output = ['status' => true, 'html' => $html, "message" => $response, "modal" => $modal];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function capacity_download()
    {
        $operator = $_GET['operator'];
        $product = $_GET['product'];
        $get_limiter_time=$_GET['time'];
        $DateFrom = $_GET['date'] ? $_GET['date'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);

        $SQLproduct = "";
        $get_limiter_time_val=$get_limiter_time;
        if (trim($product) != "") {
            $SQLproduct = " AND   `product_id`= '$product' ";
        }

        $SQLoperator = "";
        if (trim($operator) != "") {
            $SQLoperator = " AND operator_id='$operator' ";
        }

        $SQLFilterDate = "(`depart_at`<= '$DateFrom 23:59:00' AND return_at>'$DateFrom $get_limiter_time_val:00')  and status='1' ";

        $sql_data = "SELECT * FROM `tbl_booking` WHERE $SQLFilterDate $SQLoperator $SQLproduct  ";
        $OrderBy = " ORDER BY id DESC";
        $sql_data .= $OrderBy;
        $bookings = $this->db->query($sql_data)->getResult();
        // pre($bookings);
        $date = date('dmY').'-'.count($bookings);
        $filePath = WRITEPATH . 'bookingCapacity/';
        $fileName = $date.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV ,'Late Charges'
        fputcsv($file, ['Reference', 'Source', 'Airport','Depart At', 'Return At', 'CarReg' , 'Price', 'Status', 'Created At']);

        // Add rows to the CSV file
        foreach ($bookings as $booking) {
            $gcost='';
            if ($booking->refund_amount) {
                $gcost= $booking->google_cost;
            }
            $show_status = ($booking->show_status == 1)? 'show':'';
            fputcsv($file, [
                $booking->reference,
                $booking->source,
                $booking->airport,
                $booking->depart_at,
                $booking->return_at,
                $booking->carReg,
                $booking->price,
                $booking->status,
                $booking->created_at,
            ]);
        }

        fclose($file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }
}