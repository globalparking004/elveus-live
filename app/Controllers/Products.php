<?php

namespace App\Controllers;
use App\Models\SupplierModel;
use App\Models\OperatorsModel;
use App\Models\ProductsModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Products extends BaseController
{
    use ResponseTrait;
    protected $request;
    protected $Products;
    protected $Operators;
    public function __construct()
    {
        $this->Products = new ProductsModel;
        $this->Operators = new OperatorsModel;
        $this->supplier = new SupplierModel;

    }

    public function index()
    {
        $data = [
            "page_title" => "Products",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('products'), "title" => "Products", "status" => "", "link" => false]
            ]
        ];
        // $missings = get_missing_rnages();
        
        return view('products/view', $data);
    }


    public function add()
    {
        $operators = $this->Operators->findAll();

        $sql = "select * from tbl_websites";
        $websites = $this->db->query($sql)->getResult();

        $suppliers = $this->supplier->findAll();

        $data = [
            "page_title" => "Add Product",
            "operators" => $operators,
            'websites'=> $websites,
            'suppliers'=> $suppliers,
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('products'), "title" => "Products", "status" => "active", "link" => true],
                ["href" => base_url('products/add'), "title" => "Add Product", "status" => "", "link" => false]
            ]
        ];
        return view('products/add', $data);
    }

    public function get_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $data = $this->Operators->where('id', $id)->first();
        if (sizeof($data) > 0) {
            $result = ['status' => true, "data" => $data];
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function delete_record()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $data = $this->Products->where('id', $id)->first();
        if (sizeof($data) > 0) {
            $response = $this->Products->delete($id);
            if ($response) {
                $result = ['status' => true, "message" => "Record successfully deleted"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on delete record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function get()
    {
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];
        $condition = "";
        $table_map = [
            0 => 'created_at',
            1 => 'logo',
            2 => 'product_code',
            3 => 'name',
            4 => 'parent',
            5 => 'linked_product_code',
            6 => 'commission'
        ];
        $sql_count = "SELECT count(*) as total FROM tbl_products WHERE  1=1 ";//WHERE  1=1 
        $sql_data = "SELECT `id`, `product_code`, `name`, `parent`, `airport`, `created_at`, `logo`,`linked_product_code`,`commission` FROM `tbl_products` WHERE  1=1 ";
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
        $sql_count = $sql_count  . $condition;
        $sql_data  = $sql_data   . $condition;
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
            $row[] = $created_at;
            $row[] = '<img width="30" height="30" src="'.BASEURL.'logos/products/'.$value->logo.'">';
            $row[] = $value->product_code;
            $row[] = $value->name;
            $row[] = $value->parent;
            // $row[] = $value->airport;
            $row[] = $value->linked_product_code;
            $row[] = $value->commission;
            $badge = "";
            $id = id_en($value->id);
            $action = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item\" href=" . base_url("products/edit?id=" . urlencode($id)) . "><i data-feather=\"edit\"></i> Edit</a>

                  <a class=\"dropdown-item\" href=" . base_url("products/range?id=" . urlencode($id)) . "><i data-feather=\"list\"></i> Manage Range</a>
                 
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
                  <a class=\"dropdown-item\" href=" . base_url("products/duplicate?id=" . urlencode($id)) . "><i data-feather=\"repeat\"></i> Duplicate</a>

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

    public function edit()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $product = $this->Products->where('id', $id)->first();
        $operators = $this->Operators->findAll();

        $suppliers = $this->supplier->findAll();


        $sql = "select * from tbl_websites";
        $websites = $this->db->query($sql)->getResult();

        $sql = "select * from tbl_product_addons WHERE product_id= '$id'";
        $addons = $this->db->query($sql)->getResult();

        $data = [
            "page_title" => "Edit Product",
            "product" => $product,
            "websites"=> $websites,
            "operators" => $operators,
            "suppliers" => $suppliers,
            "addons" => $addons,

            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('products'), "title" => "Products", "status" => "active", "link" => true],
                ["href" => base_url('products/edit'), "title" => "Edit Product", "status" => "", "link" => false]
            ]
        ];
        return view('products/edit', $data);
    }

    public function duplicate()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $product = $this->Products->where('id', $id)->first();
        $data = $product;
        $data['product_code'] = $product['product_code'].'-dupli'.random_int(1, 50);
        $data['id'] = '';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = '';
        // pre($data);
        $result = $this->Products->insert($data);
        if ($result) {
            $result = ['status' => true, "message" => "Record successfully dubplicated", 'errors' => null];
        } else {
            $result = ['status' => false, "message" => "Unexpected error on dubplication action", 'errors' => null];
        }
        return redirect()->to('/products');
    }

    public function get_assign_value()
    {
        $id = $_GET['id'];

        $airports = get_airports();

        $product = $this->Products->where('id', $id)->first();

        foreach ($airports as $code => $name) {
          if ($code == $product['parent']) {
            echo "<option selected value='$code'>$name</option>";
          } else {
            echo "<option value='$code'>$name</option>";
          }
        }
    }

    public function save()
    {
        $data = $this->request->getVar();
        // pre($data);

        $logo = $this->request->getFile('logo');
        $logo1 = $this->request->getFile('logo1');
        $validate = $this->validate(
            [
                'product_code' => 'required|min_length[2]|is_unique[tbl_products.product_code]',
                'name' => 'required|min_length[2]',
                'logo' => 'mime_in[logo,image/jpg,image/jpeg,image/png,image/gif]'

            ],
            [
                'product_code' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long',
                    'is_unique' => 'The {field} is already taken.'
                ],
                'name' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long'
                ],
                'logo' => [
                    'required' => 'Please Upload {field}',
                    'min_length' => 'Please Upload logo png or jpg'
                ]
            ]
        );

        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            $logoname='';
            $logoname = $logo->getRandomName();

            $logo->move(ROOTPATH.'logos/products/', $logoname);
            $data['logo']=$logoname;
            // pre($data);
            $logoname1='';
            $logoname1 = $logo1->getRandomName();
            ($logoname1)??$logo1->move(ROOTPATH.'logos/products/', $logoname1);
            $data['logo1']=$logoname1;

            unset($data['csrf_test_name']);
            if (isset($data['is_none_amendable'])) {
                $data['is_none_amendable'] = 1;
            }
            if (isset($data['meet_and_greet'])) {
                $data['meet_and_greet'] = 1;
            }
            if (isset($data['on_airport'])) { 
                $data['on_airport'] = 1;
            }
            if (isset($data['park_mark'])) { 
                $data['park_mark'] = 1;
            }
            $addon_name[]='';
            if (isset($data['addon_name'])) {
                $addon_name = $data['addon_name'];
                $addon_price = $data['addon_price'];
                $addon_desc = $data['addon_desc'];
                unset($data['addon_name']);
                unset($data['addon_price']);
                unset($data['addon_desc']); 
            }
            $useful_information2='';
            $parking_facility_contact2='';
            $what_to_do_when_you_arrive2='';
            $what_to_do_when_you_return2='';
            $security_information2='';
            if (isset($data['useful_information2']))
            {
                $useful_information2=$data['useful_information2'];
                $parking_facility_contact2=$data['parking_facility_contact2'];
                $what_to_do_when_you_arrive2=$data['what_to_do_when_you_arrive2'];
                $what_to_do_when_you_return2=$data['what_to_do_when_you_return2'];
                $security_information2=$data['security_information2'];

                unset($data['useful_information2']);
                unset($data['parking_facility_contact2']);
                unset($data['what_to_do_when_you_arrive2']);
                unset($data['what_to_do_when_you_return2']);
                unset($data['security_information2']);
            }
            $result = $this->Products->insert($data);
            if ($result) {
                if ($addon_name):
                    foreach ($addon_name as $key => $n) {
                        if ($n):
                            $sql_query = "INSERT INTO `tbl_product_addons`(`product_id`, `addon_name`, `addon_price`, `addon_desc`) VALUES ('$result','$n','$addon_price[$key]','$addon_desc[$key]')";
                            $this->db->query($sql_query);
                        endif;
                    }
                endif;
                if (isset($useful_information2))
                {
                    $sql_query = "INSERT INTO `tbl_product_email_config`(`product_id`, `useful_information`, `parking_facility_contact`, `what_to_do_when_you_arrive`, `what_to_do_when_you_return`, `security_information`) VALUES ('$result','$useful_information2','$parking_facility_contact2','$what_to_do_when_you_arrive2','$what_to_do_when_you_return2','$security_information2')";
                    $this->db->query($sql_query);
                }
                $result = ['status' => true, "message" => "Record successfully added", 'errors' => null];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on add user action", 'errors' => null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function update()
    {
        $data = $this->request->getVar();
        unset($data['logo']);
        $logo = $this->request->getFile('logo');
        $logo1 = $this->request->getFile('logo1');
        $validate = $this->validate(
            [
                'product_code' => 'required|min_length[2]|is_unique[tbl_products.product_code,id,' . $data['id'] . ']',
                'name' => 'required|min_length[2]',
                'logo' => 'mime_in[logo,image/jpg,image/jpeg,image/png,image/gif]'
            ],
            [
                'product_code' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long'
                ],
                'name' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long'
                ]
            ]
        );
        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            if($logo!=''){
                $logoname='';
                $logoname = $logo->getRandomName();
                $logo->move(ROOTPATH.'logos/products/', $logoname);
                $data['logo']=$logoname;
            }
            if($logo1!=''){
                $logoname1='';
                $logoname1 = $logo1->getRandomName();
                $logo1->move(ROOTPATH.'logos/products/', $logoname1);
                $data['logo1']=$logoname1;
            }
            unset($data['csrf_test_name']);
            if (isset($data['is_none_amendable'])) {
                $data['is_none_amendable'] = 1;
            } else {
                $data['is_none_amendable'] = 0;
            }
            if (isset($data['meet_and_greet'])) {
                $data['meet_and_greet'] = 1;
            } else {
                $data['meet_and_greet'] = 0;
            }
            if (isset($data['on_airport'])) {
                $data['on_airport'] = 1;
            } else {
                $data['on_airport'] = 0;
            }
            if (isset($data['park_mark'])) {
                $data['park_mark'] = 1;
            } else {
                $data['park_mark'] = 0;
            }

            $product = $this->Products->where('id', $data['id'])->first();
            if (sizeof($product) > 0) {
                // pre($data);
                if (isset($data['useful_information2']))
                {
                    $product_id = $data['id'];
                    $useful_information2=$data['useful_information2'];
                    $parking_facility_contact2=$data['parking_facility_contact2'];
                    $what_to_do_when_you_arrive2=$data['what_to_do_when_you_arrive2'];
                    $what_to_do_when_you_return2=$data['what_to_do_when_you_return2'];
                    $security_information2=$data['security_information2'];
                    
                    $sql="SELECT * FROM `tbl_product_email_config` WHERE product_id='$product_id'";
                    $pemail= $this->db->query($sql)->result();

                    $sql_query = "INSERT INTO `tbl_product_email_config`(`product_id`, `useful_information`, `parking_facility_contact`, `what_to_do_when_you_arrive`, `what_to_do_when_you_return`, `security_information`) VALUES ('$product_id','$useful_information2','$parking_facility_contact2','$what_to_do_when_you_arrive2','$what_to_do_when_you_return','$security_information2')";
                    if ($pemail) {
                        $sql_query="UPDATE `tbl_product_email_config` SET `useful_information`='$useful_information2',`parking_facility_contact`='$parking_facility_contact2',`what_to_do_when_you_arrive`='$what_to_do_when_you_arrive',`what_to_do_when_you_return`='$what_to_do_when_you_return',`security_information`='$security_information' WHERE product_id='$product_id'";
                    }
                    $this->db->query($sql_query);
                    unset($data['useful_information2']);
                    unset($data['parking_facility_contact2']);
                    unset($data['what_to_do_when_you_arrive2']);
                    unset($data['what_to_do_when_you_return2']);
                    unset($data['security_information2']);
                }

                $result = $this->Products->update($data['id'], $data);
               
                
                if ($result) {
                    $result = ['status' => true, "message" => "Record successfully updateed", 'errors' => null];
                } else {
                    $result = ['status' => false, "message" => "Unexpected error on add user action", 'errors' => null];
                }
            } else {
                $result = ['status' => false, "message" => "product not existing into system", 'errors' => null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function range()
    {
        $dataX = $this->request->getVar();

        if (isset($dataX['id'])) {
            $product_id = id_de($dataX['id']);
        } else {
            $product_id = "";
        }
        $product = $this->Products->where('id', $product_id)->first();
        $product_name = ($product)? $product['name'].' - '.$product['parent']:'';

        $sql = "SELECT `id`, `product_id`, `name`, `daily_rate`, `day_rate` FROM `tbl_product_band_master` WHERE product_id='$product_id'";
        $bands = $this->db->query($sql)->getResult();

        $sql_data = "SELECT `id`,`product_id`,`dfrom`, `dto` FROM `tbl_ranges` WHERE product_id='$product_id'";
        $result = $this->db->query($sql_data)->getResult();

        $missingRanges = get_missing_rnages($result);
       
        $missingRanges = ($missingRanges)? implode(', ', $missingRanges):$missingRanges;
        // pre($missingRanges);
        $data = [
            "page_title" => "Manage Range",
            "product_id" => $product_id,
            "bands" => $bands,
            "missingRanges" => $missingRanges,
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('products'), "title" => "Products", "status" => "", "link" => true],
                ["href" => base_url('products'), "title" => $product_name, "status" => "", "link" => false],
                ["href" => base_url('products'), "title" => "Manage Range", "status" => "", "link" => false],
            ]
        ];
        return view('products/range', $data);
    }

    public function get_rate_cards()
    {
        $data = $this->request->getVar();
        $product_id = $data['product_id'];
        $search = $this->request->getVar('search')['value'];
        $condition = "";
        $table_map = [
            0 => 'name',
            1 => 'daily_rate',
            2 => 'day_rate'
        ];
        $sql_count = "SELECT count(*) as total FROM tbl_product_band_master WHERE product_id='$product_id'";
        $sql_data = "SELECT `id`, `product_id`, `name`, `daily_rate`, `day_rate` FROM `tbl_product_band_master` WHERE product_id='$product_id'";
        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'name') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $sql_count = $sql_count  . $condition;
        $sql_data  = $sql_data   . $condition;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = " " . $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) {
            $row = array();
            $row[] = $value->name;
            $row[] = $value->daily_rate;
            $row[] = $value->day_rate;
            $id = id_en($value->id);
            $product_id = $value->product_id;
            $action = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
     
                  <a class=\"dropdown-item\" onclick=\"show_bands(`$id`)\" href=\"javascript:void(0);\"><i data-feather='edit'></i> Edit</a>
              
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>

                  <a class=\"dropdown-item\" href=\"/products/download_band?id=$value->id\"><i data-feather=\"download\"></i> Download</a>
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

    public function add_band()
    {
        $data = $this->request->getPost();

        $validate = $this->validate(
            [
                'band_name' => 'required|min_length[2]',
                'band_daily_rate' => 'required|numeric',
                'band_day_rate' => 'required|numeric',
            ],
            [
                'band_name' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long'
                ],
                'band_daily_rate' => [
                    'required' => 'Please enter {field}',
                    'numeric' => '{field} invalid number',
                ],
                'band_day_rate' => [
                    'required' => 'Please enter {field}',
                    'numeric' => '{field} invalid number',
                ]
            ]
        );

        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            $product_id = $data['product_id'];
            $band_name = $data['band_name'];
            $band_daily_rate = $data['band_daily_rate'];
            $band_day_rate = $data['band_day_rate'];
            $band_id = $data['band_id'];
            $sql = "SELECT * FROM tbl_product_band_master WHERE product_id='$product_id' and id='$band_id' LIMIT 1";
            $result = $this->db->query($sql)->getResult();
            if (sizeof($result) > 0) {
                $sql = "UPDATE tbl_product_band_master SET name='$band_name',daily_rate='$band_daily_rate',day_rate='$band_day_rate' WHERE product_id='$product_id' and id='$band_id'";
                $result = $this->db->query($sql);
                $master_id = $band_id;
            } else {
                $sql = "INSERT INTO `tbl_product_band_master`( `product_id`, `name`, `daily_rate`, `day_rate`) VALUES ('$product_id','$band_name','$band_daily_rate','$band_day_rate')";
                $result = $this->db->query($sql);
                $master_id = $this->db->insertID();
            }


            $name = $data['name'];
            $daily_rate = $data['daily_rate'];
            $day_rate = $data['day_rate'];
            $sql = "DELETE FROM `tbl_product_band` WHERE `master_id`='$master_id'";
            $result = $this->db->query($sql);
            for ($i = 0; $i < sizeof($name); $i++) {
                $sql = "INSERT INTO `tbl_product_band`(`master_id`, `name`, `daily_rate`, `day_rate`) VALUES ('$master_id','$name[$i]','$daily_rate[$i]','$day_rate[$i]')";
                $result = $this->db->query($sql);
            }
            $result = ['status' => true, "message" => "product band successfully saved"];
        }
        return $this->response->setJSON($result);
    }

    public function get_band()
    {
        $data = $this->request->getVar();

        $product_id = $data['id'];
        $product_id = id_de($product_id);
        $sql = "SELECT * FROM tbl_product_band_master WHERE id='$product_id' LIMIT 1";
        $result = $this->db->query($sql)->getResult();
        $master = $result;
        $status = false;
        if (sizeof($master) > 0) {
            $sql = "SELECT * FROM tbl_product_band WHERE master_id='" . $result[0]->id . "' order by name";
            $result = $this->db->query($sql)->getResult();
            $status = true;
        } else {
            $result = [];
        }
        $detials = $result;
        $data = ['status' => $status, 'master' => $master, "detials" => $detials];
        return $this->response->setJSON($data);
    }

    public function import_band()
    {
        $data = $this->request->getPost();

        $validate = $this->validate(
            [
                'band_name' => 'required|min_length[2]',
                'band_daily_rate' => 'required|numeric',
                'band_day_rate' => 'required|numeric',
            ],
            [
                'band_name' => [
                    'required' => 'Please enter {field}',
                    'min_length' => '{field} must be 2 char long'
                ],
                'band_daily_rate' => [
                    'required' => 'Please enter {field}',
                    'numeric' => '{field} invalid number',
                ],
                'band_day_rate' => [
                    'required' => 'Please enter {field}',
                    'numeric' => '{field} invalid number',
                ]
            ]
        );

        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else 
        {
            $file = $this->request->getFile('excel_file');
            
            $allowedMimeTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if (in_array($file->getMimeType(), $allowedMimeTypes)) 
            {
                if ($file && $file->isValid() && !$file->hasMoved()) 
                {
                    $product_id = $data['product_id'];
                    $band_name = $data['band_name'];
                    $band_daily_rate = $data['band_daily_rate'];
                    $band_day_rate = $data['band_day_rate'];

                    // Insert data into database
                    $sql = "SELECT * FROM tbl_product_band_master WHERE product_id='$product_id' and name='$band_name' LIMIT 1";
                    $resp = $this->db->query($sql)->getRow();
                    if ($resp) {
                        $sql = "UPDATE tbl_product_band_master SET name='$band_name',daily_rate='$band_daily_rate',day_rate='$band_day_rate' WHERE product_id='$product_id' and id='$resp->id'";
                        $res = $this->db->query($sql);
                        $master_id = $resp->id;
                    } else {
                        $sql = "INSERT INTO `tbl_product_band_master`( `product_id`, `name`, `daily_rate`, `day_rate`) VALUES ('$product_id','$band_name','$band_daily_rate','$band_day_rate')";
                        $res = $this->db->query($sql);
                        $master_id = $this->db->insertID();
                    }
                    $sql = "DELETE FROM `tbl_product_band` WHERE `master_id`='$master_id'";
                    $res = $this->db->query($sql);

                    $filePath = $file->getTempName();
                    
                    // Load spreadsheet
                    $spreadsheet = IOFactory::load($filePath);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray();
                    foreach ($sheetData as $key => $row) {
                        
                        if ($key > 0 && $master_id) {
                          
                            if ($row[0] <= 30) {
                                $sql = "INSERT INTO `tbl_product_band`(`master_id`, `name`, `daily_rate`, `day_rate`) VALUES ('$master_id','$row[0]','$row[1]','$row[2]')";
                            }elseif ($row[0] > 30) {
                                 $sql = "SELECT * FROM tbl_product_band WHERE master_id='$master_id' ORDER BY name DESC";
                                $resp = $this->db->query($sql)->getRow();
                                $daily_rate = $resp->day_rate + $band_daily_rate;
                                $day_rate = $resp->day_rate + $band_daily_rate;
                                $sql = "INSERT INTO `tbl_product_band`(`master_id`, `name`, `daily_rate`, `day_rate`) VALUES ('$master_id','$row[0]','$daily_rate','$day_rate')";
                            }
                            
                            $res= $this->db->query($sql);
                        }
                    }
                    $result = ['status' => true, "message" => "product band successfully saved"];
                }else{
                    $result = ["status" => false, "message" => 'Failed to upload file.'];
                }
            }else{
                $result = ["status" => false, "message" => 'Invalid file type. Only CSV and Excel files are allowed.'];
            }
           
        }
        return $this->response->setJSON($result);
    }

    public function download_band()
    {
        // $data = $this->request->getVar();

        $product_id = $_GET['id'];
        // $product_id = id_de($product_id);
        $sql = "SELECT * FROM tbl_product_band_master WHERE id='$product_id' LIMIT 1";
        $result = $this->db->query($sql)->getResult();
        $master = $result;
        $status = false;
        if (sizeof($master) > 0) {
            $sql = "SELECT * FROM tbl_product_band WHERE master_id='" . $result[0]->id . "' order by name";
            $result = $this->db->query($sql)->getResult();
            $status = true;
        } else {
            $result = [];
        }
        $detials = $result;
        // $data = ['status' => $status, 'master' => $master, "detials" => $detials];
        // return $this->response->setJSON($data);
        // pre($master);

        $date = date('Y-m-d').'-'.$master[0]->name;
        $filePath = WRITEPATH . 'bands/';
        $fileName = 'band_'.$master[0]->name.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV
        fputcsv($file, ['Day', 'Rate', 'Change To']);

        // Add rows to the CSV file
        foreach ($detials as $r) {
            
            fputcsv($file, [
                $r->name,
                $r->daily_rate,
                $r->day_rate,
            ]);
        }
        fclose($file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    public function delete_band()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $sql = "SELECT * FROM tbl_product_band_master WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql)->getResult();
        if (sizeof($result) > 0) {
            $sql = "DELETE FROM tbl_product_band_master WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $sql = "DELETE FROM tbl_product_band WHERE master_id='$id'";
                $response = $this->db->query($sql);
                if ($response) {
                    $result = ['status' => true, "message" => "Record successfully deleted"];
                } else {
                    $result = ['status' => false, "message" => "Unable to deleted detials record"];
                }
            } else {
                $result = ['status' => false, "message" => "Unable to deleted master record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function get_bands_ranges()
    {
        $data = $this->request->getVar();
        $product_id = $data['id'];

        $sql = "SELECT id,name as text FROM tbl_product_band_master WHERE product_id='$product_id' order by name";
        $rate_cards = $this->db->query($sql)->getResult();
        // $rate_cards=jsn($rate_cards,true);

        $data = ['status' => true, "message" => "its working", "rate_cards" => $rate_cards];
        return $this->response->setJSON($data);
    }

    public function add_ranges()
    {
        $data = $this->request->getPost();
        $range_id = $data['range_id'];
        $product_id = $data['range_product_id'];
        $dfrom = date("Y-m-d", strtotime($data['dfrom']));
        $dto = date("Y-m-d", strtotime($data['dto']));
        $monday = $data['monday'];
        $tuesday = $data['tuesday'];
        $wednesday = $data['wednesday'];
        $thursday = $data['thursday'];
        $friday = $data['friday'];
        $saturday = $data['saturday'];
        $sunday = $data['sunday'];

        if (strval($range_id) > 0) {
            $sql = "UPDATE tbl_ranges SET product_id='$product_id',dfrom='$dfrom',dto='$dto',monday='$monday',tuesday='$tuesday',wednesday='$wednesday',thursday='$thursday',friday='$friday',saturday='$saturday',sunday='$sunday' WHERE product_id='$product_id' AND id='$range_id'";
            $result = $this->db->query($sql);
        } else {
            $sql = "INSERT INTO `tbl_ranges`(`product_id`, `dfrom`, `dto`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`) VALUES ('$product_id','$dfrom','$dto','$monday','$tuesday','$wednesday','$thursday','$friday','$saturday','$sunday')";
            $result = $this->db->query($sql);
            $range_id = $this->db->insertID();
        }
        if ($result) {
            $data = ['status' => true, "message" => "Range successfully saved"];
        } else {
            $data = ['status' => false, "message" => "Unexpected error on range saved"];
        }
        return $this->response->setJSON($data);
    }

    public function edit_range()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $sql = "SELECT * FROM `tbl_ranges` WHERE id='$id'";
        $result = $this->db->query($sql)->getResult();
        if (sizeof($result) > 0) {
            $result = $result[0];
            $dfrom = date("m/d/Y", strtotime($result->dfrom));
            $dto = date("m/d/Y", strtotime($result->dto));
            $result->dfrom = $dfrom;
            $result->dto = $dto;
            $data = ['status' => true, 'result' => $result];
        } else {
            $data = ['status' => false, 'data' => [], "message" => "Record not found"];
        }
        return $this->response->setJSON($data);
    }

    public function get_ranges()
    {
        $data = $this->request->getVar();
        $product_id = $data['product_id'];
        $search = $this->request->getVar('search')['value'];
        $condition = "";
        $table_map = [
            0 => 'id',
            1 => 'dfrom',
            2 => 'dto'
        ];
        $sql_count = "SELECT count(*) as total FROM tbl_ranges WHERE product_id='$product_id'";
        $sql_data = "SELECT `id`,`product_id`,`dfrom`, `dto` FROM `tbl_ranges` WHERE product_id='$product_id'";
        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'id') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $sql_count = $sql_count  . $condition;
        $sql_data  = $sql_data   . $condition;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = " " . $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();

        
        // pre($missings);
        foreach ($result as $value) {
            $row = array();
            $row[] = $value->id;
            $row[] = $value->dfrom;
            $row[] = $value->dto;
            $id = $value->id;
            $product_id = $value->product_id;
            $action = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
     
                  <a class=\"dropdown-item\" onclick=\"edit_range(`$id`,`$product_id`)\" href=\"javascript:void(0);\"><i data-feather='edit'></i> Edit</a>
              
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_range(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
                </div>
              </div>";
            $row[] = $action;
            $data[] = $row;
        }
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $total_count->total,
            'recordsFiltered' => $total_count->total,
            'data' => $data,
        ];
        return $this->setResponseFormat('json')->respond($output);
    }


    public function delete_range()
    {
        $id = $this->request->getVar('id');
        $sql = "SELECT * FROM `tbl_ranges` WHERE id='$id'";
        $data = $this->db->query($sql)->getResult();
        if (sizeof($data) > 0) {
            $sql = "DELETE FROM `tbl_ranges` WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Record successfully deleted"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on delete record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }


    public function get_close_outs()
    {
        $types = ['0' => "NONE", "1" => "No Arrival/Departure", "2" => "Closed Out"];
        $data = $this->request->getVar();
        $product_id = $data['product_id'];
        $search = $this->request->getVar('search')['value'];
        $condition = "";
        $table_map = [
            0 => 'close_out_type_id',
            1 => 'close_out_from',
            2 => 'close_out_to'
        ];
        $sql_count = "SELECT count(*) as total FROM tbl_close_outs WHERE product_id='$product_id'";
        $sql_data = "SELECT `id`, `product_id`, `close_out_type_id`, `close_out_from`, `close_out_to` FROM `tbl_close_outs` WHERE product_id='$product_id'";
        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'close_out_type_id') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
        }
        $sql_count = $sql_count  . $condition;
        $sql_data  = $sql_data   . $condition;
        $total_count = $this->db->query($sql_count)->getRow();
        $OrderBy = " ORDER BY " . $table_map[$this->request->getVar('order')[0]['column']];
        $SortBy = " " . $this->request->getVar('order')[0]['dir'];
        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        $sql_data .= $OrderBy . $SortBy . $Limit;
        $result = $this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) {
            $row = array();
            $close_id = ($value->close_out_type_id)? $types[$value->close_out_type_id]:'';
            $row[] = $value->close_out_type_id . "-" . $close_id;
            $row[] = date("m/d/Y", strtotime($value->close_out_from));
            $row[] = date("m/d/Y", strtotime($value->close_out_to));
            $id = $value->id;
            $product_id = $value->product_id;
            $action = "<div class=\"btn-group\">
                <a href=\"javascript:void(0);\" class=\"btn btn-outline-primary btn-sm waves-effect dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                Actions
                </a>
                <div class=\"dropdown-menu\">
     
                  <a class=\"dropdown-item\" onclick=\"edit_close_out(`$id`)\" href=\"javascript:void(0);\"><i data-feather='edit'></i> Edit</a>
              
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_close_out(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
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

    public function add_close_out()
    {
        $data = $this->request->getPost();
        $validate = $this->validate(
            [
                'close_out_type_id' => 'required|numeric',
                'close_out_from' => 'required',
                'close_out_to' => 'required'
            ],
            [
                'close_out_type_id' => [
                    'required' => 'Please enter {field}'
                ],
                'close_out_from' => [
                    'required' => 'Please enter {field}'
                ],
                'close_out_to' => [
                    'required' => 'Please enter {field}'
                ]
            ]
        );
        if (!$validate) {
            $errors = $this->validation->getErrors();
            $result = ["status" => false, "message" => '', "errors" => $errors];
        } else {
            $product_id = $data['close_out_product_id'];
            $close_out_type_id = $data['close_out_type_id'];
            $close_out_from = date("Y-m-d", strtotime($data['close_out_from']));
            $close_out_to = date("Y-m-d", strtotime($data['close_out_to']));
            $close_out_id = $data['close_out_id'];
            $sql = "SELECT * FROM tbl_close_outs WHERE product_id='$product_id' and id='$close_out_id' LIMIT 1";
            $result = $this->db->query($sql)->getResult();
            if (sizeof($result) > 0) {
                $sql = "UPDATE tbl_close_outs SET close_out_type_id='$close_out_type_id',close_out_from='$close_out_from',close_out_to='$close_out_to' WHERE product_id='$product_id' and id='$close_out_id'";
                $result = $this->db->query($sql);
                $master_id = $close_out_id;
            } else {
                $sql = "INSERT INTO `tbl_close_outs`( `product_id`, `close_out_type_id`, `close_out_from`, `close_out_to`) VALUES ('$product_id','$close_out_type_id','$close_out_from','$close_out_to')";
                $result = $this->db->query($sql);
                $master_id = $this->db->insertID();
            }
            $result = ['status' => true, "message" => "close outs successfully saved"];
        }
        return $this->response->setJSON($result);
    }

    public function edit_close_out()
    {
        $data = $this->request->getVar();
        $id = $data['id'];
        $sql = "SELECT * FROM tbl_close_outs WHERE id='$id' LIMIT 1";
        $result = $this->db->query($sql)->getResult();
        if (sizeof($result) > 0) {
            $response = $result[0];
            $close_out_from = date("m/d/Y", strtotime($response->close_out_from));
            $close_out_to = date("m/d/Y", strtotime($response->close_out_to));
            $response->close_out_from = $close_out_from;
            $response->close_out_to = $close_out_to;
            $response = ['status' => true, "result" => $response];
        } else {
            $response = ['status' => false, "message" => "Record not found"];
        }
        return $this->response->setJSON($response);
    }

    public function delete_close_out()
    {
        $id = $this->request->getVar('id');
        $sql = "SELECT * FROM tbl_close_outs WHERE id='$id' LIMIT 1";
        $data = $this->db->query($sql)->getResult();
        if (sizeof($data) > 0) {
            $sql = "DELETE FROM tbl_close_outs WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Record successfully deleted"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on delete record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }
    // Addons
    public function addon_add()
    {
        $product_id = $this->request->getVar('product_id');
        $name = $this->request->getVar('name');
        $price = $this->request->getVar('price');
        $desc = $this->request->getVar('desc');

        if ($product_id && $name && $price) {
            
            $sql = "INSERT INTO `tbl_product_addons`(`product_id`, `addon_name`, `addon_price`, `addon_desc`) VALUES ('$product_id','$name','$price','$desc')";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Addon successfully added"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on added record"];
            }
        } else {
            $result = ['status' => false, "message" => "Product Id or addon name or price is missing"];
        }
        return $this->response->setJSON($result);
    }

    public function addon_update()
    {
        $id = $this->request->getVar('id');
        $name = $this->request->getVar('name');
        $price = $this->request->getVar('price');
        $desc = $this->request->getVar('desc');

        $sql = "SELECT * FROM tbl_product_addons WHERE id='$id' LIMIT 1";
        $data = $this->db->query($sql)->getResult();
        if (sizeof($data) > 0) {
            
            $sql = "UPDATE `tbl_product_addons` SET `addon_name`='$name',`addon_price`='$price',`addon_desc`='$desc' WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Addon successfully updated"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on update record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function addon_status()
    {
        $id = $this->request->getVar('id');
        $status = $this->request->getVar('status');
        $sql = "SELECT * FROM tbl_product_addons WHERE id='$id' LIMIT 1";
        $data = $this->db->query($sql)->getResult();
        if (sizeof($data) > 0) {
            
            $sql = "UPDATE `tbl_product_addons` SET `addon_status`='$status' WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Status successfully updated"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on update record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function addon_delete()
    {
        $id = $this->request->getVar('id');
        $sql = "SELECT * FROM tbl_product_addons WHERE id='$id' LIMIT 1";
        $data = $this->db->query($sql)->getResult();
        if (sizeof($data) > 0) {
            $sql = "DELETE FROM tbl_product_addons WHERE id='$id'";
            $response = $this->db->query($sql);
            if ($response) {
                $result = ['status' => true, "message" => "Record successfully deleted"];
            } else {
                $result = ['status' => false, "message" => "Unexpected error on delete record"];
            }
        } else {
            $result = ['status' => false, "message" => "Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

}
