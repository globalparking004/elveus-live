<?php 
namespace App\Controllers;

use App\Models\DriverModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;


class Driver extends BaseController
{	
	use ResponseTrait;
    protected $request;
    protected $Driver;
	public function __construct()
    {    	
        $this->Driver  = new DriverModel();
    }

    public function index()
    {   
        $data=[
            "page_title"=>"Drivers",
            // 'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('bookings'),"title"=>"Booking","status"=>"active","link"=>true],
                ["href"=>base_url('drivers'),"title"=>"Drivers","status"=>"","link"=>false]]
        ];
            
        return view('drivers/view',$data);       
    }

    public function get_record()
    {   
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Driver->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $result=['status'=>true,"data"=>$data];
        }else{
            $result=['status'=>false,"message"=>"Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function delete()
    {
        
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Driver->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $response=$this->Driver->delete($id);
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
            1 => 'airport',
            2 => 'name',
            3 => 'phone',
        ];
        $sql_count="SELECT count(*) as total FROM tbl_drivers WHERE 1=1";
        $sql_data="SELECT id, airport,name, phone, created_at FROM tbl_drivers WHERE 1=1";
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

        //exit($sql_count);

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
            $row[] = $value->airport;
            $row[] = $value->name;
            $row[] = $value->phone;
            
            
            $id=id_en($value->id);
            $action="<div class=\"dropdown\">
            <button type=\"button\" class=\"btn p-0 dropdown-toggle hide-arrow\" data-bs-toggle=\"dropdown\">
                <i data-feather='more-vertical'></i>               
                </button>
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
                'airport'=>'required',
                'name'=>'required|min_length[2]',
                
            ],
            [
                'airport'=>[
                    'required'=>'Please select Airport',
                ],
                'name'=>[
                    'required'=>'Please enter name',
                    'min_length'=>'Name must be 2 char long'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
            $airport=$this->request->getPost('airport');
            $name=$this->request->getPost('name');
            $phone=$this->request->getPost('phone');
            
            $params=[
                'airport'=>$airport,
                'name'=>$name,
                'phone'=>$phone,
                
            ];
            // pre($params);
            $result=$this->Driver->insert($params);
            if($result)
            {
                $result=['status'=>true,"message"=>"Driver successfully added",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on add driver action",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function update()
    {   
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        $data['id']=$id;
        $res=$this->Driver->where('id',$id)->first();
        $validate = $this->validate(
            [   
                'airport'=>'required',
                'name'=>'required|min_length[2]',
                                
            ],
            [
                'airport'=>[
                    'required'=>'Please select Airport',
                ],
                'name'=>[
                    'required'=>'Please enter name',
                    'min_length'=>'Name must be 2 char long'
                ]
                
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
                $airport=$this->request->getPost('airport');
                $name=$this->request->getPost('name');
                $phone=$this->request->getPost('phone');
                
                $params=[
                    'airport'=>$airport,
                    'name'=>$name,
                    'phone'=>$phone,
                    
                ];
                $result=$this->Driver->update($data['id'], $params);
                if($result)
                {
                    $result=['status'=>true,"message"=>"Record successfully updated",'errors'=>null];
                }else{
                    $result=['status'=>false,"message"=>"Unexpected error on update record",'errors'=>null];
                }
        }
        return $this->response->setJSON($result);
    }
   
}