<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Security\Security;

use ValueError;

class supplier extends BaseController
{
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    public function __construct()
    {
        $this->Supplier = new SupplierModel;
        $this->Roles = new RolesModel;

    }


    public function index()
    {
       
        $data = [
            "page_title" => "Supplier",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('Supplier'), "title" => "Supplier", "status" => "", "link" => false]]
        ];
        return view('supplier/view', $data);
    }



    public function save()
    {
        $validate = $this->validate(
            [
                'name' => 'required|min_length[2]',
                'code' => 'required|min_length[1]',
                'status' => 'required|in_list[1,0]'
            ],
            [
                'name' => [
                    'required' => 'Please enter name',
                    'min_length' => 'name must be 1 char long'
                ],
                'code' => [
                    'required' => 'Please enter code',
                    'min_length' => 'code must be 1 char long'
                ],
                'status' => [
                    'required' => 'Please select status',
                    'in_list' => 'Invalid status selection'
                ]
            ]
        );
        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            $name = $this->request->getPost('name');
            $code = $this->request->getPost('code');
            $status = $this->request->getPost('status');
            $token = $this->generateToken(42);
            $data = [
                'name' => $name,
                'code' => $code,
                'status' => $status,
                'access_token' => $token
            ];
            $result = $this->Supplier->insert($data);
            if ($result) {
                $result = ['status' => true, "message" => "Record successfully added", 'errors' => null];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on add user action", 'errors' => null];
            }
        }
        return $this->response->setJSON($result);
    }


    public function generateToken($length)
    {
        $randomBytes = random_bytes($length);

        // Encode in base64
        $base64Token = base64_encode($randomBytes);

        // Remove non-alphanumeric characters
        $cleanToken = preg_replace('/[^A-Za-z0-9]/', '', $base64Token);

        // Truncate to desired length
        $finalToken = substr($cleanToken, 0, $length);

        return $finalToken;
    }


    public function get()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'name',
            2 => 'code',
            3 => 'access_token',
            4 => 'status'
        ];
        $sql_count = "SELECT count(*) as total FROM tbl_supplier WHERE  1=1 ";
        $sql_data = "SELECT `id`, `name`, `code`, `access_token`, `created_at`,`status` FROM `tbl_supplier` WHERE  1=1 ";
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
        $OrderBy = " ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = " " . $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();

   
        $data = array();
        foreach ($result as $value) {
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));
            // $row[] = $created_at;           
            
            $comma_separated="";

       
            $sql_data = "SELECT `product_code` FROM `tbl_products` WHERE parent='$value->code' ";
            $result_code = $this->db->query($sql_data)->getResult();
            if($result_code){

                foreach($result_code as $result_code_r)
                {

                    $val[]=$result_code_r->product_code;
                }
                $comma_separated = implode(",", $val);
            }


            $row[] = $value->name;
            $row[] = $value->code;
            $row[] = $value->access_token;
            $row[] = $comma_separated;

            unset($val);
            $badge = "";
            if ($value->status == "1") {
                $badge = "badge badge-glow bg-success";
                $status = "ACtive";
            } else if ($value->status == "0") {
                $badge = "badge badge-glow bg-danger";
                $status = "inactive";

            } else if ($value->status == "active") {
                $badge = "badge badge-glow bg-warning";
            }
            $row[] = "<span class='$badge'>" . ucfirst($status) . "</span>";
            $id = id_en($value->id);
            $action = "<div class=\"btn-group\">
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
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }


    public function get_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $data = $this->Supplier->where('id', $id)->first();
        if (sizeof($data) > 0) {
            $result = ['status' => true, "data" => $data];
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }



    public function update()
    {
        $data = $this->request->getVar();
        $id = id_de($data['id']);
        $data['id'] = $id;
        $res = $this->Supplier->where('id', $id)->first();
        $validate = $this->validate(
            [
                'name' => 'required|min_length[2]',
                'code' => 'required|min_length[1]',
                'status' => 'required|in_list[1,0]'
            ],
            [
                'name' => [
                    'required' => 'Please enter name',
                    'min_length' => 'name must be 2 char long'
                ],
                'code' => [
                    'required' => 'Please enter code',
                    'min_length' => 'code must be 1 char long'
                ],
                'status' => [
                    'required' => 'Please select status',
                    'in_list' => 'Invalid status selection'
                ]
            ]
        );
        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            $dataX = [
                'name' => $data['name'],
                'code' => $data['code'],
                'status' => $data['status'],
            ];
            $result = $this->Supplier->update($data['id'], $dataX);
            if ($result) {
                $result = ['status' => true, "message" => "Record successfully updated", 'errors' => null];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on update record", 'errors' => null];
            }
        }
        return $this->response->setJSON($result);
    }


    public function delete_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $data = $this->Supplier->where('id', $id)->first();
        if (sizeof($data) > 0) {
            $response = $this->Supplier->delete($id);
            if ($response) {
                $result = ['status' => true, "message" => "Recording successfully deleted"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on delete record"];
            }

        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    // Invoices
    public function get_admin_invoice()
    {
        $data = $this->request->getVar();
        $status = 1;

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $product = $_GET['product'] ? $_GET['product'] : '';
        $supplier = $_GET['supplier'] ? $_GET['supplier'] : '';

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
            $product_code = " AND   b.id= '$product' ";
        }

        $SQLsupplier = "";

        if (trim($supplier) != "" && $supplier != "*") {
            $SQLsupplier = " AND (b.source LIKE CONCAT('$supplier', '%') OR b.source='$supplier') ";
        }

        $SQLstatus = " AND b.status='$status' ";

        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GL %' AND b.reference NOT LIKE 'GO-%' AND b.reference NOT LIKE 'ps_%'";

        $SQLFilterDate = "AND (date(b.depart_at) BETWEEN '$DateFrom' AND '$DateTo')";

        $sql_data = "SELECT  b.source,count(b.id) as qty, SUM(b.price) as totPrice, count(bc.id) as collected,SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_booking` b
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
        // pre($result);
        $data='';
        $totQty =0;
        $totCollected =0;
        $totNoShow =0;
        $totFee =0;
        $totNetAmount =0;
        $amount =0;
        $totNetAmountC =0;
        $amountC =0;
        $saleTotal =0;

        if (is_array($result)) 
        {
            // pre($result);
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
            // pre($result2);
            $resultt=get_cpd_p4u($result2);
            // pre($resultt);
            foreach ($resultt as $key => $p) 
            {
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
                        $amount +=$p->totPrice;
                        $totFee += $fee;
                        $price = $p->totPrice;
                        $netAmount = $price - ($p->totPrice/100)*30;
                        $totNetAmount += $price - ($p->totPrice/100)*30;
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
                    $data .= '<td>'.$p->source.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->collected.'</td>';
                    $data .= '<td>'.$p->noShow.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td>'.$netAmount.'</td>';
                    $data .= '<td>'.$priceC.'</td>';
                    $data .= '<td>'.$netAmountC.'</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=supplier&product=&source='.urlencode($p->source)).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
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
            // $data .= '<th>0</th>';
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
        $saleTotal = $amount;
        $operatorTotal = ($saleTotal/100)* 30; 
        $subTotal = $saleTotal - $operatorTotal;

        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('M-Y',strtotime($DateFrom)),

            'totNetAmount' => round($totNetAmount,2),
            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'operatorTotal' => round($operatorTotal,2),
            'subTotal' => round($subTotal,2),
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

    public function get_admin_airport_invoice()
    {
        $data = $this->request->getVar();
        $status = 1;

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $product = $_GET['product'] ? $_GET['product'] : '';
        $supplier = $_GET['supplier'] ? $_GET['supplier'] : '';

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
            $product_code = " AND   b.id= '$product' ";
        }

        $SQLsupplier = "";

        if (trim($supplier) != "" && $supplier != "*") {
            $SQLsupplier = " AND (b.source LIKE CONCAT('$supplier', '%') OR b.source='$supplier') ";
        }

        $SQLstatus = " AND b.status='$status' ";

        $SQLref = " AND (b.reference IS NOT NULL AND b.reference !='') AND b.reference NOT LIKE 'GL-%' AND b.reference NOT LIKE 'GL %' AND b.reference NOT LIKE 'GO-%'";

        $SQLFilterDate = "AND (date(depart_at) BETWEEN '$DateFrom' AND '$DateTo')";

        $sql_data = "SELECT  b.airport,count(b.id) as qty, SUM(b.price) as totPrice, count(bc.id) as collected, SUM(CASE WHEN bc.id IS NOT NULL THEN b.price ELSE 0 END) AS collectedAmount, SUM(CASE WHEN b.show_status=0 AND bc.id IS NULL THEN 1 ELSE 0 END) AS noShow FROM `tbl_booking` b 
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
    
        $OrderBy = " ORDER BY b.airport ASC";
        $GroupBy = " GROUP BY b.airport";
        
        $sql_data .= $GroupBy. $OrderBy;
        $result = $this->db->query($sql_data)->getResult();
        // pre($resultp);
        $data='';
        $totQty =0;
        $totCollected =0;
        $totNoShow =0;
        $totFee =0;
        $totNetAmount =0;
        $amount =0;
        $totNetAmountC =0;
        $amountC =0;
        $saleTotal =0;

        if (is_array($result)) 
        {
            // pre($resultp);
            // $result2 = $this->mergeAndSumByName($result,'ParkVia');
            // $result2 = $this->mergeAndSumByName($result,'CPD');
            // $result2 = $this->mergeAndSumByName($result,'P4U');
            // $result2 = $this->mergeAndSumByName($result,'APU');
            // $result2 = $this->mergeAndSumByName($result,'FreeToMove');
            // $result2 = $this->mergeAndSumByName($result,'Holiday Extras');

            foreach ($result as $key => $p) 
            {
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
                        $amount +=$p->totPrice;
                        $totFee += $fee;
                        $price = $p->totPrice;
                        $netAmount = $price - ($p->totPrice/100)*30;
                        $totNetAmount += $price - ($p->totPrice/100)*30;
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
                    $data .= '<td>'.$p->airport.'</td>';
                    $data .= '<td>'.$p->qty.'</td>';
                    $data .= '<td>'.$p->collected.'</td>';
                    $data .= '<td>'.$p->noShow.'</td>';
                    $data .= '<td>'.$price.'</td>';
                    $data .= '<td>'.$netAmount.'</td>';
                    $data .= '<td>'.$priceC.'</td>';
                    $data .= '<td>'.$netAmountC.'</td>';
                    // $data .= '<td>0</td>';
                    $data .= '<td><a href='.base_url('invoices/download?airport='.$airport.'&from='.$DateFrom.'&to='.$DateTo.'&ref=supplier&product=&source='.urlencode($supplier).'&airport='.$p->airport).' class="btn btn-primary waves-effect waves-float waves-light btn-sm">Download</a></td>';
                    
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
            // $data .= '<th>0</th>';
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
        $saleTotal = $amount;
        $operatorTotal = ($saleTotal/100)* 30; 
        $subTotal = $saleTotal - $operatorTotal;

        $output = [
            'invoiceNo' => '#'.$airport.'-'.date('M-Y',strtotime($DateFrom)),

            'totNetAmount' => round($totNetAmount,2),
            'totFee' => round($totFee,2),
            'saleTotal' => round($saleTotal,2),
            'operatorTotal' => round($operatorTotal,2),
            'subTotal' => round($subTotal,2),
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
                    $mergedData[$searchKey] = (object) ['source' => $searchKey, 'qty' => 0, 'totPrice' => 0, 'collected' => 0,'collectedAmount'=>0, 'noShow'=>0];
                }
                // Sum the values
                $mergedData[$searchKey]->qty += $item->qty;
                $mergedData[$searchKey]->totPrice += $item->totPrice;
                $mergedData[$searchKey]->collected += $item->collected;
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

}