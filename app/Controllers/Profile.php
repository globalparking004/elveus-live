<?php
namespace App\Controllers;
use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;

class Profile extends BaseController
{   
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    public function __construct()
    {       
        $this->Users = new UsersModel;
        $this->Roles = new RolesModel;
    }

    public function index()
    {       	
    	$sql_data="SELECT id,description FROM tbl_roles";
        $result=$this->db->query($sql_data)->getResult();
        $session=$this->session->get("AUTH");

        $user=$this->Users->where('id',$session['id'])->first();
        // pre($user);
        $data=[
            "page_title"=>"Profile",
            'roles'=>$result,
            'user'=>$user,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('profile'),"title"=>"Profile","status"=>"","link"=>false]]
        ];
        
        return view('profile',$data);      
    }


    public function change_password()
    {
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        $data['id']=$id;
        $res=$this->Users->where('id',$id)->first();
        $validate = $this->validate(
            [ 
                'old_password'=>'required|min_length[5]',
                'new_password'=>'required|min_length[5]',
                'confirm_password'=>'required|min_length[5]|matches[new_password]',                 
            ],
            [
                'old_password'=>[
                    'required'=>'Please enter password',
                    'min_length'=>'Password must be 5 char long'
                ],
                'new_password'=>[
                    'required'=>'Please enter password',
                    'min_length'=>'Password must be 5 char long'
                ],
                'confirm_password'=>[
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
            $user=$this->Users->where('id',$id)->first();
            $verify_password=password_verify($data['old_password'],$user['password']);
            if($verify_password)
            {   
                $dataX=['password'=>password_hash($data['new_password'], PASSWORD_DEFAULT)];
                $result=$this->Users->update($id, $dataX);
                if($result)
                {
                    $result=['status'=>true,"message"=>"password successfully changed",'errors'=>null];
                }else{
                    $result=['status'=>false,"message"=>"Unexpected error on change password",'errors'=>null];
                }
            }else{
                $result=['status'=>false,"message"=>"invalid old password",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function save()
    {
    	$data=$this->request->getVar();
        $id=id_de($data['id']);
        $data['id']=$id;
        $res=$this->Users->where('id',$id)->first();
        $validate = $this->validate(
            [   
                'first_name'=>'required|min_length[2]',
                'email'=>'required|valid_email|is_unique[tbl_users.email,id,'.$id.']'
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
                ]
            ]
        );

        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];

        } else {

        		$dataX=[
                    'first_name'=>$data['first_name'],
                    'last_name'=>$data['last_name'],
                    'email'=>$data['email'],
                    'phone'=>$data['phone'],
                    'pic'=>$data['pic']
                ];
                $result=$this->Users->update($data['id'], $dataX);
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