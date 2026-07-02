<?php
namespace App\Controllers;
use App\Models\UsersModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{	
	use ResponseTrait;
    protected $request;
    protected $Users;
    protected $user_id;

	public function __construct()
    {    	
        $this->Users = new UsersModel;

        $user = session()->get('AUTH');
        $this->user_id = $user['id'];
    }

    public function index()
    {   
        $sql_data="SELECT id,description FROM tbl_roles WHERE id>1";
        $result=$this->db->query($sql_data)->getResult();

        $sql_data2="SELECT id,description FROM tbl_operators";
        $result_operators=$this->db->query($sql_data2)->getResult();

        $data=[
            "page_title"=>"Users",
            'roles'=>$result,
            'get_operator'=>$result_operators,            
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('users'),"title"=>"Users","status"=>"","link"=>false]]
        ];
        return view('users/view',$data);       
    }

    public function get_record()
    {   
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Users->where('id',$id)->first();
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
        $data=$this->Users->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $response=$this->Users->delete($id);
            if($response)
            {
                logActivity($this->user_id, $id ,'Delete user', 'User deletd successfully');
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
            0 => 'a.created_at',
            1 => 'a.first_name',
            2 => 'a.last_name',
            3 => 'a.email',
            4 => 'a.phone',
            5 => 'b.description',
            6 => 'a.status',
            6 => 'a.type',
        ];
        $sql_count="SELECT count(*) as total FROM tbl_users a, tbl_roles b WHERE b.id=a.role_id and a.role_id>1 ";
        $sql_data="SELECT a.id, a.first_name,a.last_name,a.email,a.phone,a.password,b.description,a.status,a.created_at,a.type FROM tbl_users a, tbl_roles b WHERE b.id=a.role_id and a.role_id>1 ";
        if(!empty($search))
        {
            foreach($table_map as $key => $val)
            {
                if($table_map[$key]=='a.created_at')
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
        

        // $OrderBy=" ORDER BY ".$table_map[$this->request->getVar('order')[0]['column']];
        $OrderBy=" ORDER BY a.id DESC";
        $SortBy=" ";//$this->request->getVar('order')[0]['dir'];
        $Limit=" LIMIT ".$this->request->getVar('start').",".$this->request->getVar('length');
        $sql_data.=$OrderBy.$SortBy.$Limit;
        // pre($sql_data);
        $result=$this->db->query($sql_data)->getResult();
        $data = array();

        foreach ($result as $key => $value) 
        {   
            $row = array();
            $created_at = date("d-m-Y", strtotime($value->created_at));         
            $row[] = $created_at;             
            $row[] = $value->first_name;
            $row[] = $value->last_name;
            $row[] = $value->email;
            $row[] = $value->phone;
            $row[] = $value->description;
            $badge="";
            if($value->status=="active"){
                $badge="badge badge-glow bg-success";
            }else if($value->status=="inactive"){
                $badge="badge badge-glow bg-danger";
            }else if($value->status=="pending"){
                $badge="badge badge-glow bg-warning";
            }
            $row[] = "<span class='$badge'>".ucfirst($value->status)."</span>";
            $id=id_en($value->id);
            $logs='';
            if ($value->description=='DRT' || $value->description =='CSR') {
                $logs = "<a class=\"dropdown-item\" href=\"". base_url('users/logs?id='.$value->id)."\"><i data-feather=\"activity\"></i> Logs</a>";
            }
            $action="<div class=\"dropdown\">
                <button type=\"button\" class=\"btn p-0 dropdown-toggle hide-arrow\" data-bs-toggle=\"dropdown\">
                <i data-feather='more-vertical'></i>               
                </button>
                <div class=\"dropdown-menu\">
                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"edit_data(`$id`);\"><i data-feather=\"edit\"></i> Edit</a>
                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"reset_password(`$id`);\"><i data-feather='refresh-cw'></i> Reset Password</a>
                    <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
                    ".$logs."
                    <a class=\"dropdown-item\" href=\"".base_url('users/devices/logout?email='.$value->email)."\"><i data-feather=\"log-out\"></i> Logout</a>
                </div>
              </div>";
            $row[] = $action;
            $data[] = $row; 
                
                  // <a class=\"dropdown-item\" href=\"".base_url('users/devices/logout?email='.$value->email)."\"><i data-feather=\"signout\"></i> Logout</a>
                
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
                'first_name'=>'required|min_length[2]',
                'email'=>'required|valid_email|is_unique[tbl_users.email]',
                // 'password'=>'required|min_length[5]',
                'role_id'=>'required|is_not_unique[tbl_roles.id]',
                'status'=>'required|in_list[inactive,active,pending]',
                'type'=>'required|in_list[Staff,Client]',
                
            ],
            [
                'first_name'=>[
                    'required'=>'Please enter first name',
                    'min_length'=>'first name must be 2 char long'
                ],
                'email'=>[
                    'required'=>'Please enter email address',
                    'valid_email'=>'Please enter valid email address',
                    'is_unique'=>'Email already existing, please try another email address'
                ],
                // 'password'=>[
                //     'required'=>'Please enter password',
                //     'min_length'=>'Password must be 5 char long'
                // ],
                'role_id'=>[
                    'required'=>'Please select role',
                    'is_not_unique'=>'Invalid role selection'
                ],
                'status'=>[
                    'required'=>'Please select status',
                    'in_list'=>'Invalid status selection'
                ],
                'type'=>[
                    'required'=>'Please select type',
                    'in_list'=>'Invalid type selection'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
                $first_name=$this->request->getPost('first_name');
                $last_name=$this->request->getPost('last_name');
                $email=$this->request->getPost('email');
                $phone=$this->request->getPost('phone');
                $password=$this->request->getPost('password');
                $role_id=$this->request->getPost('role_id');
                $status=$this->request->getPost('status');
                $type=$this->request->getPost('type');
                $airports = implode(',', $this->request->getPost('airport'));
                
                $operator=$this->request->getPost('operator');

                $password = ($password)? password_hash($password, PASSWORD_DEFAULT):'';

                $data=[
                    'user_type'=>1,
                    'first_name'=>$first_name,
                    'last_name'=>$last_name,
                    'email'=>$email,
                    'phone'=>$phone,
                    'password'=> $password,
                    'role_id'=>$role_id,
                    'status'=>$status,
                    'type'=>$type,
                    'airport'=>$airports,
                    'operator_id'=>$operator
                ];

                $result=$this->Users->insert($data);
        
                if($result)
                {   
                    logActivity($this->user_id, $result ,'Add user', 'User successfully added');
                    set_password_email($first_name, $last_name ,$email, id_en($result));

                    $result=['status'=>true,"message"=>"User successfully added",'errors'=>null];
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
        $res=$this->Users->where('id',$id)->first();
        $validate = $this->validate(
            [   
                'first_name'=>'required|min_length[2]',
                'email'=>'required|valid_email|is_unique[tbl_users.email,id,'.$id.']',
                'role_id'=>'required|is_not_unique[tbl_roles.id]',
                'status'=>'required|in_list[inactive,active,pending]',
                'type'=>'required|in_list[Staff,Client]',                
            ],
            [
                'first_name'=>[
                    'required'=>'Please enter first name',
                    'min_length'=>'first name must be 2 char long'
                ],
                'email'=>[
                    'required'=>'Please enter email address',
                    'valid_email'=>'Please enter valid email address',
                    'is_unique'=>'Email already existing, please try another email address'
                ],                
                'role_id'=>[
                    'required'=>'Please select role',
                    'is_not_unique'=>'Invalid role selection'
                ],
                'status'=>[
                    'required'=>'Please select status',
                    'in_list'=>'Invalid status selection'
                ],
                'type'=>[
                    'required'=>'Please select type',
                    'in_list'=>'Invalid type selection'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 
            $airports = implode(',', $data['airport']);
            $dataX=[
                'first_name'=>$data['first_name'],
                'last_name'=>$data['last_name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'role_id'=>$data['role_id'],
                'status'=>$data['status'],
                'type'=>$data['type'],
                'airport'=> $airports,
                'operator_id'=>$data['operator']
            ];
            // pre($dataX);
            $result=$this->Users->update($data['id'], $dataX);
            if($result)
            {
                logActivity($this->user_id, $data['id'] ,'Update user', 'User successfully updated');
                $result=['status'=>true,"message"=>"Record successfully updated",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on update record",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function get_roles()
    {   
        $data = $this->request->getVar();
        $searchTerm="";
        if(isset($data['searchTerm']))
        {
            $searchTerm=$data['searchTerm'];
        }
        $sql_data="SELECT id,description as text FROM tbl_roles WHERE id>1 AND description like '%$searchTerm%' LIMIT 10";
        $result=$this->db->query($sql_data)->getResult();
        return $this->setResponseFormat('json')->respond($result);
    }

    public function reset_password()    
    {
        $data = $this->request->getVar();
        $validate = $this->validate(
            [ 
                'password'=>'required|min_length[5]',                
            ],
            [
                'password'=>[
                    'required'=>'Please enter password',
                    'min_length'=>'Password must be 5 char long'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{
            $id = $data['user_id'];
            $id=id_de($id);
            $password=$data['password'];
            
            $user=$this->Users->where('id',$id)->first();
            if(sizeof($user)>0)
            {   
                $res=$this->Users->update($id,['password'=>password_hash($password, PASSWORD_DEFAULT)]);
                if($res)
                {
                    logActivity($this->user_id, $id ,'Reset password', 'Password successfully reset');
                    $result=['status'=>true,"message"=>"Password successfully reset"];
                }else{
                    $result=['status'=>false,"message"=>"Unexpected error on reset password"];
                }
            }else{
                $result=['status'=>false,"message"=>"Requested record not found in system"];
            }
        }        
        return $this->response->setJSON($result);
    }

    public function user_logs()
    {   

        $id=$this->request->getGet('id');
        
        $sql = "SELECT * from activity_logs WHERE user_id='$id' ";
        $data = $this->db->query($sql)->getResult();

        if(sizeof($data)>0)
        {
           $data=[
                "page_title"=>"User activity logs",    
                "data"=>$data,    
                "breadcrumb"=>[
                    ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                    ["href"=>base_url('users/logs'),"title"=>"Activity Logs","status"=>"","link"=>false]]
            ];
            return view('users/logs',$data);  
        }else{
           return redirect()->to('/users');
        }
    }

    public function get_devices()
    {
        $data = $this->request->getVar();
        $search=$this->request->getVar('search')['value'];
        $condition="";
        $table_map = [
            0 => 'login_time',
            1 => 'email',
            2 => 'ip_address',
            3 => 'device',
            4 => 'location',
        ];
        $sql_count="SELECT count(*) as total FROM user_logins WHERE 1=1";
        $sql_data="SELECT * FROM user_logins WHERE 1=1";
        if(!empty($search))
        {
            foreach($table_map as $key => $val)
            {
                if($table_map[$key]=='login_time')
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
        // $OrderBy=" ORDER BY ".$table_map[$this->request->getVar('order')[0]['column']];
        $OrderBy=" ORDER BY id DESC";
        $SortBy=" ";//$this->request->getVar('order')[0]['dir'];
        $Limit=" LIMIT ".$this->request->getVar('start').",".$this->request->getVar('length');
        $sql_data.=$OrderBy.$SortBy.$Limit;

        $result=$this->db->query($sql_data)->getResult();
        $data = array();
        foreach ($result as $value) 
        {   
            $row = array();         
            $login_time = date("d-m-Y", strtotime($value->login_time));         
            $row[] = $login_time;
            $row[] = $value->email;
            $row[] = $value->ip_address;
            $row[] = $value->device;
            $row[] = $value->location;
            $action="<a class=\"btn btn-primary waves-effect waves-float waves-light\" href='".base_url('users/devices/logout?email='.$value->email)."' ><i data-feather=\"sign-out\"></i> Logout</a>";
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

    public function device_logout()
    {
        $email = $_GET['email'];
        if (!$email) {
            return redirect()->to('/users');
        }
        $user = $this->Users->where('email',$email)->first();
        $this->Users->update($user['id'], ['session_id' => null]);

        // $sql_data="DELETE FROM user_logins WHERE email='$email'";
        // $this->db->query($sql_data);

        return redirect()->to('/users');
    }

    public function logout()
    {
        // clear session_id in DB
        $this->Users->update(session()->get('AUTH')['id'], ['session_id' => null]);
        $this->session->destroy();
        unset($_SESSION['AUTH']);
        return redirect()->to('/login');
    }
}