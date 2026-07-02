<?php

namespace App\Controllers;
ini_set('memory_limit', '-1');

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use ValueError;

class promotion extends BaseController
{
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    protected $Airport;
    public function __construct()
    {
        $this->Users = new UsersModel;
        $this->Roles = new RolesModel;
        $airports = session()->get('AUTH');
        $this->Airport = "*";
        // if ($airports['airport']) {
        //     $this->Airport = trim($airports['airport']);
        // }
        if ($this->Airport == "") {
            $this->Airport = "*";
        }
    }

    public function index()
    { 
        $data = [
            "page_title" => "Promotion Codes",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('bookings'), "title" => "Promotion Codes", "status" => "active", "link" => true],
                ["href" => base_url('promotion/add'), "title" => "Add Promotion Codes", "status" => "", "link" => false]
            ]
        ];


        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select * from tbl_agents";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['agents'] = $agent_result;

        return view('promotion/view', $data);
    }

    public function view_promotions()
    {
        $data = [
            "page_title" => "Promo Codes",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('promotion/view'), "title" => "view promotion code", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select * from tbl_agents";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['agents'] = $agent_result;

        $sql_data = "select * from tbl_booking";
        $promo_result = $this->db->query($sql_data)->getResult();
        $data['promocode'] = $promo_result;

        return view('promotion/report', $data);
        // redirect("bookings/add");

        // return redirect()->to('bookings/add');
    }

    public function get_promotions()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $code = $_GET['code'];
        $agent = $_GET['agent'];
        $website = $_GET['website'];
        $promotional_name = $_GET['promotional_name'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'code',
            2 => 'agent',
            3 => 'promotional_name',
            4 => 'valid_from',
            5 => 'valid_to'
        ];

        $SQLcode = "";
        if (trim($code) != "") {
            $SQLcode = " AND code='$code' ";
        }

        $SQLagent = "";
        if (trim($agent) != "") {
            $SQLagent = " AND agent='$agent' ";
        }

        $SQLpromotional_name = "";
        if (trim($promotional_name) != "") {
            $SQLpromotional_name = " AND promotional_name='$promotional_name' ";
        }

        $SQLwebsite = "";
        if (trim($website) != "") {
            $SQLwebsite = " AND website='$website' ";
        }

        $SQLFilterDate = "";


        $SQLFilterDate = "date(created_at) BETWEEN '2023-01-01' AND '$DateTo'";
        // $SQLFilterDate = "date(created_at) BETWEEN '$DateFrom' AND '$DateTo'";
        
        // if ($filter_date == "valid_from") {
        // $SQLFilterDate = "(valid_from >= '$DateFrom' AND VALID_TO <= '$DateTo')";
        // $SQLFilterDate = "'$DateFrom' AND '$DateTo'";

        // } else if ($filter_date == "valid_to") {
        //     $SQLFilterDate = "date(valid_to) BETWEEN '$DateFrom' AND '$DateTo'";
        // }

        $sql_count = "SELECT count(*) as total FROM tbl_promotion_code WHERE $SQLFilterDate $SQLagent $SQLpromotional_name $SQLcode $SQLwebsite ";
        $sql_data = "SELECT * FROM `tbl_promotion_code`  WHERE  $SQLFilterDate $SQLagent $SQLpromotional_name $SQLcode $SQLwebsite ";
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

        //exit($sql_count);

        $sql_data = $sql_data . $condition;


        //exit($sql_data);

        // return json_encode($sql_data);

        // exit;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY id DESC"; //" ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();

        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            // $sql_data2 = "SELECT * FROM `tbl_operators` where id='$value->operator_id'";
            // $result2 = $this->db->query($sql_data2)->getRow();
            // $operator_name = "";
            // if ($result2) {
            //     $operator_name = $result2->description;
            // }

            // $sql_data2 = "SELECT product_code,name FROM `tbl_products` where id='$value->product_id' LIMIT 1";
            // $result2 = $this->db->query($sql_data2)->getRow();
            // $product_code = "";
            // if ($result2) {
            //     $product_code = $result2->product_code;
            // }

            // $sql_data2 = "SELECT * FROM `tbl_booking` where promocode='$value->promocode'";
            // $promo_result = $this->db->query($sql_data2)->getRow();
            // $promocode = $promo_result->promocode;
            // $promocode = "";
            // if (isset($promo_result)) {
            //     $promocode = $promo_result->promocode;
            // }

             $promocode = "";
            if (isset($value->code)) {
                $sql_data = "SELECT count(*) as count FROM `tbl_booking` where promocode='$value->code'";
                // $sql_data = "SELECT count(*) as count, created_at FROM `tbl_booking` where promocode='$value->code' AND created_at >='$value->created_at'";
                $promo_result = $this->db->query($sql_data)->getRow();
                $promocode = $promo_result->count;

            }
          

            $row[] = $value->code;

            $row[] = $value->promotional_name;
            $row[] = $value->agent;
            $row[] = $value->amount;
            $row[] = $value->website;
            $row[] = $promocode;




            $row[] = date("m/d/Y", strtotime($value->valid_from));
            $row[] = date("m/d/Y", strtotime($value->valid_to));

            // $action = "<div class=\"btn-group\">
            //     <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            //     Actions
            //     </a>
            //     <div class=\"dropdown-menu\">
            //       <a class=\"dropdown-item\" href=" . base_url("bookings/details?id=" . urlencode($id)) . "><i data-feather=\"edit\"></i> View</a>
            //       <a class=\"dropdown-item\" onclick=\"print_card(`$id`);\" href=\"javascript:void(0);\"><i data-feather='printer'></i> Print Card</a>
            //     </div>
            //   </div>";
            // $row[] = $action;
            // $row[] = $operator_name;
            // $row[] = $value->reference;
            // $row[] = $value->price;
            // $row[] = $value->surname;
            // $row[] = $value->airport;
            // $row[] = $value->carReg;
            // $row[] = date("m/d/Y H:i:s", strtotime($value->booked_at));
            // $row[] = date("m/d/Y H:i:s", strtotime($value->depart_at));
            // $row[] = date("m/d/Y H:i:s", strtotime($value->return_at));
            // if ($value->status == 1) {
            //     $badge = "badge badge-glow bg-success";
            //     $row[] = "<span class='$badge'>Completed</span>";
            // } elseif ($value->status == 0) {
            //     $badge = "badge badge-glow bg-warning";
            //     $row[] = "<span class='$badge'>Pending</span>";
            // } elseif ($value->status == 2) {
            //     $badge = "badge badge-glow bg-danger";
            //     $row[] = "<span class='$badge'>Cancelled</span>";
            // } elseif ($value->status == 3) {
            //     $badge = "badge badge-glow bg-info";
            //     $row[] = "<span class='$badge'>No Show</span>";
            // }
            // $row[] = $value->source;
            // $id = id_en($value->id);
            // $other_actions = '';
            // if ($value->status == 0) {
            //     //$other_actions="<a class=\"dropdown-item\" href=" . base_url("bookings/details?id=" . urlencode($id)) . "><i data-feather='check-circle'></i> Complete Booking</a>";

            //     $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('complete_booking',`$id`);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> Complete Booking</a>";

            // } else if ($value->status == 1) {
            //     $other_actions = "<a class=\"dropdown-item\" onclick=\"show_booking_modal('cancel_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='minus-circle'></i> Cancel Booking</a>";
            //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('make_refund',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='dollar-sign'></i> Make a Refund</a>";
            //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('move_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather='corner-up-left'></i> Move Booking</a>";
            //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('edit_booking',`$id`);\" href=\"javascript:void(0)\"><i data-feather=\"edit\"></i> Amend Booking</a>";
            //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('resend_email',`$id`);\" href=\"javascript:void(0)\"><i data-feather='send'></i> Resend Booking Confirmation</a>";
            //     $other_actions .= "<a class=\"dropdown-item\" onclick=\"show_booking_modal('booking_pdf',`$id`);\"  href=\"javascript:void(0)\"><i data-feather='file'></i> Booking PDF</a>";
            // }
            // $action = "<div class=\"btn-group\">
            //     <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            //     Actions
            //     </a>
            //   </div>";
            // $row[] = $action;            
            $action = "<div class=\"dropdown\">
            <button type=\"button\" class=\"btn p-0 dropdown-toggle hide-arrow\" data-bs-toggle=\"dropdown\">
                <i data-feather='more-vertical'></i>               
                </button>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"edit_data(`$value->id`);\"><i data-feather=\"edit\"></i> Edit</a>
                </div>
              </div>";
              
                  // <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$value->id`);\"><i data-feather=\"trash\"></i> Delete</a>
            $row[] = $action;
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

    public function promotion_save()
    {
        $code = isset($_POST['code']) ? $_POST['code'] : '';
        $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
        $promotion_name = isset($_POST['promotion_name']) ? $_POST['promotion_name'] : '';
        $valid_from = isset($_POST['valid_from']) ? $_POST['valid_from'] : '';
        $valid_to = isset($_POST['valid_to']) ? $_POST['valid_to'] : '';
        $website = isset($_POST['website']) ? $_POST['website'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $agent = isset($_POST['agent']) ? $_POST['agent'] : '';
        $agentid = isset($_POST['agentid']) ? $_POST['agentid'] : '';
        $valid_from = strtotime($valid_from);
        $valid_from = date('Y-m-d', $valid_from);
        $valid_to = strtotime($valid_to);
        $valid_to = date('Y-m-d', $valid_to);

        $validationRules = [
            'code' => 'required',
            'amount' => 'required',
            'promotion_name' => 'required',
            'type' => 'required'
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
            $sql_data = "INSERT INTO tbl_promotion_code 
                        (code, promotional_name, amount, valid_from, valid_to, agent, agent_id, website,type) 
                        VALUES 
                        ('$code', '$promotion_name', '$amount', '$valid_from', '$valid_to', '$agent', '$agentid', '$website','$type');";

            $result = $this->db->query($sql_data);

            // print_r($sql_data);
            // $id = $this->db->insertID();
            // send_email($email,"Your Parking Booking Confirmation",$id);
            if ($result) {
                echo json_encode(['code' => 1, 'msg' => "Data updated successfully"]);
            } else {

                echo json_encode(['code' => 0, 'error' => "not saved"]);
            }
        }
    }
  
    public function promotion_update()
    {
        $id = $_GET['id'];
        $sql_data = "select * from tbl_promotion_code where id='$id'";
        $result = $this->db->query($sql_data)->getRow();
        $valid_from=date("m/d/Y", strtotime($result->valid_from));
        $valid_to=date("m/d/Y", strtotime($result->valid_to));

        if ($result) {
            $result = ['status' => true, "data" => $result,'valid_from'=>"$valid_from",'valid_to'=>"$valid_to"];
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function promotion_update_values()
    {
        $CODE = $_POST['CODE'];
        $PROMOTIONNAME = $_POST['PROMOTIONNAME'];
        $AGENT = $_POST['AGENT'];
        $amount = $_POST['amount'];
        $valid_from = $_POST['valid_from'];
        $valid_to = $_POST['valid_to'];
        
        $website = $_POST['website'];

        $valid_from = strtotime($valid_from);
        $valid_from = date('Y-m-d', $valid_from);
        $valid_to = strtotime($valid_to);
        $valid_to = date('Y-m-d', $valid_to);


        $id = $_POST['id'];
        $sql = "UPDATE tbl_promotion_code 
                 SET code='$CODE', promotional_name='$PROMOTIONNAME', agent='$AGENT', amount='$amount', 
                  valid_from='$valid_from', valid_to='$valid_to',website='$website' WHERE id='$id'";

        $result = $this->db->query($sql);
        if ($result) {
            $result = ['status' => true, "data" => $result];
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function promotion_delete_record()
    {
        $id = $_GET['id'];
        $sql = "delete from tbl_promotion_code WHERE id='$id'";

        $result = $this->db->query($sql);
        if ($result) {
            $result = ['status' => true, "data" => $result];
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);

    }

    public function promotion_report()
    {

        $data = [
            "page_title" => "Promotion Report",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('promotion/report'), "title" => "Promotion Report", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;

        $sql_data = "select * from tbl_agents";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['agents'] = $agent_result;


        return view('promotion/view_report', $data);
    }

    public function get_promotion_report()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $code = $_GET['code'] ? $_GET['code'] : '';;
        $agent = $_GET['agent'] ? $_GET['agent'] : '';;
        $website = $_GET['website'] ? $_GET['website'] : '';;
        $status = $_GET['status'] ? $_GET['status'] : '';;
        // $promotional_name = $_GET['promotional_name'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $condition = "";
        $table_map = [
            0 => 'agent',
            // 1 => 'code_usages',
            // 2 => 'valid_from',
            // 3 => 'valid_to'
        ];

        $SQLcode = "";
        if (trim($code) != "") {
            $SQLcode = " AND pc.code='$code' ";
        }

        $SQLagent = "";
        if (trim($agent) != "" && trim($agent) != '*') {
            $SQLagent = " AND pc.agent='$agent' ";
        }

        // $SQLpromotional_name = "";
        // if (trim($promotional_name) != "") {
        //     $SQLpromotional_name = " AND pc.promotional_name='$promotional_name' ";
        // }

        $SQLwebsite = "";
        if (trim($website) != "") {
            $SQLwebsite = " AND pc.website='$website' ";
        }

        $SQLstatus = " AND bk.status ='1'";
        if (trim($status) != "" && trim($status) != '*') {
            $SQLstatus = " AND bk.status='$status' ";
        }

        
        $SQLFilterDate = "";


        $SQLFilterDate = "date(bk.booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate = "date(created_at) BETWEEN '$DateFrom' AND '$DateTo'";
        
        // if ($filter_date == "valid_from") {
        // $SQLFilterDate = "(valid_from >= '$DateFrom' AND VALID_TO <= '$DateTo')";
        // $SQLFilterDate = "'$DateFrom' AND '$DateTo'";

        // } else if ($filter_date == "valid_to") {
        //     $SQLFilterDate = "date(valid_to) BETWEEN '$DateFrom' AND '$DateTo'";
        // }

        $sql_count = "SELECT count(DISTINCT pc.id) as total,bk.booked_at FROM tbl_promotion_code pc LEFT JOIN tbl_booking bk ON bk.promocode=pc.code WHERE $SQLFilterDate $SQLagent $SQLcode $SQLwebsite $SQLstatus AND pc.agent IS NOT NULL ";
        $sql_data = "SELECT pc.*, count(DISTINCT bk.id) as code_usages,bk.booked_at FROM tbl_promotion_code pc LEFT JOIN tbl_booking bk  ON bk.promocode=pc.code  WHERE  $SQLFilterDate $SQLagent $SQLcode $SQLwebsite $SQLstatus AND pc.agent IS NOT NULL ";
        // exit($sql_data);


        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'agent') {
                    $condition .= " AND ( pc." . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR  pc." . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }

        $GroupBy = 'GROUP BY pc.agent';
        $sql_count = $sql_count. $condition;

        // exit($sql_count);

        $sql_data = $sql_data . $condition;


        // exit($sql_data);

        // return json_encode($sql_data);

        // exit;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY pc.agent ASC"; 
        $SortBy = "";
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');

        $sql_data .= $GroupBy. $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        // pre($result);
        foreach ($result as $value) {
            
                $row = array();
                // $promocode = "";
                // if (isset($value->code)) {
                //     $sql_data = "SELECT count(*) as count FROM `tbl_booking` where promocode='$value->code'";
                //     // $sql_data = "SELECT count(*) as count, created_at FROM `tbl_booking` where promocode='$value->code' AND created_at >='$value->created_at'";
                //     $promo_result = $this->db->query($sql_data)->getRow();
                //     $promocode = $promo_result->count;

                // }
              
                // $row[] = $value->code;
                // $row[] = $value->promotional_name;
                $row[] = $value->agent;
                // $row[] = $value->amount;
                // $row[] = $value->website;
                $row[] = $value->code_usages;

                $row[] = date("m/d/Y", strtotime($value->valid_from));
                $row[] = date("m/d/Y", strtotime($value->valid_to));
            // if ($value->code_usages >0) 
            // {
                $data[] = $row;
            // }
        }
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,//$total_count->total
            'recordsFiltered' => $total_count->total,//$total_count->total
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

}