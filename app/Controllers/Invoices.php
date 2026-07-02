<?php
namespace App\Controllers;
use App\Models\InteliquentModel;
use App\Models\SupplierModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Invoices extends BaseController
{   
    use ResponseTrait;
    public function __construct()
    {    
        $this->supplier = new SupplierModel;
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
        
        return view('invoices/index',$data);        
    }
    // Departure invoice complete
    public function admin_invoice()
    {

        // echo "umair";
        $data = [
            "page_title" => "Departure Invoice",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('departure'), "title" => "Departure Invoice", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $sql_data = "select id,name from tbl_products";
        $result = $this->db->query($sql_data)->getResult();
        $data['products'] = $result;

        $data['suppliers'] = $this->supplier->findAll();
        // echo'<pre>';print_r($report_result);die;
        $DateFrom = '2024-09-01 01:03:15';
        $DateTo = date('Y-m-d H:i:s');

        $SQLWebsiteCondition = " AND source IS NOT NULL AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source !='CPD' AND source!='CTAP' AND source !='P4U' AND source!='Holiday Extras' AND source!='ParkVia' AND source!='FreeToMove' AND source!='Airport Parking With Us' AND source!='JBF' AND source !='Park&Fly' AND source!='YTE' AND source!='HCP' ";

        $SQLFilterDate = " AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        $SQLstatus = " AND status='1' "; 

        // $query ="SELECT product_code, name, parent,meet_and_greet,park_mark,product_type FROM `tbl_products`  WHERE meet_and_greet=1 OR park_mark=1";
        // $result = $this->db->query($query)->getResult();
        // $sql = "UPDATE `tbl_booking` SET status='2' WHERE refund_amount IS NOT NULL AND (booked_at IS NOT NULL AND booked_at !='0000-00-00 00:00:00') AND booked_at >= '$DateFrom' AND booked_at <= '2024-09-30'";

        // $sql = "UPDATE `tbl_products` SET product_type='On Airport' WHERE on_airport=1";
        // $result = $this->db->query($sql);

        // $sql ="ALTER TABLE tbl_products ADD product_type VARCHAR(30) NULL";
        // if ($this->db->query($sql)) {
        //     echo "Table created successfully!";
        // } else {
        //     echo "Failed to create table.";
        // }
        // $AUTH=session()->get('AUTH');
        // pre($AUTH);
        // $this->update_source();
        
        return view('invoices/admin_invoice', $data);
    }

    public function update_source()
    {
        // Load the XLSX file
        $filePath = WRITEPATH . 'uploads/data.xlsx'; // Change to your file path
        $spreadsheet = IOFactory::load($filePath);

        // Get the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Extract the data
        $data = $sheet->toArray();

        foreach ($data as $key => $v) {
            if ($v[0] != 'Reference') {
                if ($v[1] == 'ParkVIa') {
                    $v[1] = 'ParkVia';
                }
                $sql = "UPDATE `tbl_booking` SET source='$v[1]' WHERE reference='$v[0]'";
                $this->db->query($sql);
            }
        }
        // $sql = "UPDATE `tbl_booking` SET source='Holiday Extras' WHERE reference='HLMXTH'";
        // $result = $this->db->query($sql);
    }

    public function get_admin_invoice()
    {
        $data = $this->request->getVar();
        // $search = $this->request->getVar('search')['value'];
        $status = 1;

        $airport = $_GET['Airport'] ? $_GET['Airport'] : '';
        $product = $_GET['Product'] ? $_GET['Product'] : '';

        $inputLabels = $_GET['inputLabels'] ? $_GET['inputLabels'] : '';
        $inputValues = $_GET['inputValues'] ? $_GET['inputValues'] : '';

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $SQLairport = "";
        if (trim($airport) != "") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" || trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `id`= '$product' ";
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            $SQLstatus = " AND b.status='$status' ";
        }
        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%')";

        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";
        // if ($filter_date == "booking_at") {
        //     $SQLFilterDate = "and date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "departure_at") {
        //     $SQLFilterDate = "and date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "return_at") {
        //     $SQLFilterDate = "and date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // }

        // $sql_data = "SELECT count(*) as qty, SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLproduct $SQLref";
        $sql_data = "SELECT  p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_booking` b 
                    LEFT JOIN `tbl_products` p ON b.product_id=p.id
                    LEFT JOIN (
                        SELECT *
                        FROM tbl_booking_collect c
                        WHERE c.id = (
                            SELECT MIN(id) 
                            FROM tbl_booking_collect 
                            WHERE booking_id = c.booking_id
                        )
                        AND (c.status = 'collected' OR c.status = 'returned')
                    ) bc ON b.id = bc.booking_id 
                    WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref";


        $sql_data = $sql_data;
    
        $OrderBy = " ORDER BY p.name ASC";
        
        $sql_data .= $OrderBy;
        // $result = $this->db->query($sql_data)->getResult();
        $result = $this->db->query($sql_data)->getRow();

        $data ='';
        $totQty =0;
        $totCollected =0;
        $totNoShow =0;
        $totFee =0;
        $amount =0;
        $totNetAmount =0;
        $totNetAmountC =0;
        $amountC =0;
        $saleTotal =0;
        $resultp = array();

        // $sql_data = "SELECT id, name FROM `tbl_products` WHERE  1=1  $product_code  ";
        // $resultp = $this->db->query($sql_data)->getRow();

        if(trim($product) == "*") {
            // $sql_data = "SELECT id, name FROM `tbl_products` WHERE  parent='$airport'";

            $sql_data = "SELECT  p.id as pid,p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_products` p 
                        LEFT JOIN `tbl_booking` b ON p.id=b.product_id  
                        LEFT JOIN (
                            SELECT *
                            FROM tbl_booking_collect c
                            WHERE c.id = (
                                SELECT MIN(id) 
                                FROM tbl_booking_collect 
                                WHERE booking_id = c.booking_id
                            )
                            AND (c.status = 'collected' OR c.status = 'returned')
                        ) bc ON b.id = bc.booking_id
                        WHERE 1=1 $SQLFilterDate $SQLstatus $SQLref $SQLairport GROUP BY p.name";
            $resultp = $this->db->query($sql_data)->getResult();
        }
        // pre($resultp);
        if (is_array($resultp) && !empty($resultp)) 
        {
            foreach ($resultp as $key => $p) 
            {

                // $SQLproduct = " AND   `product_id`= '$p->id'";
                // $sql_data = "SELECT count(*) as qty, SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLproduct $SQLref ";
                // $sql_data .= $OrderBy;

                // $result = $this->db->query($sql_data)->getRow();
                $price=0;
                $netAmount=0;
                if ($p->qty > 0) 
                {
                    $totQty += $p->qty;
                    $totCollected += $p->collected;
                    $totNoShow += $p->noShow;
                    $fee = $p->qty*1.95;
                    if ($p->totPrice) 
                    {
                        $amount +=$p->totPrice - $fee;
                        $totFee += $fee;
                        $price = $p->totPrice - $fee;
                        $netAmount = $price - ($price/100)*30; 
                        $totNetAmount += $price - ($price/100)*30;
                    }
                    $priceC=0;
                    $netAmountC=0;
                    if ($p->collectedAmount) 
                    {
                        $amountC +=$p->collectedAmount;
                        $priceC = $p->collectedAmount;
                        $netAmountC = $priceC - ($p->collectedAmount/100)*30;
                        $totNetAmountC += $priceC - ($p->collectedAmount/100)*30;
                    }
                    $data .='<tr>';
                    $data .= '<td>'.$p->name.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->collected.'</td>';
                    $data .= '<td>'.$p->noShow.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td>'.$netAmount.'</td>';
                    $data .= '<td>'.$priceC.'</td>';
                    $data .= '<td>'.$netAmountC.'</td>';
                    $data .= '<td>'.$fee.'</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=admin&product='.$p->pid).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
                    $data .= '</tr>';
                }
            }
            $data .='<tr>';
            $data .= '<th>Total</th>';
            $data .= '<th>'.$totQty.'</th>';
            $data .= '<th>'.$totCollected.'</th>';
            $data .= '<th>'.$totNoShow.'</th>';
            $data .= '<th>'.$amount.'</th>';
            $data .= '<th>'.$totNetAmount.'</th>';
            $data .= '<th>'.$amountC.'</th>';
            $data .= '<th>'.$totNetAmountC.'</th>';
            $data .= '<th>'.$totFee.'</th>';
            $data .= '<th></th>';
            $data .= '</tr>';
                
        }else
        {
            $totFee = $result->qty*1.95;
            if ($result->totPrice) 
            {
                $amount =$result->totPrice -  $totFee;
            }
            if ($result->collectedAmount) 
            {
                $amountC =$result->collectedAmount;
            }
            $totNetAmount = $amount - ($amount/100)*30;
            $totNetAmountC = $amountC - ($amountC/100)*30;
            

            $data .='<tr>';
            $data .= '<td>'.$result->name.'</td>';
            $data .= '<td>'.$result->qty.'</td>';
            $data .= '<td>'.$result->collected.'</td>';
            $data .= '<td>'.$totNoShow.'</td>';
            $data .= '<td>'.$amount.'</td>';
            $data .= '<td>'.$totNetAmount.'</td>';
            $data .= '<td>'.$amountC.'</td>';
            $data .= '<td>'.$totNetAmountC.'</td>';
            $data .= '<td>'.$totFee.'</td>';
            $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=admin&product='.$product).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
        }

        $inputs = array();
        $extra_price = 0;
        if ($inputLabels) {
            foreach ($inputLabels as $key => $v) {
                if ($inputValues[$key]) {
                    $extra_price+= $inputValues[$key];
                    $inputs[]= array(
                        'label' => $v,
                        'value' => $inputValues[$key]
                    );
                }
            }
        }
        $saleTotal = $amount;
        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('dmY').rand(0, 99),

            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'netAmount' => round($totNetAmount,2),
            'extra_price' => $extra_price,

            'airport' => $airport,
            'product' => $product,
            'inputs' => $inputs,
            'dateFrom' => $DateFrom,
            'dateTo' => $DateTo,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    } 
    // Operator invoice complete
    public function operator_invoice()
    {

        // echo "umair";
        $data = [
            "page_title" => "Operator Departure Invoice",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('departure'), "title" => "Departure Invoice", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $sql_data = "select id,name from tbl_products";
        $result = $this->db->query($sql_data)->getResult();
        $data['products'] = $result;
        
        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['operators'] = $agent_result;

        $data['suppliers'] = $this->supplier->findAll();
        // echo'<pre>';print_r($report_result);die;

        return view('invoices/operator_invoice', $data);
    }  

    public function get_operator_invoice()
    {
        $data = $this->request->getVar();
        // $search = $this->request->getVar('search')['value'];

        $airport = $_GET['Airport'] ? $_GET['Airport'] : '';
        $operator = $_GET['operator'] ? $_GET['operator'] : '';
        $product = $_GET['Product'] ? $_GET['Product'] : '';

        $googleCost = $_GET['googleCost'] ? $_GET['googleCost'] : 0;
        $googleCostRefund = $_GET['googleCostRefund'] ? $_GET['googleCostRefund'] : 0;
        $disputeAmount = $_GET['disputeAmount'] ? $_GET['disputeAmount'] : 0;
        $officeCost = $_GET['officeCost'] ? $_GET['officeCost'] : 0;
        $inputLabels = $_GET['inputLabels'] ? $_GET['inputLabels'] : '';
        $inputValues = $_GET['inputValues'] ? $_GET['inputValues'] : '';

        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $SQLairport = "";
        if (trim($airport) != "") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLoperator = "";
        $GroupBy = "GROUP BY p.name";
        if (trim($operator) != "" && trim($operator) != "*") {
            $SQLoperator = " AND b.operator_id='$operator' ";
            $GroupBy = "";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" && trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `id`= '$product' ";
        }

        $SQLstatus = " AND (b.status='1' OR b.status='4') ";
        
        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%')";

        $SQLFilterDate = "AND date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // if ($filter_date == "booking_at") {
        //     $SQLFilterDate = "and date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "departure_at") {
        //     $SQLFilterDate = "and date(depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // } else if ($filter_date == "return_at") {
        //     $SQLFilterDate = "and date(return_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // }

        // $sql_data = "SELECT count(*) as qty, SUM(price) as totPrice FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLproduct $SQLref";
        $sql_data = "SELECT  p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice, SUM(google_cost) as totGcost, SUM(b.refund_amount) as totRefund, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_booking` b 
                    LEFT JOIN `tbl_products` p ON b.product_id=p.id 
                    LEFT JOIN (
                        SELECT *
                        FROM tbl_booking_collect c
                        WHERE c.id = (
                            SELECT MIN(id) 
                            FROM tbl_booking_collect 
                            WHERE booking_id = c.booking_id
                        )
                        AND (c.status = 'collected' OR c.status = 'returned')
                    ) bc ON b.id = bc.booking_id
                    WHERE 1=1 $SQLFilterDate $SQLairport $SQLoperator $SQLproduct $SQLstatus $SQLref";


        $sql_data = $sql_data;
    
        $OrderBy = " ORDER BY b.depart_at ASC";
        
        $sql_data .= $OrderBy;
        // $result = $this->db->query($sql_data)->getResult();
        $result = $this->db->query($sql_data)->getRow();

       
        $data='';
        $totRefund =0;
        $totQty =0;
        $totCollected =0;
        $totNoShow =0;
        $totFee =0;
        $amount =0;
        $amountC =0;
        $totGcost =0;
        $saleTotal =0;
        $resultp ='';

        if(trim($product) == "*") {
            // $sql_data = "SELECT id, name FROM `tbl_products` WHERE  parent='$airport'";
            $sql_data = "SELECT  p.id as pid,p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice, SUM(b.google_cost) as totGcost, SUM(b.refund_amount) as totRefund, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id
                LEFT JOIN (
                    SELECT *
                    FROM tbl_booking_collect c
                    WHERE c.id = (
                        SELECT MIN(id) 
                        FROM tbl_booking_collect 
                        WHERE booking_id = c.booking_id
                    )
                    AND (c.status = 'collected' OR c.status = 'returned')
                ) bc ON b.id = bc.booking_id WHERE 1=1 $SQLFilterDate $SQLstatus $SQLref $SQLairport $SQLoperator $GroupBy";
            // pre($sql_data);
            $resultp = $this->db->query($sql_data)->getResult();
        }
         // pre($sql_data);
        if (is_array($resultp)) 
        {
            foreach ($resultp as $key => $p) 
            {
                $price=0;
                $priceC=0;
                if ($p->qty > 0) 
                {
                    $totRefund += $p->totRefund;
                    $totQty += $p->qty;
                    $totCollected += $p->collected;
                    $totNoShow += $p->noShow;
                    $fee = $p->qty*1.95;
                    if ($p->totPrice) 
                    {
                        $amount +=$p->totPrice - $fee;
                        $totFee += $fee;
                        $price = $p->totPrice- $fee;
                    }
                    if ($p->collectedAmount) 
                    {
                        $amountC +=$p->collectedAmount;
                        $priceC = $p->collectedAmount;
                    }
                    if ($p->totGcost) {
                        $totGcost +=$p->totGcost;
                    }

                    $data .='<tr>';
                    $data .= '<td>'.$p->name.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->collected.'</td>';
                    $data .= '<td>'.$p->noShow.'</td>';
                    $data .= '<td>'.$p->totRefund.'</td>';
                    $data .= '<td>'.$p->totGcost.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td>'.$priceC.'</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=operator&product='.$p->pid).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
                    $data .= '</tr>';
                }
            }
            $data .='<tr>';
            $data .= '<th>Total</th>';
            $data .= '<th>'.$totQty.'</th>';
            $data .= '<th>'.$totCollected.'</th>';
            $data .= '<th>'.$totNoShow.'</th>';
            $data .= '<th>'.$totRefund.'</th>';
            $data .= '<th>'.$totGcost.'</th>';
            $data .= '<th>'.$amount.'</th>';
            $data .= '<th>'.$amountC.'</th>';
            $data .= '<th></th>';
            $data .= '</tr>';
                
        }else
        {
            $totRefund = $result->totRefund;
            $totQty = $result->qty;
            $totCollected = $result->collected;
            $totFee = $result->qty*1.95;
            if ($result->totPrice) 
            {
                $amount =$result->totPrice- $totFee; 
            }
            if ($result->collectedAmount) 
            {
                $amountC =$result->collectedAmount; 
            }
            $data .='<tr>';
            $data .= '<td>'.$result->name.'</td>';
            $data .= '<td>'.$result->qty.'</td>';
            $data .= '<td>'.$totCollected.'</td>';
            $data .= '<td>'.$result->noShow.'</td>';
            $data .= '<td>'.$result->totRefund.'</td>';
            $data .= '<td>'.$result->totGcost.'</td>';
            $data .= '<td>'.$amount.'</td>';
            $data .= '<td>'.$amountC.'</td>';
            $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=operator&product='.$product).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
        }

        $inputs = array();
        $extra_price = 0;
        if ($inputLabels) {
            foreach ($inputLabels as $key => $v) {
                if ($inputValues[$key]) {
                    $extra_price+= $inputValues[$key];
                    $inputs[]= array(
                        'label' => $v,
                        'value' => $inputValues[$key]
                    );
                }
            }
        }

        $saleTotal = $amount - $totFee;
        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('dmY').rand(0, 99),

            'amount' => round($amount,2),
            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'googleCost' => $googleCost,
            'googleCostRefund' => $googleCostRefund,
            'refunds' => $totRefund,
            'disputeAmount' => $disputeAmount,
            'officeCost' => $officeCost,
            'extra_price' => $extra_price,

            'airport' => $airport,
            'product' => $product,
            'inputs' => $inputs,
            'dateFrom' => $DateFrom,
            'dateTo' => $DateTo,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Apply Google Cost
    public function apply_gcost()
    {

        // echo "umair";
        $data = [
            "page_title" => "Apply Google Cost",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('apply_gcost'), "title" => "Apply Google Cost", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $sql_data = "select id,name from tbl_products";
        $result = $this->db->query($sql_data)->getResult();
        $data['products'] = $result;
        
        $sql_data = "select id,description from tbl_operators";
        $agent_result = $this->db->query($sql_data)->getResult();
        $data['operators'] = $agent_result;

        $data['suppliers'] = $this->supplier->findAll();
        // echo'<pre>';print_r($report_result);die;

        return view('invoices/apply_gcost', $data);
    } 

    public function get_apply_gcost()
    {
        $data = $this->request->getVar();

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $website = $_GET['website'] ? $_GET['website'] : '';
        $gcost = $_GET['gcost'] ? $_GET['gcost'] : '';

        $DateFrom = $_GET['dateFrom'] ? $_GET['dateFrom'] : '';
        $DateTo = $_GET['dateTo'] ? $_GET['dateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND airport='$airport'";
        }

        $SQLwebsite = "";

        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND source ='$website' ";
        }

        $SQLstatus = " AND (status='1' OR status='4') ";
        
        $SQLref = " AND (reference IS NOT NULL AND reference !='') AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%') ";

        $SQLFilterDate = "AND (date(depart_at) BETWEEN '$DateFrom' AND '$DateTo')";
      
        $sql_data = "SELECT  source,count(id) as qty, SUM(price) as totPrice, SUM(refund_amount) as totRefund FROM `tbl_booking` WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLwebsite";
        $result = $this->db->query($sql_data)->getResult();

        if ($gcost) 
        {
            $sql_data2 = "SELECT  id,airport, price, google_cost FROM `tbl_booking` WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLwebsite";
            $result2 = $this->db->query($sql_data2)->getResult();
            if ($result2) {
                $googleCost = round($gcost/count($result2), 2);
                // echo $googleCost;pre($result2);

                foreach ($result2 as $key => $r) {
                    $sql_query = "UPDATE `tbl_booking` SET google_cost='$googleCost' WHERE id='$r->id' ";
                    $this->db->query($sql_query);
                }
            }
        }

        $data='';
        $totRefund =0;
        $totQty =0;
        $totFee =0;
        $amount =0;
        $saleTotal =0;

        // pre($sql_data);
        if (is_array($result)) 
        {
            foreach ($result as $key => $p) 
            {
                $price=0;
                
                if ($p->qty > 0) 
                {
                    $totRefund += $p->totRefund;
                    $totQty += $p->qty;
                    $fee = $p->qty*1.95;
                    if ($p->totPrice) 
                    {
                        $amount +=$p->totPrice;
                        $totFee += $fee;
                        $price = $p->totPrice;
                    }

                    $data .='<tr>';
                    $data .= '<td>'.$p->source.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->totRefund.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=supplier&product=&source='.urlencode($p->source)).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
                    $data .= '</tr>';
                }
            }
            $data .='<tr>';
            $data .= '<th>Total</th>';
            $data .= '<th>'.$totQty.'</th>';
            $data .= '<th>'.$totRefund.'</th>';
            $data .= '<th>'.$amount.'</th>';
            $data .= '<th></th>';
            $data .= '</tr>';
                
        }

        // $saleTotal = $amount - $totFee;
        $saleTotal = $amount;
        $operatorTotal = ($saleTotal/100)* 30; 
        $subTotal = $saleTotal - $operatorTotal;

        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('M-Y',strtotime($DateFrom)),

            'amount' => round($amount,2),

            'airport' => $airport,
            'website' => $website,
            'dateFrom' => $DateFrom,
            'dateTo' => $DateTo,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    
    // Paid Invoices
    public function paid_invoice()
    {

        // echo "umair";
        $data = [
            "page_title" => "Paid Invoices",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('paid'), "title" => "Paid Invoices", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $data['websites'] = $this->db->query($sql_data)->getResult();

        $sql_data = "select id,name from tbl_products";
        $result = $this->db->query($sql_data)->getResult();
        $data['products'] = $result;

        $data['suppliers'] = $this->supplier->findAll();
        // echo'<pre>';print_r($report_result);die;

        return view('invoices/paid_invoice', $data);
    }

    public function get_paid_invoice()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $reference = $_GET['reference'];
        $carReg = $_GET['carReg'];
        $airport = $_GET['airport'];
        $DateFrom = $_GET['DateFrom'] ? $_GET['DateFrom'] : '';
        $DateTo = $_GET['DateTo'] ? $_GET['DateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);
        $website = $_GET['website'];
        $role_id = $_GET['role_id'];

        $condition = "";
        $table_map = [
            0 => 'reference',
            1 => 'airport',
            2 => 'booked_at',
            3 => 'depart_at',
            4 => 'return_at',
            5 => 'carReg',
            6 => 'price',
            7 => 'status'
        ];

        $SQLreference = "";
        if (trim($reference) != "") {
            $SQLreference = " AND reference='$reference' ";
        }

        $SQLCarRegistration = "";
        if (trim($carReg) != "") {
            $SQLCarRegistration = " AND carReg LIKE '$carReg%' ";
        }
     

        $SQLwebsite = "";
        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND source='$website' ";
        }

        $SQLstatus = " AND is_paid='1' ";
        

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
        $SQLref = " AND (reference IS NOT NULL AND reference !='') AND(b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%') ";

        $sql_count = "SELECT count(*) as total FROM tbl_booking WHERE 1=1  $SQLFilterDate $SQLreference $SQLCarRegistration $SQLstatus $SQLairport $SQLwebsite $SQLref ";
        $sql_data = "SELECT * FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLreference $SQLCarRegistration $SQLstatus $SQLairport $SQLwebsite $SQLref  ";

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


        // exit($sql_data);

        $total_count = $this->db->query($sql_count)->getRow();

        $OrderBy = " ORDER BY id desc";
        $SortBy = ""; //. $this->request->getVar('order')[0]['dir'];


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        if (strval($role_id) > 1) {

            $sql_data .= $OrderBy . $SortBy;

        } else {
            $sql_data .= $OrderBy . $SortBy . $Limit;

        }
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
       
        $totalll_count = 0;
        $grand_totall = array();

            
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

            $sql_data3 = "SELECT product_code,name FROM `tbl_products` where id='$value->product_id' LIMIT 1";
            $result3 = $this->db->query($sql_data3)->getRow();
            $product_code = "";
            $product_name = "";

            if ($result3) {
                $product_code = $result3->product_code;
                $product_name = $result3->name;
            }


            $row[] = $value->reference;
            $sourceHtml = $value->source;
            if (empty($value->source)) {
                $sourceHtml = $value->source. " <button class='webEdit btn btn-outline-warning btn-sm' data-id='$value->id'>Edit</button>";
            }
            // $row[] = $value->source. " <button class='webEdit btn btn-outline-warning btn-sm' data-id='$value->id'>Edit</button>";
            // $row[] = $sourceHtml;
            $row[] = $value->airport . "\n" . $product_code;
            // $row[] = $value->firstName . " " . $value->surname;
            $row[] = date("d-M-Y", strtotime($value->booked_at));
            $row[] = date("d-M-Y", strtotime($value->depart_at));
            $row[] = date("d-M-Y", strtotime($value->return_at));
            $row[] = $value->carReg;
            $row[] = $value->price.'<br><span class="text-danger">'.$value->refund_amount.'</span>';

            // $row[] = $value->price;

            $id = id_en($value->id);
            $row[] = "<span class='badge badge-glow bg-success'>Paid</span>";
            
            $row[] = $operator_name;
            
            $row[] = $value->google_cost;


            $data[] = $row;

            // $totalll_count=sizeof($data);

        }

        if (strval($role_id) > 1) {

            $totalll_count_result = sizeof($grand_totall);

        } else {

            $totalll_count_result = $total_count->total;

        }

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }
    // Extra funtions
    public function admin_generate_invoice()
    {
        $data = $this->request->getVar();
        // $search = $this->request->getVar('search')['value'];
        $status = 1;

        $invoiceNo = $_GET['ginvoiceNo'] ? $_GET['ginvoiceNo'] : '';
        $airport = $_GET['gairport'] ? $_GET['gairport'] : '';
        $product = $_GET['gproduct'] ? $_GET['gproduct'] : '';

        $totFee = $_GET['gtotFee'] ? $_GET['gtotFee'] : 0;
        $netAmount = $_GET['gnetAmount'] ? $_GET['gnetAmount'] : 0;
        $saleTotal = $_GET['gsaleTotal'] ? $_GET['gsaleTotal'] : 0;
        $operatorTotal = $_GET['goperatorTotal'] ? $_GET['goperatorTotal'] : 0;
        $subTotal = $_GET['gsubTotal'] ? $_GET['gsubTotal'] : 0;
        $payable = $_GET['gpayable'] ? $_GET['gpayable'] : 0;
        $inputLabels = $_GET['ginputLabels'] ? $_GET['ginputLabels'] : '';
        $inputValues = $_GET['ginputValues'] ? $_GET['ginputValues'] : '';

        $supplier = $_GET['gsupplier'] ? $_GET['gsupplier'] : '';
        $isSupplier = $_GET['isSupplier'] ? $_GET['isSupplier'] : '';
        $filter_type = $_GET['filter_type'] ? $_GET['filter_type'] : '';

        $DateFrom = $_GET['gdateFrom'] ? $_GET['gdateFrom'] : '';
        $DateTo = $_GET['gdateTo'] ? $_GET['gdateTo'] : '';

        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        $DateTo = date('Y-m-d', strtotime($DateTo));

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" && trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `id`= '$product' ";
        }

        $SQLsupplier = "";

        if (trim($supplier) != "" && trim($supplier) != '*') {
            $SQLsupplier = " AND parent='$supplier' ";
            if ($isSupplier) {
                $SQLsupplier = " AND (source LIKE CONCAT('$supplier', '%') OR source='$supplier') ";
            }
        }

        $SQLstatus = "";
        if (trim($status) != "" && trim($status) != "*") {
            $SQLstatus = " AND b.status='$status' ";
        }
        $SQLref = " AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%') ";//|| b.reference LIKE 'GL %'
        if ($isSupplier) {
            $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GL %' AND reference NOT LIKE 'GO-%'";
        }
    
        $resultp=array();
        $result='';
        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";
        $OrderBy = " ORDER BY source ASC";
        $prefix = 'departure';
         
        

        if ($isSupplier) 
        {
            $sql_data = "SELECT  b.source as name, b.airport, count(b.id) as qty, SUM(b.price) as totPrice FROM `tbl_booking` b WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref $SQLsupplier GROUP BY b.source";
            $sql_data .= $OrderBy;
            $resultt = $this->db->query($sql_data)->getResult();
            // pre($sql_data);
            $resultp = $this->mergeAndSumByName($resultt,'ParkVia');
            $resultp = $this->mergeAndSumByName($resultt,'CPD');
            $resultp = $this->mergeAndSumByName($resultt,'P4U');
            $resultp = $this->mergeAndSumByName($resultt,'APU');
            $resultp = $this->mergeAndSumByName($resultt,'FreeToMove');
            $resultp = $this->mergeAndSumByName($resultt,'Holiday Extras');
            $resultp = $this->mergeAndSumByName($resultt,'Park&Fly');
            $resultp = $this->mergeAndSumByName($resultt,'YTE');
            $resultp = $this->mergeAndSumByName($resultt,'HCP');
            $resultp = $this->mergeAndSumByName($resultt,'CYP');
            $resultp = $this->mergeAndSumByName($resultt,'https://longtermparking.ie/');
            $resultp = $this->mergeAndSumByName($resultt,'Go Comparison');

            if ($isSupplier == 2) 
            {
                $sql_data = "SELECT  b.airport as name, count(b.id) as qty, SUM(b.price) as totPrice FROM `tbl_booking` b WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref $SQLsupplier GROUP BY b.airport ORDER BY b.airport ASC";
                $resultp = $this->db->query($sql_data)->getResult();
                // pre($sql_data);
            }
            
        }else{
            $sql_data = "SELECT  p.name, p.parent, b.airport, count(b.id) as qty, SUM(b.price) as totPrice FROM `tbl_booking` b LEFT JOIN `tbl_products` p ON b.product_id=p.id  WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref";
            $sql_data .= $OrderBy;
            $result = $this->db->query($sql_data)->getRow();
        }
        

        if(trim($product) == "*" && !$isSupplier) {

            $sql_data = "SELECT  p.id as pid,p.name, p.parent, b.airport, count(b.id) as qty, SUM(b.price) as totPrice FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id  WHERE 1=1 $SQLFilterDate $SQLstatus $SQLref $SQLsupplier $SQLairport $SQLproduct GROUP BY p.name";
            $resultp = $this->db->query($sql_data)->getResult();
            // pre($sql_data);
        }
        $html = '';
        $totQty=0;
        $totalFee=0;
        $totNetAmount=0;
        $amount=0;
        
        if (is_array($resultp) && !empty($resultp)) 
        {
            foreach ($resultp as $key => $p) 
            {
                $price = 0;
                $fee=0;
                $netAmount=0;
                if ($p->qty > 0) 
                {
                    $totQty += $p->qty;
                    if ($p->totPrice) 
                    {
                        
                        if ($isSupplier) {
                            $amount += $p->totPrice;
                            $price = $p->totPrice;
                            
                        }else{
                            $fee = $p->qty * 1.95;
                            $amount += $p->totPrice - $fee;
                            $price = $p->totPrice - $fee;
                            $totalFee +=  $fee;
                            // $netAmount = ($p->totPrice/100)*30;
                            // $totNetAmount += ($p->totPrice/100)*30;
                        }  

                        $netAmount = $price - ($price/100)*30;
                        $totNetAmount += $price - ($price/100)*30;
                    }

                    $html .='<tr>';
                    $html .= '<td>'.$p->name.'</td>';
                    $html .= '<td>'.$p->qty.'</td>';
                    $html .= '<td>'.$price.'</td>';
                    $html .= '<td>'.$netAmount.'</td>';
                    if (!$isSupplier) {
                        $html .= '<td>'.$fee.'</td>';
                    }
                    
                    $html .= '</tr>';
                }
            }
            $html .='<tr>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px; ">Total</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px; ">'.$totQty.'</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px; ">'.$amount.'</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px; ">'.$totNetAmount.'</th>';
            if (!$isSupplier) {
                $html .= '<th style="text-align: left;font-size: .757rem;">'.$totalFee.'</th>';
            }
            $html .= '</tr>';
            $html .='<tr><td colspan="4"></td></tr>';
                
        }

        $grossIncom=0; 
        if ($subTotal !=0) 
        {
            $grossIncom =round(($subTotal/100)*10,2);
        }
        if ($saleTotal !=0 && !$isSupplier) 
        {
            $grossIncom =round(($saleTotal/100)*10,2);
        }

        $data = [
            'totFee' => $totFee,
            'saleTotal' => $saleTotal,
            'netAmount' => $netAmount,
            'operatorTotal' => $operatorTotal,
            'subTotal' => $subTotal,
            'grossIncom' => $grossIncom,
            'totalPayable' => $payable,

            'inputLabels' => $inputLabels,
            'inputValues' => $inputValues,
        ];

        $data['invoiceNo'] = $invoiceNo;
        $data['dateFrom'] = $DateFrom;
        $data['dateTo'] = $DateTo;
        $data['result'] = $result;
        $data['html'] = $html;
        $data['isSupplier'] = $isSupplier;

        $html = view('invoices/admin_pdf', $data);

        // Setup Dompdf options
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // $dompdf->stream($airport.'-'.date('M-Y h:i:s a',strtotime($DateFrom)).'.pdf');
        // pre($html);

        // Save the generated PDF to a folder
        $output = $dompdf->output();

        $date = $DateFrom.'_'.$DateTo;
        $filePath = WRITEPATH . 'invoices/';
        $fileName = $prefix.'_admin_invoice_' . $airport.'-'. $date . '.pdf';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }

        file_put_contents($filePath. $fileName, $output);

        // $dompdf->stream("invoice.pdf", array("Attachment" => false));
        // Force download of the PDF file
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    public function operator_generate_invoice()
    {
        $data = $this->request->getVar();
        // $search = $this->request->getVar('search')['value'];

        $invoiceNo = $_GET['ginvoiceNo'] ? $_GET['ginvoiceNo'] : '';
        $airport = $_GET['gairport'] ? $_GET['gairport'] : '';
        $product = $_GET['gproduct'] ? $_GET['gproduct'] : '';

        $saleTotal = $_GET['gsaleTotal'] ? $_GET['gsaleTotal'] : 0;
        $operatorTotal = $_GET['goperatorTotal'] ? $_GET['goperatorTotal'] : 0;
        $subTotal = $_GET['gsubTotal'] ? $_GET['gsubTotal'] : 0;

        $googleCost = $_GET['ggoogleCost'] ? $_GET['ggoogleCost'] : 0;
        $googleCostRefund = $_GET['ggoogleCostRefund'] ? $_GET['ggoogleCostRefund'] : 0;
        $disputeAmount = $_GET['gdisputeAmount'] ? $_GET['gdisputeAmount'] : 0;
        $officeCost = $_GET['gofficeCost'] ? $_GET['gofficeCost'] : 0;
        $payable = $_GET['gpayable'] ? $_GET['gpayable'] : 0;

        $inputLabels = $_GET['ginputLabels'] ? $_GET['ginputLabels'] : '';
        $inputValues = $_GET['ginputValues'] ? $_GET['ginputValues'] : '';

        $supplier = $_GET['gsupplier'] ? $_GET['gsupplier'] : '';
        $isSupplier = $_GET['isSupplier'] ? $_GET['isSupplier'] : '';
        $filter_type = $_GET['filter_type'] ? $_GET['filter_type'] : '';

        $DateFrom = $_GET['gdateFrom'] ? $_GET['gdateFrom'] : '';
        $DateTo = $_GET['gdateTo'] ? $_GET['gdateTo'] : '';

        $DateFrom = date('Y-m-d', strtotime($DateFrom));
        $DateTo = date('Y-m-d', strtotime($DateTo));

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" && trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `id`= '$product' ";
        }

        $SQLsupplier = "";

        if (trim($supplier) != "" && trim($supplier) != '*') {
            $SQLsupplier = " AND parent='$supplier' ";
            if ($isSupplier) {
                $SQLsupplier = " AND (source LIKE CONCAT('$supplier', '%') OR source='$supplier') ";
            }
        }

        $SQLstatus = " AND (b.status='1' OR b.status='4') ";
        
        $SQLref = " AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%') ";// || b.reference LIKE 'GL %'
        if ($isSupplier) {
            $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GO-%'";
        }
        
        $resultp=array();
        $result='';

        $SQLFilterDate = "and date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo'";
        $OrderBy = " ORDER BY source ASC";
        $prefix = 'departure';
        

        if ($isSupplier) 
        {
            $sql_data = "SELECT  b.source as name,count(b.id) as qty, SUM(b.price) as totPrice,SUM(b.refund_amount) as totRefund FROM `tbl_booking` b WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref $SQLsupplier GROUP BY b.source";
            $sql_data .= $OrderBy;
            $resultt = $this->db->query($sql_data)->getResult();
            // pre($sql_data);
            $resultp = $this->mergeAndSumByName($resultt,'ParkVia');
            $resultp = $this->mergeAndSumByName($resultt,'CPD');
            $resultp = $this->mergeAndSumByName($resultt,'P4U');
            $resultp = $this->mergeAndSumByName($resultt,'APU');
            $resultp = $this->mergeAndSumByName($resultt,'FreeToMove');
            $resultp = $this->mergeAndSumByName($resultt,'Holiday Extras');
            $resultp = $this->mergeAndSumByName($resultt,'Park&Fly');
            $resultp = $this->mergeAndSumByName($resultt,'YTE');
            $resultp = $this->mergeAndSumByName($resultt,'HCP');
            $resultp = $this->mergeAndSumByName($resultt,'CYP');
            $resultp = $this->mergeAndSumByName($resultt,'https://longtermparking.ie/');
            $resultp = $this->mergeAndSumByName($resultt,'Go Comparison');
        }else{
            $sql_data = "SELECT  p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice,SUM(b.refund_amount) as totRefund FROM `tbl_booking` b LEFT JOIN `tbl_products` p ON b.product_id=p.id  WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref";
            $sql_data .= $OrderBy;
            $result = $this->db->query($sql_data)->getRow();
        }
        

        if(trim($product) == "*" && !$isSupplier) {

            $sql_data = "SELECT  p.id as pid,p.name, p.parent,count(b.id) as qty, SUM(b.price) as totPrice,SUM(b.refund_amount) as totRefund FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id  WHERE 1=1 $SQLFilterDate $SQLstatus $SQLref $SQLsupplier $SQLairport $SQLproduct GROUP BY p.name";
            $resultp = $this->db->query($sql_data)->getResult();
        }

        $html = '';
        $totRefund=0;
        $totQty=0;
        $amount=0;
        if (is_array($resultp)) 
        {
            foreach ($resultp as $key => $p) 
            {
                $price = 0;
                if ($p->qty > 0) 
                {
                    $totRefund += $p->totRefund;
                    $totQty += $p->qty;
                    $totQty += $p->qty;
                    $totFee = $p->qty*1.95;
                    if ($p->totPrice) 
                    {
                        $amount += $p->totPrice;
                        $price = $p->totPrice;
                    }

                    $html .='<tr>';
                    $html .= '<td>'.$p->name.'</td>';
                    $html .= '<td>'.$p->qty.'</td>';
                    $html .= '<td>'.$p->totRefund.'</td>';
                    $html .= '<td>'.$price.'</td>';
                    
                    $html .= '</tr>';
                }
                    
            }
            $html .='<tr>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px;">Total</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px;">'.$totQty.'</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px;">'.$totRefund.'</th>';
            $html .= '<th style="text-align: left;font-size: .757rem;padding: 8px;">'.$amount.'</th>';
            $html .= '</tr>';
            $html .='<tr><td colspan="4"></td></tr>';
                
        }

        $grossIncom=0; 
        if ($subTotal !=0) 
        {
            $grossIncom =round(($subTotal/100)*10,2);
        }
        if ($saleTotal !=0 && !$isSupplier) 
        {
            $grossIncom =round(($saleTotal/100)*10,2);
        }

        $data = [
            'saleTotal' => $saleTotal,
            'grossIncom' => $grossIncom,
            'operatorTotal' => $operatorTotal,
            'subTotal' => $subTotal,
            'googleCost' => $googleCost,
            'googleCostRefund' => $googleCostRefund,
            'disputeAmount' => $disputeAmount,
            'officeCost' => $officeCost,
            'totalPayable' => $payable,

            'inputLabels' => $inputLabels,
            'inputValues' => $inputValues,
        ];

        $data['invoiceNo'] = $invoiceNo;
        $data['dateFrom'] = $DateFrom;
        $data['dateTo'] = $DateTo;
        $data['result'] = $result;
        $data['html'] = $html;
        $data['isSupplier'] = $isSupplier;

        $html = view('invoices/operator_pdf', $data);

        // Setup Dompdf options
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // $dompdf->stream($airport.'-'.date('M-Y h:i:s a',strtotime($DateFrom)).'.pdf');
        // pre($html);

        // Save the generated PDF to a folder
        $output = $dompdf->output();

        $date = $DateFrom.'_'.$DateTo;
        $filePath = WRITEPATH . 'invoices/';
        $fileName = $prefix.'_operator_invoice_' . $airport.'-'. $date . '.pdf';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }

        file_put_contents($filePath. $fileName, $output);

        // $dompdf->stream("invoice.pdf", array("Attachment" => false));
        // Force download of the PDF file
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    function mergeAndSumByName(&$array, $searchKey) 
    {
        $mergedData = [];
        
        foreach ($array as $item) {
            // Check if the name starts with 'ParkVia'
            if (stripos($item->name, $searchKey) === 0) {
                if (!isset($mergedData[$searchKey])) {
                    // Initialize the merged entry
                    $mergedData[$searchKey] = (object) ['name' => $searchKey, 'qty' => 0, 'totPrice' => 0,'totRefund' => 0];
                }
                // Sum the values
                $mergedData[$searchKey]->qty += $item->qty;
                $mergedData[$searchKey]->totPrice += $item->totPrice;
                $mergedData[$searchKey]->totRefund += $item->totRefund;
            } else {
                // Keep other items unchanged
                $mergedData[] = $item;
            }
        }
        
        // Convert associative array to indexed array for final result
        $array = array_values($mergedData);
        return $array;
    }

    public function download_csv()
    {
        $ref = $_GET['ref'] ? $_GET['ref'] : '';
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $source = isset($_GET['source']) ? urldecode($_GET['source']) : '';
        $product = $_GET['product'] ? $_GET['product'] : '';
        $DateFrom = $_GET['from'] ? $_GET['from'] : '';
        $DateTo = $_GET['to'] ? $_GET['to'] : '';

        $weekDate = isset($_GET['week']) ? $_GET['week'] : '';

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" && trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `id`= '$product' ";
        }
        
        $SQLsource = "";
        if (empty($source) && isset($_GET['source'])) {
            $SQLsource = " AND (b.source IS NULL OR b.source = '')";
        }
        if (trim($source) != "" && trim($source) != "*") {
            $SQLsource = " AND (b.source='$source' OR b.source='$source-Dashboard') ";
            if (trim($source) == "CPD-P4U") { 
                $SQLsource = " AND (b.source='CPD' OR b.source='CPD-Dashboard' OR b.source='P4U' OR b.source='P4U-Dashboard') ";
            }
            // $SQLsource = " AND (b.source LIKE CONCAT('$source', '%') OR b.source='$source') ";
        }


        $SQLref = " AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%')  ";
        if ($ref =='supplier') {
            $SQLproduct = "";
            $product_code = "";
            $SQLref = " AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GO-%'";
            $SQLref .= " AND b.reference NOT LIKE 'GL %' AND b.reference NOT LIKE 'GO-%'";
        }
        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";

        if ($weekDate) 
        {
            $dates = explode("_", $weekDate);
            $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$dates[0]' AND '$dates[1]')";
            $SQLsource = "";
        }
        
        $SQLstatus = " AND (b.status='1' OR b.status='4')";

        // $sql_query = "SELECT reference, product_id, airport, depart_at, return_at, price, created_at FROM `tbl_booking`  WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLproduct $SQLref ORDER BY depart_at ASC";
        
        $sql_data = "SELECT id, name FROM `tbl_products` WHERE  1=1  $product_code";
        $resultp = $this->db->query($sql_data)->getRow();

        if ($resultp && $product_code) 
        {
            $sql_query = "SELECT  p.name, b.airport,b.reference, b.source, b.product_id , b.depart_at, b.return_at, b.price, b.refund_amount, b.google_cost , b.show_status, b.created_at, bc.late_charges, bc.status as bcstatus FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id 
                LEFT JOIN (
                    SELECT *
                    FROM tbl_booking_collect c
                    WHERE c.id = (
                        SELECT MIN(id) 
                        FROM tbl_booking_collect 
                        WHERE booking_id = c.booking_id
                    )
                    AND (c.status = 'collected' OR c.status = 'returned')
                ) bc ON b.id = bc.booking_id  
                WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLsource AND p.name='$resultp->name' ORDER BY b.depart_at ASC";
        }else{
            $sql_query = "SELECT  p.name, b.airport,b.reference, b.source, b.product_id , b.depart_at, b.return_at, b.price, b.refund_amount, b.google_cost, b.show_status, b.created_at, bc.late_charges, bc.status as bcstatus FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id 
                LEFT JOIN (
                    SELECT *
                    FROM tbl_booking_collect c
                    WHERE c.id = (
                        SELECT MIN(id) 
                        FROM tbl_booking_collect 
                        WHERE booking_id = c.booking_id ORDER BY id desc
                    )
                    AND (c.status = 'collected' OR c.status = 'returned')
                ) bc ON b.id = bc.booking_id  
                WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLsource ORDER BY b.depart_at ASC";
        }

        // $sql_query = "SELECT  p.name, b.airport,b.reference, b.source, b.product_id , b.depart_at, b.return_at, b.price, b.created_at FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id  WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLsource AND p.name='$resultp->name' ORDER BY b.depart_at ASC";
        

        $bookings = $this->db->query($sql_query)->getResult();
        // pre($bookings);
        $date = date('dmY').'-'.count($bookings);
        $filePath = WRITEPATH . 'invoices/';
        $fileName = $ref.'_departure_'.$airport.'-'.$date.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV ,'Late Charges'
        fputcsv($file, ['Reference', 'Source', 'Airport', 'Product', 'Depart At', 'Return At', 'Price', 'Refund Amount', 'Google Cost ', 'Google Refund Cost' ,'NoShow Status', 'Mark Collect', 'Created At']);

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
                $booking->name,
                $booking->depart_at,
                $booking->return_at,
                $booking->price,
                $booking->refund_amount,
                $booking->google_cost,
                $gcost,
                // $booking->late_charges,
                $booking->show_status,
                $booking->bcstatus,
                $booking->created_at,
            ]);
        }

        fclose($file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    public function get_airport_products()
    {
        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $operator = $_GET['operator'] ? $_GET['operator'] : '';
        $sql_data = "select id,name from tbl_products WHERE `parent` = '$airport'";
        if ($operator) {
            $sql_data = "select id,name from tbl_products WHERE `parent` = '$airport' AND `operator_id` = '$operator'";
        }
        
        $result = $this->db->query($sql_data)->getResult();

        echo'<option value="*">All</option>';
        foreach ($result as $key => $r) {
            echo '<option value="'.$r->id.'">'.$r->name.'</option>';
        }
    }

    public function get_acairport_websites()
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
    }   
}