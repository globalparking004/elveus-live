<?php
namespace App\Controllers;
use App\Models\InteliquentModel;
use App\Models\SupplierModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Account extends BaseController
{   
    use ResponseTrait;
    public function __construct() 
    {  
      $this->supplier = new SupplierModel;
    }

    // website
    public function index()
    {
        $data = [
            "page_title" => "Website Account Management",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('account'), "title" => "Website Account Management", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $data['suppliers'] = $this->supplier->findAll();

        return view('general/account', $data);
    }

    public function get_account() 
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
    
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $agent = $_GET['agent'] ? $_GET['agent'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $role_id = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'airport',
            1 => 'amount',
        ];
     
        $SQLagent = "";
        if (trim($agent) != "" && trim($agent) != "*") {
            $SQLagent = " AND source='$agent' ";
        }

        // $SQLstatus = " AND (status='1' OR status='4') ";
        $SQLstatus = " AND status=1";
        

        $SQLairport = "";
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND airport='" . $airport . "'";
        }
        $filter_date = 'booking_at';

        $SQLFilterDate = "";
        
        // if ($filter_date == "booking_at") {
            $SQLFilterDate = "and date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "departure_at") {
        //     $SQLFilterDate = "and date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "return_at") {
        //     $SQLFilterDate = "and date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // }
        $SQLref = " AND (reference IS NOT NULL AND reference !='') AND reference LIKE 'GL-%' ";

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLagent $SQLref ";
        $sql_data = "SELECT airport,count(*) as totalQty, SUM(price) as totalAmount FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLagent $SQLref ";

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


        $GroupBy = " GROUP BY airport";

        $sql_count = $sql_count . $condition . $GroupBy;

        $sql_data = $sql_data . $condition;

        // exit($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();

        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $GroupBy.$OrderBy . $SortBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
       
        $totalll_count = 0;
        $grand_totall = array();

        // pre($result);
        foreach ($result as $value) 
        {
            $row = array();
            $totalAmount = round($value->totalAmount - ($value->totalQty*1.95),2);
            $row[] = $value->airport;
            $row[] = $value->totalQty;
            $row[] = $totalAmount;

            // $id = id_en($value->id);
    
            $action = "<button class='gcostAdd btn btn-outline-success btn-sm' data-id='$value->airport' data-qty='$value->totalQty' data-amount='$totalAmount'>Add</button>";
            
            $row[] = $action;


            $data[] = $row;

            // $totalll_count=sizeof($data);

        }

        if (strval($role_id) > 1) {

            $totalll_count_result = sizeof($grand_totall);

        } else {

            $totalll_count_result = ($total_count)? $total_count->total: 0;

        }

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_account2() 
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
    
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $role_id = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'airport',
            1 => 'dateFrom',
            2 => 'dateTo',
            3 => 'qty',
            4 => 'Amount',
        ];

        $SQLairport = "";
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND airport='" . $airport . "'";
        }
        $SQLtype=" AND account_type=1";

        $sql_count = "SELECT count(*) as total FROM tbl_booking_account WHERE 1=1  $SQLairport $SQLtype";
        $sql_data = "SELECT * FROM `tbl_booking_account`  WHERE 1=1  $SQLairport $SQLtype";

        // echo"$sql_data";

        //exit($sql_data);


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

        $sql_count = $sql_count . $condition;

        $sql_data = $sql_data . $condition;

        // exit($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();

        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $OrderBy . $SortBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
       
        $totalll_count = 0;
        $grand_totall = array();

        // pre($result);
        foreach ($result as $value) 
        {
            $created_at = date("Y-m-d", strtotime($value->created_at));

            $row = array();
            $percentage = round(($value->google_cost/$value->amount)*100,2);
            $row[] = $created_at;
            $row[] = $value->airport;
            $row[] = $value->date_from; 
            $row[] = $value->date_to;
            $row[] = $value->qty;
            $row[] = $value->amount;
            $row[] = ($value->google_cost_type == 1)?$value->google_cost:'';
            $row[] = $percentage.'%';

            $images = explode(',',$value->gcost_image);
            $img='';
            foreach ($images as $key => $image) {
                if (!empty(trim($image))) { 
                    $img .= '<a href="' . base_url('screenshot/' . $image) . '" target="_blank"><img src="' . base_url('screenshot/' . $image) . '" class="mb-1" width="120"></a>';
                }
            }
            $row[]= $img;

            $id = id_en($value->id);
    
            // $action = "<button class='downlodBtn btn btn-outline-danger btn-sm' data-id='$value->airport' data-dfrom='$value->date_from' data-dto='$value->date_to'>Download</button>";
            $google_cost = ($value->google_cost_type ==2)? round(($value->google_cost*100)/$value->amount,2):$value->google_cost;
            $action = '<div class="btn-group">
                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                    </a>
                    <div class="dropdown-menu">          
                         <a href="'. base_url('account/download?airport='.$value->airport.'&dateFrom='.$value->date_from.'&dateTo='.$value->date_to.'&type=1') .'" class=" dropdown-item">
                            <i data-feather="download"></i> Download
                        </a>
                        <a class="editBtn dropdown-item" 
                           data-id="' . htmlspecialchars($id) . '" 
                           data-qty="' . htmlspecialchars($value->qty) . '" 
                           data-amount="' . htmlspecialchars($value->amount) . '" 
                           data-gtype="' . htmlspecialchars($value->google_cost_type) . '" 
                           data-gcost="' . htmlspecialchars($google_cost) . '" 
                           data-img="' . htmlspecialchars($value->gcost_image) . '">
                            <i data-feather="edit"></i> Edit
                        </a>
                        
                        <a class="deleteBtn dropdown-item" 
                           data-id="' . htmlspecialchars($id) . '">
                            <i data-feather="trash"></i> Delete
                        </a>
                    </div>
                  </div>';

            
            $row[] = $action;


            $data[] = $row;

        }

        if (strval($role_id) > 1) {

            $totalll_count_result = sizeof($grand_totall);

        } else {

            $totalll_count_result = ($total_count)? $total_count->total: 0;

        }

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Supplier
    public function account_supplier()
    {
        $data = [
            "page_title" => "Supplier Account Management",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('account'), "title" => "Supplier Account Management", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $data['suppliers'] = $this->supplier->findAll();

        return view('general/account_supplier', $data);
    }

    public function get_account_supplier() 
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
    
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $agent = $_GET['agent'] ? $_GET['agent'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $role_id = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'airport',
            1 => 'amount',
        ];
     
        $SQLagent = "";
        if (trim($agent) != "" && trim($agent) != "*") {
            $SQLagent = " AND source='$agent' ";
        }

        // $SQLstatus = " AND (status='1' OR status='4') ";
        $SQLstatus = " AND status=1";
        

        $SQLairport = "";
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND airport='" . $airport . "'";
        }
        $filter_date = 'booking_at';

        $SQLFilterDate = "";
        
        $SQLFilterDate = "and date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        
        $SQLref = " AND (reference IS NOT NULL AND reference !='') AND reference NOT LIKE 'GL-%' ";

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLagent $SQLref ";
        $sql_data = "SELECT airport,count(*) as totalQty, SUM(price) as totalAmount FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLagent $SQLref ";

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


        $GroupBy = " GROUP BY airport";

        $sql_count = $sql_count . $condition . $GroupBy;

        $sql_data = $sql_data . $condition;

        // exit($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();

        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $GroupBy.$OrderBy . $SortBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
       
        $totalll_count = 0;
        $grand_totall = array();

        // pre($result);
        foreach ($result as $value) 
        {
            $row = array();
            $totalAmount = round($value->totalAmount - ($value->totalQty*1.95),2);
            $row[] = $value->airport;
            $row[] = $value->totalQty;
            $row[] = $totalAmount;

            // $id = id_en($value->id);
    
            $action = "<button class='gcostAdd btn btn-outline-success btn-sm' data-id='$value->airport' data-qty='$value->totalQty' data-amount='$totalAmount'>Add</button>";
            
            $row[] = $action;


            $data[] = $row;

            // $totalll_count=sizeof($data);

        }

        if (strval($role_id) > 1) {

            $totalll_count_result = sizeof($grand_totall);

        } else {

            $totalll_count_result = ($total_count)? $total_count->total: 0;

        }

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_account_supplier2() 
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
    
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $role_id = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'airport',
            1 => 'dateFrom',
            2 => 'dateTo',
            3 => 'qty',
            4 => 'Amount',
        ];

        $SQLairport = "";
        if (trim($airport) != "" && $airport != "*") {
            $SQLairport = " AND airport='" . $airport . "'";
        }
        $SQLtype=" AND account_type=2";

        $sql_count = "SELECT count(*) as total FROM tbl_booking_account WHERE 1=1  $SQLairport $SQLtype";
        $sql_data = "SELECT * FROM `tbl_booking_account`  WHERE 1=1  $SQLairport $SQLtype";

        // echo"$sql_data";

        //exit($sql_data);


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

        $sql_count = $sql_count . $condition;

        $sql_data = $sql_data . $condition;

        // exit($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();

        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $OrderBy . $SortBy . $Limit;

        
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
       
        $totalll_count = 0;
        $grand_totall = array();

        // pre($result);
        foreach ($result as $value) 
        {
            $created_at = date("Y-m-d", strtotime($value->created_at));

            $row = array();
            $percentage = round(($value->google_cost/$value->amount)*100,2);
            $row[] = $created_at;
            $row[] = $value->airport;
            $row[] = $value->date_from; 
            $row[] = $value->date_to;
            $row[] = $value->qty;
            $row[] = $value->amount;
            $row[] = ($value->google_cost_type == 1)?$value->google_cost:'';
            $row[] = $percentage.'%';

            $images = explode(',',$value->gcost_image);
            $img='';
            foreach ($images as $key => $image) {
                if (!empty(trim($image))) { 
                    $img .= '<a href="' . base_url('screenshot/' . $image) . '" target="_blank"><img src="' . base_url('screenshot/' . $image) . '" class="mb-1" width="120"></a>';
                }
            }
            $row[]= $img;

            $id = id_en($value->id);
    
            // $action = "<button class='downlodBtn btn btn-outline-danger btn-sm' data-id='$value->airport' data-dfrom='$value->date_from' data-dto='$value->date_to'>Download</button>";
            $google_cost = ($value->google_cost_type ==2)? round(($value->google_cost*100)/$value->amount,2):$value->google_cost;
            $action = '<div class="btn-group">
                    <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                    </a>
                    <div class="dropdown-menu">          
                         <a href="'. base_url('account/download?airport='.$value->airport.'&dateFrom='.$value->date_from.'&dateTo='.$value->date_to.'&type=2') .'" class=" dropdown-item">
                            <i data-feather="download"></i> Download
                        </a>
                        <a class="editBtn dropdown-item" 
                           data-id="' . htmlspecialchars($id) . '" 
                           data-qty="' . htmlspecialchars($value->qty) . '" 
                           data-amount="' . htmlspecialchars($value->amount) . '" 
                           data-gtype="' . htmlspecialchars($value->google_cost_type) . '" 
                           data-gcost="' . htmlspecialchars($google_cost) . '" 
                           data-img="' . htmlspecialchars($value->gcost_image) . '"
                           data-desc="' . htmlspecialchars($value->description) . '">
                            <i data-feather="edit"></i> Edit
                        </a>
                        
                        <a class="deleteBtn dropdown-item" 
                           data-id="' . htmlspecialchars($id) . '">
                            <i data-feather="trash"></i> Delete
                        </a>
                    </div>
                  </div>';

            
            $row[] = $action;


            $data[] = $row;

        }

        if (strval($role_id) > 1) {

            $totalll_count_result = sizeof($grand_totall);

        } else {

            $totalll_count_result = ($total_count)? $total_count->total: 0;

        }

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function add_account()
    {
        // Only allow POST request
        if ($this->request->getMethod() !== 'post') {
            $output = [
                'error' => true,
                'message' => 'Method Not Allowed',
            ];
            return $this->setResponseFormat('json')->respond($output);
        }

        // Get POST data
        $id = ($this->request->getPost('id')) ? id_de($this->request->getPost('id')):'';
    
        $airport = $this->request->getPost('airport');
        $qty = $this->request->getPost('qty');
        $amount = $this->request->getPost('amount');
        $googleCosType = $this->request->getPost('gtype');
        $googleCost = $this->request->getPost('google_cost');
        $description = $this->request->getPost('description');
        $account_type = $this->request->getPost('account_type');

        $googleCost = ($googleCosType == 2)? round(($googleCost/100)*$amount,2):$googleCost;
        $DateFrom = $this->request->getPost('DateFrom');
        $DateTo = $this->request->getPost('DateTo');

        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        $DateTo = date('Y-m-d', strtotime($DateTo));
        // Validate uploaded file
        // $images = $this->request->getFile('gcost_image');
        $images = $this->request->getFiles();
        $uploadedFiles = [];

        // if (empty($images) && empty($id)) {
        if (!isset($images['gcost_image']) && empty($id)) {
            $output = [
                'error' => true,
                'message' => 'Invalid image file',
            ];
            return $this->setResponseFormat('json')->respond($output);
        }

        // if ($image->getSize() > 2 * 1024 * 1024) { // 2MB limit
        //     return $this->response->setStatusCode(400)->setJSON(['error' => 'Image file is too large']);
        // }

        $images = $images['gcost_image'];
  
        $sql_query = "SELECT * FROM `tbl_booking_account` WHERE id='$id'";
        $result = $this->db->query($sql_query)->getRow();
       
        if ($result) {
            $airport = $result->airport;
            $DateFrom = $result->date_from;
            $DateTo = $result->date_to;
            $qty = $result->qty;
            $amount = $result->amount;

            $percentage = round(($googleCost/$amount)*100,2);

            $sql_query= "UPDATE `tbl_booking_account` SET google_cost_type='$googleCosType',google_cost='$googleCost', description='$description' WHERE id='$id'";
            if ($images) {
                foreach ($images as $image) {
                    if ($image->isValid() && !$image->hasMoved()) {
                        $newName = $image->getRandomName();
                        $image->move(WRITEPATH . 'screenShots', $newName);
                        $uploadedFiles[] = $newName; // Or full path if needed

                        // Optional: Delete old image if needed
                        if (isset($result->gcost_image)) 
                        {
                            $images = explode(',',$result->gcost_image);
                            foreach ($images as $key => $image) 
                            {
                                if (!empty(trim($image))) {
                                    $oldFilePath = WRITEPATH . 'screenShots/' . $image;
                                    if (file_exists($oldFilePath)) {
                                        unlink($oldFilePath);
                                    }
                                }
                            }
                        }
                    }
                }
                $imageNames = implode(',', $uploadedFiles);

                $sql_query= "UPDATE `tbl_booking_account` SET google_cost='$googleCost', gcost_image='$imageNames', description='$description' WHERE id='$id'";
                // pre($sql_query);
            }
                
            $result = $this->db->query($sql_query);

            $bookings = get_account_bookings($DateFrom, $DateTo, $airport);
            foreach ($bookings as $key => $b) {
                $gcost = round(($percentage/100)*($b->price-1.95),2);
                $sql_query1= "UPDATE `tbl_booking` SET google_cost='$gcost' WHERE id='$b->id'";
                $this->db->query($sql_query1);
            }
            $message = 'Google cost updated successfully';
        }
            

        $sql_query = "SELECT * FROM `tbl_booking_account` WHERE airport='$airport' AND ((date_from >= '$DateFrom' AND date_to <= '$DateTo') OR (date_from <= '$DateTo' AND date_to >= '$DateFrom'))";
        $result = $this->db->query($sql_query)->getRow();
        // pre($result);
        if (!$result) {

            foreach ($images as $image) 
            {
                if ($image->isValid() && !$image->hasMoved()) 
                {
                    $newName = $image->getRandomName();
                    $image->move(WRITEPATH . 'screenShots', $newName);
                    $uploadedFiles[] = $newName; // Or full path if needed
                }
            }
            $imageNames = implode(',', $uploadedFiles);
            // pre($imageNames);
            // // Move uploaded file to writable/uploads/
            // $newName = $image->getRandomName();
            // $image->move(WRITEPATH.'/screenShots', $newName);
            
            $percentage = round(($googleCost/$amount)*100,2);
            

            $sql_query ="INSERT INTO `tbl_booking_account`(`airport`, `date_from`, `date_to`, `qty`, `amount`, `google_cost_type`, `google_cost`, `gcost_image`,`description`,`account_type`) VALUES ('$airport', '$DateFrom', '$DateTo', '$qty', '$amount', '$googleCosType', '$googleCost','$imageNames','$description', '$account_type')";
            // print_r($percentage);
            // pre($sql_query);
            $result = $this->db->query($sql_query);

            $bookings = get_account_bookings($DateFrom, $DateTo, $airport);
            foreach ($bookings as $key => $b) {
                $gcost = round(($percentage/100)*($b->price-1.95),2);
                $sql_query= "UPDATE `tbl_booking` SET google_cost='$gcost' WHERE id='$b->id'";
                $this->db->query($sql_query);
            }

            $message = 'Google cost added successfully';
        }elseif (empty($id)) {
           $message = 'Already exist in database';
        }

        $output = [
            'error' => false,
            'message' => $message,
            // 'data' => $sql_query,
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function delete_account()
    {
         if (!$this->request->isAJAX()) {
            $output = [
                'error' => true,
                'message' => 'Direct access not allowed',
            ];
            return $this->setResponseFormat('json')->respond($output);
        }
        
        $id = $this->request->getGet('id');
        
        // Validate ID
        if (empty($id)) {
            $output = [
                'error' => true,
                'message' => 'ID is required',
            ];
            return $this->setResponseFormat('json')->respond($output);
        }
       $id=id_de($id);

        $sql_query = "SELECT * FROM `tbl_booking_account` WHERE id='$id'";
        $result = $this->db->query($sql_query)->getRow();
        $message = 'Data is not exist';
        if ($result) {
            if (isset($result->gcost_image)) 
            {
                $images = explode(',',$result->gcost_image);
                foreach ($images as $key => $image) 
                {
                    if (!empty(trim($image))) {
                        $oldFilePath = WRITEPATH . 'screenShots/' . $image;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                }
            }

            $bookings = get_account_bookings($result->date_from, $result->date_to, $result->airport);

            foreach ($bookings as $key => $b) {
                $sql_query= "UPDATE `tbl_booking` SET google_cost=NULL WHERE id='$b->id'";
                $this->db->query($sql_query);
            }

            $sql_query="DELETE FROM `tbl_booking_account` WHERE id='$id'";
            $deleted = $this->db->query($sql_query);
            if ($deleted) {
                $message ='Account deleted successfully';
            } else {
                $message = 'Failed to delete account';
            }
        }
        $output = [
            'error' => false,
            'message' => $message,
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function viewScreenshot($filename)
    {
        $path = WRITEPATH . 'screenShots/' . $filename;

        if (!is_file($path)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found.');
        }

        return $this->response->setHeader('Content-Type', mime_content_type($path))
                ->setBody(file_get_contents($path));
    }

    public function downlaod_account_bookings()
    {
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $DateFrom = $_GET['dateFrom'] ? $_GET['dateFrom'] : '';
        $DateTo = $_GET['dateTo'] ? $_GET['dateTo'] : '';
        $type = $_GET['type'] ? $_GET['type'] : '';

        $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        $SQLstatus = 'AND status=1';
        $SQLreference = ($type==2)? "AND (reference IS NOT NULL AND reference !='') AND reference NOT LIKE 'GL-%' ":"AND (reference IS NOT NULL AND reference !='') AND reference LIKE 'GL-%' ";
        $sql = "SELECT reference, airport, source, firstName, surname, email, contactNumber, price, google_cost, status, created_at  FROM `tbl_booking` WHERE airport ='$airport' $SQLFilterDate $SQLstatus $SQLreference";
        // pre($sql);
        $bookings = $this->db->query($sql)->getResult();
        // pre($bookings);

        $date = date('Y-m-d');
        $filePath = WRITEPATH . 'exports/';
        $fileName = 'account_bookings_-'.$date.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV
        fputcsv($file, ['Reference', 'Airport', 'Source','First Name', 'Last Name', 'Email', 'Contact No', 'Price', 'Google Cost', 'Status', 'Created At']);

        // Add rows to the CSV file
        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking->reference,
                $booking->airport,
                $booking->source,
                $booking->firstName,
                $booking->surname,
                $booking->email,
                $booking->contactNumber,
                $booking->price,
                $booking->google_cost,
                $booking->status,
                $booking->created_at
            ]);
        }
        // pre($file);

        fclose($file);
        // file_put_contents($filePath . $fileName, $file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }
}