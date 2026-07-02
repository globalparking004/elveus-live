<?php
namespace App\Controllers;
use App\Models\OperatorsModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;

class Operators extends BaseController
{	
	use ResponseTrait;
    protected $request;
    protected $Operators;
	public function __construct()
    {    	
        $this->Operators = new OperatorsModel;
    }

    public function index()
    {           
        $data=[
            "page_title"=>"Operators",
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('operators'),"title"=>"Operators","status"=>"","link"=>false]]
        ];
        return view('operators/view',$data);       
    }

    public function get_record()
    {   
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Operators->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $result=['status'=>true,"data"=>$data];
        }else{
            $result=['status'=>false,"message"=>"Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function delete_record()
    {
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Operators->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $response=$this->Operators->delete($id);
            if($response)
            {
                $result=['status'=>true,"message"=>"Recording successfully deleted"];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on delete record"];
            }

        }else{
            $result=['status'=>false,"message"=>"Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function get()
    {
        $data = $this->request->getVar();
        $search=$this->request->getVar('search')['value'];
        $condition="";
        $table_map = [
            0 => 'created_at',
            1 => 'description',
            2 => 'parked',
            3 => 'capacity',
            4 => 'status'
        ];
        $sql_count="SELECT count(*) as total FROM tbl_operators WHERE  1=1 ";
        $sql_data="SELECT `id`, `description`, `parked`, `capacity`, `created_at`,`changed_at`,`status` FROM `tbl_operators` WHERE  1=1 ";
        if(!empty($search))
        {
            foreach($table_map as $key => $val)
            {
                if($table_map[$key]=='created_at')
                {
                    $condition .= " AND ( ".$val." LIKE '%".$search."%'";
                }else{
                    $condition .= " OR ".$val." LIKE '%".$search."%'";
                }
            }
            $condition .= " )";
        }
        $sql_count = $sql_count  . $condition;
        $sql_data  = $sql_data   . $condition;
        $total_count=$this->db->query($sql_count)->getRow();
        $OrderBy=" ORDER BY ".$table_map[$this->request->getVar('order')[0]['column']];
        $SortBy=" ".$this->request->getVar('order')[0]['dir'];
        $Limit=" LIMIT ".$this->request->getVar('start').",".$this->request->getVar('length');
        $sql_data.=$OrderBy.$SortBy.$Limit;
        $result=$this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) 
        {   
            $row = array();         
            $created_at = date("d-m-Y", strtotime($value->created_at));         
            $row[] = $created_at;             
            $row[] = $value->description;
            $row[] = $value->parked;
            $row[] = $value->capacity;
            $badge="";
            if($value->status=="active"){
                $badge="badge badge-glow bg-success";
            }else if($value->status=="inactive"){
                $badge="badge badge-glow bg-danger";
            }else if($value->status=="active"){
                $badge="badge badge-glow bg-warning";
            }
            $row[] = "<span class='$badge'>".ucfirst($value->status)."</span>";
            $id=id_en($value->id);
            $action="<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"edit_data(`$id`);\"><i data-feather=\"edit\"></i> Edit</a>
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
                </div>
              </div>";
            $row[] = $action;
            $data[] = $row; 
        }
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal'=>$total_count->total,
            'recordsFiltered'=>$total_count->total,
            'data'=>$data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function save()
    {
        $validate = $this->validate(
            [   
                'description'=>'required|min_length[2]',
                'capacity'=>'required|min_length[1]',
                'status'=>'required|in_list[inactive,active]'                
            ],
            [
                'description'=>[
                    'required'=>'Please enter description',
                    'min_length'=>'description must be 2 char long'
                ],
                'capacity'=>[
                    'required'=>'Please enter capacity',
                    'min_length'=>'capacity must be 1 char long'
                ],
                'status'=>[
                    'required'=>'Please select status',
                    'in_list'=>'Invalid status selection'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
                $description=$this->request->getPost('description');
                $capacity=$this->request->getPost('capacity');             
                $status=$this->request->getPost('status');
                $get_limiter_time=$this->request->getPost('get_limiter_time');

                $data=[
                    'description'=>$description,
                    'capacity'=>$capacity,
                    'status'=>$status,
                    'get_limiter_time'=>$get_limiter_time

                ];
                $result=$this->Operators->insert($data);
                if($result)
                {
                    $result=['status'=>true,"message"=>"Record successfully added",'errors'=>null];
                }else{
                    $result=['status'=>false,"message"=>"Unexpected error on add user action",'errors'=>null];
                }
        }
        return $this->response->setJSON($result);
    }


    public function update()
    {   
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        $data['id']=$id;
        $res=$this->Operators->where('id',$id)->first();
        $validate = $this->validate(
            [   
                'description'=>'required|min_length[2]',
                'capacity'=>'required|min_length[1]',
                'status'=>'required|in_list[inactive,active]'                
            ],
            [
                'description'=>[
                    'required'=>'Please enter description',
                    'min_length'=>'description must be 2 char long'
                ],
                'capacity'=>[
                    'required'=>'Please enter capacity',
                    'min_length'=>'capacity must be 1 char long'
                ],
                'status'=>[
                    'required'=>'Please select status',
                    'in_list'=>'Invalid status selection'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
                $dataX=[
                    'description'=>$data['description'],
                    'capacity'=>$data['capacity'],
                    'status'=>$data['status'],
                    'get_limiter_time'=>$data['get_limiter_time'],
                ];
                $result=$this->Operators->update($data['id'], $dataX);
                if($result)
                {
                    $result=['status'=>true,"message"=>"Record successfully updated",'errors'=>null];
                }else{
                    $result=['status'=>false,"message"=>"Unexpected error on update record",'errors'=>null];
                }
        }
        return $this->response->setJSON($result);
    }

    public function get_operator_invoice()
    {
        $data = $this->request->getVar();

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $product = $_GET['product'] ? $_GET['product'] : '';
        $supplier = $_GET['supplier'] ? $_GET['supplier'] : '';

        $googleCost = $_GET['googleCost'] ? $_GET['googleCost'] : 0;
        $googleCostRefund = $_GET['googleCostRefund'] ? $_GET['googleCostRefund'] : 0;
        $disputeAmount = $_GET['disputeAmount'] ? $_GET['disputeAmount'] : 0;
        $officeCost = $_GET['officeCost'] ? $_GET['officeCost'] : 0;
        $inputLabels = $_GET['inputLabels'] ? $_GET['inputLabels'] : '';
        $inputValues = $_GET['inputValues'] ? $_GET['inputValues'] : '';

        $DateFrom = $_GET['dateFrom'] ? $_GET['dateFrom'] : '';
        $DateTo = $_GET['dateTo'] ? $_GET['dateTo'] : '';
        $DateFrom = strtotime($DateFrom);
        $DateFrom = date('Y-m-d', $DateFrom);
        $DateTo = strtotime($DateTo);
        $DateTo = date('Y-m-d', $DateTo);

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLproduct = "";
        $product_code = "";
        if (trim($product) != "" && trim($product) != "*") {
            $SQLproduct = " AND   b.product_id= '$product'";
            $product_code = " AND   `b.id`= '$product' ";
        }

        $SQLsupplier = "";

        if (trim($supplier) != "" && trim($supplier) != "*") {
            $SQLsupplier = " AND (b.source LIKE CONCAT('$supplier', '%') OR b.source='$supplier') ";
        }

        $SQLstatus = " AND (b.status='1' OR b.status='4') ";
        
        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GL %' AND b.reference NOT LIKE 'GO-%'";

        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";
      
        $sql_data = "SELECT  b.source,count(b.id) as qty, SUM(b.price) as totPrice, SUM(b.google_cost) as totGcost, SUM(b.refund_amount) as totRefund, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_booking` b 
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
                    WHERE 1=1 $SQLFilterDate $SQLairport $SQLproduct $SQLstatus $SQLref $SQLsupplier";
        $sql_data = $sql_data;
    
        $OrderBy = " ORDER BY b.depart_at ASC";
        $GroupBy = " GROUP BY b.source";
        
        $sql_data .= $GroupBy. $OrderBy;
        $result = $this->db->query($sql_data)->getResult();
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

        // pre($sql_data);
        if (is_array($result)) 
        {
            $result2 = $this->mergeAndSumByName($result,'ParkVia');
            $result2 = $this->mergeAndSumByName($result,'CPD');
            $result2 = $this->mergeAndSumByName($result,'P4U');
            $result2 = $this->mergeAndSumByName($result,'APU');
            $result2 = $this->mergeAndSumByName($result,'CTAP');
            $result2 = $this->mergeAndSumByName($result,'FreeToMove');
            $result2 = $this->mergeAndSumByName($result,'Holiday Extras');
            $result2 = $this->mergeAndSumByName($result,'Park&Fly');
            $result2 = $this->mergeAndSumByName($result,'YTE');
            $result2 = $this->mergeAndSumByName($result,'HCP');
            $result2 = $this->mergeAndSumByName($result,'CYP');
            $result2 = $this->mergeAndSumByName($result,'https://longtermparking.ie/');
            $result2 = $this->mergeAndSumByName($result,'Go Comparison');
            $result2 = $this->mergeAndSumByName($result,'goairportparking.com');
            $result2 = $this->mergeAndSumByName($result,' ');
            $resultt=get_op_cpd_p4u($result2);
            foreach ($resultt as $key => $p) 
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
                        $amount +=$p->totPrice;
                        $totFee += $fee;
                        $price = $p->totPrice;
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
                    $data .= '<td>'.$p->source.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->collected.'</td>';
                    $data .= '<td>'.$p->noShow.'</td>';
                    $data .= '<td>'.$p->totRefund.'</td>';
                    $data .= '<td>'.$p->totGcost.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td>'.$priceC.'</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=supplier&product=&source='.urlencode($p->source)).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
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
                
        }

        $inputs = array();
        $extra_price = 0;
        if ($inputLabels) {
            foreach ($inputLabels as $key => $v) {
                $extra_price+= $inputValues[$key];
                $inputs[]= array(
                    'label' => $v,
                    'value' => $inputValues[$key]
                );
            }
        }
        // $saleTotal = $amount - $totFee;
        $saleTotal = $amount;
        $operatorTotal = ($saleTotal/100)* 30; 
        $subTotal = $saleTotal - $operatorTotal;

        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('M-Y',strtotime($DateFrom)),

            'amount' => round($amount,2),
            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'operatorTotal' => round($operatorTotal,2),
            'subTotal' => round($subTotal,2),
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

    function mergeAndSumByName(&$array, $searchKey) 
    {
        $mergedData = [];
        
        foreach ($array as $item) {
            // Check if the name starts with 'ParkVia'
            if (stripos($item->source, $searchKey) === 0) {
                if (!isset($mergedData[$searchKey])) {
                    // Initialize the merged entry
                    $mergedData[$searchKey] = (object) ['source' => $searchKey, 'qty' => 0, 'totPrice' => 0, 'collected' =>0 ,'totRefund' => 0, 'totGcost' => 0, 'collectedAmount'=>0,'noShow'=>0];
                }
                // Sum the values
                $mergedData[$searchKey]->qty += $item->qty;
                $mergedData[$searchKey]->totPrice += $item->totPrice;
                $mergedData[$searchKey]->collected += $item->collected;
                $mergedData[$searchKey]->totRefund += $item->totRefund;
                $mergedData[$searchKey]->totGcost += $item->totGcost;
                $mergedData[$searchKey]->collectedAmount += $item->collectedAmount;
                $mergedData[$searchKey]->noShow += $item->noShow;
            } else {
                // Keep other items unchanged
                $mergedData[] = $item;
            }
        }
        
        // Convert associative array to indexed array for final result
        $array = array_values($mergedData);
        return $array;
    }

    public function get_invoice_data()
    {
        $data = $this->request->getVar();

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $website = $_GET['website'] ? $_GET['website'] : '';

        $officeCost = $_GET['officeCost'] ? $_GET['officeCost'] : 0;
        $inputLabels = $_GET['inputLabels'] ? $_GET['inputLabels'] : '';
        $inputValues = $_GET['inputValues'] ? $_GET['inputValues'] : '';

        $month = $_GET['month'] ? $_GET['month'] : '';
        $days = $_GET['days'] ? $_GET['days'] : '';
        $year = $_GET['year'] ? $_GET['year'] : '';
        // $year = date('Y');

        $lastDay = date('Y-m-t',strtotime("$year-$month-01"));


        $DateFrom = date($year.'-'.$month.'-01');
        $DateTo = date($year.'-'.$month.'-d');


        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLwebsite = "";

        if (trim($website) != "" && trim($website) != "*") {
            $SQLwebsite = " AND b.source ='$website' ";
        }

        // $SQLday = " AND DAY(depart_at) % '$days' = 0 ";
        

        $SQLstatus = " AND (b.status='1' OR b.status='4') ";
        
        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND (b.reference LIKE 'GL-%' OR b.reference LIKE 'GO-%') ";

        $weeks= array();

        if ($days == 7) {
            $week1= date('Y-m-d', strtotime($DateFrom.' +1 week'));

            $date2 = date('Y-m-d', strtotime($week1.' +1 day'));
            $week2= date('Y-m-d', strtotime($date2.' +1 week'));

            $date3 = date('Y-m-d', strtotime($week2.' +1 day'));
            $week3= date('Y-m-d', strtotime($date3.' +1 week'));

            $date4 = date('Y-m-d', strtotime($week3.' +1 day'));
            $week4= date('Y-m-t',strtotime("$year-$month-01"));

            $weeks['week1'] = array('dateFrom' => $DateFrom,'dateTo' => $week1);
            $weeks['week2'] = array('dateFrom' => $date2,'dateTo' => $week2);
            $weeks['week3'] = array('dateFrom' => $date3,'dateTo' => $week3);
            $weeks['week4'] = array('dateFrom' => $date4,'dateTo' => $week4);
        }elseif ($days == 15) 
        {
            $week1= date('Y-m-d', strtotime($DateFrom.' +14 day'));

            $date2 = date('Y-m-d', strtotime($week1.' +1 day'));
            $week2= date('Y-m-t',strtotime("$year-$month-01"));

            $weeks['week1'] = array('dateFrom' => $DateFrom,'dateTo' => $week1);
            $weeks['week2'] = array('dateFrom' => $date2,'dateTo' => $week2);
        }else{
            $weeks['week1'] = array('dateFrom' => $DateFrom,'dateTo' => $lastDay);
        }
        // pre($weeks);
        $data='';
        $totRefund =0;
        $totGcost =0;
        $totGRcost =0;
        $totQty =0;
        $totCollected =0;
        $totFee =0;
        $amount =0;
        $amountC =0;
        $saleTotal =0;
        foreach ($weeks as $key => $w) 
        {
            $DateFrom = $w['dateFrom'];
            $DateTo = $w['dateTo'];
            $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";
            $sql_data = "SELECT count(b.id) as qty, SUM(b.price) as totPrice, SUM(b.refund_amount) as totRefund, SUM(b.google_cost) as totGcost, count(bc.id) as collected,SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount FROM `tbl_booking` b 
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
                WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLwebsite";
        
            // $OrderBy = " ORDER BY depart_at ASC";
            // $GroupBy = ""; 
            // $sql_data .= $GroupBy. $OrderBy;

            $result = $this->db->query($sql_data)->getResult();

            // pre($sql_data);
            if (is_array($result)) 
            {

                foreach ($result as $key => $p) 
                {
                    $price=0;
                    $priceC=0;
                    if ($p->qty > 0) 
                    {
                        $dfrom = $w['dateFrom'];
                        $dto = $w['dateTo'];
                        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$dfrom' AND '$dto')";
                        $sql_data = "SELECT count(b.id) as qty, SUM(b.refund_amount) as totRefund, SUM(b.google_cost) as totGRcost, count(bc.id) as collected,SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount FROM `tbl_booking` b 
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
                            WHERE 1=1 $SQLFilterDate $SQLairport $SQLstatus $SQLref $SQLwebsite AND b.status='4'";
                        $gresult = $this->db->query($sql_data)->getRow();
                        // pre($result);
                        $totRefund += $p->totRefund;
                        $totGcost += $p->totGcost;
                        $totGRcost += $gresult->totGRcost;
                        $totQty += $p->qty;
                        $totCollected += $p->collected;
                        $fee = $p->qty*1.95;
                        if ($p->totPrice) 
                        {
                            $amount +=$p->totPrice;
                            $totFee += $fee;
                            $price = $p->totPrice;
                        }
                        if ($p->collectedAmount) 
                        {
                            $amountC +=$p->collectedAmount;
                            $priceC = $p->collectedAmount;
                        }
                        $dateWeek =$w['dateFrom'].'_'.$w['dateTo'];
                        $data .='<tr>';
                        $data .= '<td>'.$dateWeek.'</td>';
                        $data .= '<td>'.$p->qty.'</td>';
                        $data .= '<td>'.$p->collected.'</td>';
                        $data .= '<td>'.$p->totRefund.'</td>';
                        $data .= '<td>'.$p->totGcost.'</td>';
                        $data .= '<td>'.$price.'</td>';
                        $data .= '<td>'.$priceC.'</td>';
                        $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=admin&product=&source=&week='.$dateWeek).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>'; 
                        
                        $data .= '</tr>';
                    }
                }   
            }
        }
        $data .='<tr>';
        $data .= '<th>Total</th>';
        $data .= '<th>'.$totQty.'</th>';
        $data .= '<th>'.$totCollected.'</th>';
        $data .= '<th>'.$totRefund.'</th>';
        $data .= '<th>'.$totGcost.'</th>';
        $data .= '<th>'.$amount.'</th>';
        $data .= '<th>'.$amountC.'</th>';
        $data .= '<th></th>';
        $data .= '</tr>';

        $inputs = array();
        $extra_price = 0;
        if ($inputLabels) {
            foreach ($inputLabels as $key => $v) {
                $extra_price+= $inputValues[$key];
                $inputs[]= array(
                    'label' => $v,
                    'value' => $inputValues[$key]
                );
            }
        }

        // $saleTotal = $amount - $totFee;
        $saleTotal = $amount;
        $operatorTotal = ($saleTotal/100)* 30; 
        $subTotal = $saleTotal; //operatorTotal

        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('M-Y',strtotime($DateFrom)),

            'amount' => round($amount,2),
            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'operatorTotal' => round($operatorTotal,2),
            'subTotal' => round($subTotal,2),
            'googleCost' => round($totGcost,2),
            'googleRefundCost' => round($totGRcost,2),
            'refunds' => $totRefund,
            'disputeAmount' => 0,
            'officeCost' => $officeCost,
            'extra_price' => $extra_price,

            'airport' => $airport,
            'website' => $website,
            'dateFrom' => $DateFrom,
            'dateTo' => $DateTo,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function getWeekDate($yearweek)
    {
        $year = substr($yearweek, 0, -2);
        $week = substr($yearweek, 4);

        // $year = 2024;
        // $week = 40;
        // echo $year.' Week:'.$week;die;

        // Create a DateTime object for the first day of the given week
        $date = new \DateTime();
        $date->setISODate($year, $week);

        // Get the start date of the week (Monday)
        $startOfWeek = $date->format('Y-m-d');

        // Get the end date of the week (Sunday)
        $date->modify('+6 days');
        $endOfWeek = $date->format('Y-m-d');

        // Output the result
        return $startOfWeek." - ".$endOfWeek;
    }

}