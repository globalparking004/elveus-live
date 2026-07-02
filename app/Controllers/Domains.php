<?php
namespace App\Controllers;
use App\Models\WebsitesModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;

class Domains extends BaseController
{	
	use ResponseTrait;
    protected $request;
    protected $Websites;
	public function __construct()
    {    	
        $this->Websites = new WebsitesModel;
    }

    public function index()
    {           
        $result=[];
        $data=[
            "page_title"=>"Websites",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('websites'),"title"=>"Websites","status"=>"","link"=>false]]
        ];
       
        return view('websites/view',$data);       
    }
    public function add()
    {
        // $operators = $this->Operators->findAll();
        // $sql = "select * from tbl_websites";
        // $websites = $this->db->query($sql)->getResult();
      

        $data = [
            "page_title" => "Add Domain",
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('domains'), "title" => "Domains", "status" => "active", "link" => true],
                ["href" => base_url('domains/add'), "title" => "Add Domain", "status" => "", "link" => false]
            ]
        ];
        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;
        return view('websites/add', $data);
    }

    public function get_record()
    {   
        $id=$this->request->getVar('id');
        $id=id_de($id);
        $data=$this->Websites->where('id',$id)->first();
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
        $data=$this->Websites->where('id',$id)->first();
        if(sizeof($data)>0)
        {
            $response=$this->Websites->delete($id);
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
            0 => 'logo',
            1 => 'code',
            2 => 'web_name',
            3 => 'domain',
            4 => 'legal_name',
            5 => 'type',
            6 => 'payment_redirection',

        ];
        $sql_count="SELECT count(*) as total FROM tbl_websites WHERE 1=1 ";
        // $sql_count="SELECT count(*) as total FROM tbl_websites";
        $sql_data="SELECT * FROM tbl_websites WHERE 1=1 ";
        if(!empty($search))
        {
            foreach($table_map as $key => $val)
            {
                if($table_map[$key]=='logo') 
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
            // $row[] = $value->logo;
            $row[] = '<img width="30" height="30" src="'.BASEURL.'logos/'.$value->logo.'">';
            $row[] = $value->id;
            $row[] = $value->code;
            $row[] = $value->web_name;
            $row[] = $value->domain;
            $row[] = $value->legal_name;
            $row[] = $value->type;
            $row[] = $value->payment_redirection;

            $id=id_en($value->id);
            $action="<div class=\"dropdown\">
            <button type=\"button\" class=\"btn p-0 dropdown-toggle hide-arrow\" data-bs-toggle=\"dropdown\">
                <i data-feather='more-vertical'></i>               
                </button>
                <div class=\"dropdown-menu\">
                  <a class=\"dropdown-item\" href=" . base_url("domains/edit?id=" . urlencode($id)) . "><i data-feather=\"edit\"></i> Edit</a>
                  <a class=\"dropdown-item\" href=\"javascript:void(0);\" onclick=\"delete_data(`$id`);\"><i data-feather=\"trash\"></i> Delete</a>
                  <a class=\"dropdown-item\" href=" . base_url("domains/duplicate?id=" . urlencode($id)) . "><i data-feather=\"repeat\"></i> Duplicate</a>
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
        $short_code=$this->request->getPost('short_code');
        $airport_name=$this->request->getPost('airport_name');
        $web_name=$this->request->getPost('web_name');
        $domain=$this->request->getPost('domain');
        $legal_name=$this->request->getPost('legal_name');
        $email=$this->request->getPost('email');
        $type=$this->request->getPost('type');
        $google_analytics_id=$this->request->getPost('google_analytics_id');
        $google_adwords_id=$this->request->getPost('google_adwords_id');
        $google_conversion_event_id=$this->request->getPost('google_conversion_event_id');
        $secret_key=$this->request->getPost('secret_key');
        $publisher_key=$this->request->getPost('publisher_key');
        $title=$this->request->getPost('title');
        $header_color=$this->request->getPost('header_color');
        $footer_color=$this->request->getPost('footer_color');
        $introduction=$this->request->getPost('introduction');
        $customer_service=$this->request->getPost('customer_service');
        $address=$this->request->getPost('address');
        $company_id=$this->request->getPost('company_id');
        $footer=$this->request->getPost('footer');
        $terms_condition=$this->request->getPost('terms_condition');
        $privacy_policy=$this->request->getPost('privacy_policy');
        $contact_us=$this->request->getPost('contact_us');
        $cur=$this->request->getPost('cur');
        $logo     = $this->request->getFile('logo');
        $reviews     = $this->request->getFile('reviews');
        $payment_redirection=$this->request->getPost('payment_redirection');
        $terminals = implode(',', $this->request->getPost('terminals'));

        $why_choose     = $this->request->getFile('why_choose');
        $allowedTypes = ['png','jpg']; 
        $reviewsname='';
        if (in_array($reviews->getExtension(), $allowedTypes)) {
            $reviewsname = $reviews->getRandomName();
            $reviews->move(ROOTPATH.'logos/reviews/', $reviewsname);
        }
        if (in_array($logo->getExtension(), $allowedTypes)) {
            $logoname='';
            $logoname = $logo->getRandomName();
            $logo->move(ROOTPATH.'logos/', $logoname);
            $data=[
                'short_code'=>$short_code,
                'airport_name'=>$airport_name,
                'web_name'=>$web_name,
                'domain'=>$domain,
                'legal_name'=>$legal_name,
                'email'=>$email,
                'type'=>$type,
                'code'=>$short_code,
                'title'=>$title,
                'logo'=>$logoname,
                'reviews'=>$reviewsname,
                'introduction'=>$introduction,
                'customer_service'=>$customer_service,
                'address'=>$address,
                'company_id'=>$company_id,
                'footer'=>$footer,
                'terms_conditions'=>$terms_condition,
                'privacy_policy'=>$privacy_policy,
                'contact_us'=>$contact_us,
                'why_choose'=>$why_choose,
                'header_color'=>$header_color,
                'footer_color'=>$footer_color,
                'google_analytics_id'=>$google_analytics_id,
                'google_adwords_id'=>$google_adwords_id,
                'google_conversion_event_id'=>$google_conversion_event_id,
                'secret_key'=>$secret_key,
                'publisher_key'=>$publisher_key,
                'cur'=>$cur,
                'payment_redirection'=>$payment_redirection,
                'terminals'=>$terminals
            ];
            $result=$this->Websites->insert($data);
            if($result)
            {
                $result=['status'=>true,"message"=>"Domain successfully added",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on add domain action",'errors'=>null];
            }
        }
        else{
            $data=[
                'short_code'=>$short_code,
                'web_name'=>$web_name,
                'domain'=>$domain,
                'legal_name'=>$legal_name,
                'email'=>$email,
                'type'=>$type,
                'code'=>$short_code,
                'title'=>$title,
                'reviews'=>$reviewsname,
                'introduction'=>$introduction,
                'customer_service'=>$customer_service,
                'address'=>$address,
                'company_id'=>$company_id,
                'footer'=>$footer,
                'terms_conditions'=>$terms_condition,
                'privacy_policy'=>$privacy_policy,
                'contact_us'=>$contact_us,
                'why_choose'=>$why_choose,
                'header_color'=>$header_color,
                'footer_color'=>$footer_color,
                'google_analytics_id'=>$google_analytics_id,
                'google_adwords_id'=>$google_adwords_id,
                'google_conversion_event_id'=>$google_conversion_event_id,
                'secret_key'=>$secret_key,
                'publisher_key'=>$publisher_key,
                'cur'=>$cur,
                'payment_redirection'=>$payment_redirection,
                'terminals'=>$terminals
            ];
            $result=$this->Websites->insert($data);
            if($result)
            {
                $result=['status'=>true,"message"=>"Domain successfully added",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on add domain action",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function edit()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $domain = $this->Websites->where('id', $id)->first();

        $data = [
            "page_title" => "Edit Domain",
            "domain"=> $domain,
            "breadcrumb" => [
                ["href" => base_url('dashboard'), "title" => "Home", "status" => "active", "link" => true],
                ["href" => base_url('domains'), "title" => "Domains", "status" => "active", "link" => true],
                ["href" => base_url('domains/edit'), "title" => "Edit Domain", "status" => "", "link" => false]
            ]
        ];

        $sql_data = "select * from tbl_websites";
        $result = $this->db->query($sql_data)->getResult();
        $data['websites'] = $result;
        // pre($domain);
        return view('websites/edit', $data);
    }

    public function update()
    {
        $id=$this->request->getPost('id');
        $short_code=$this->request->getPost('short_code');
        $airport_name=$this->request->getPost('airport_name');
        $web_name=$this->request->getPost('web_name');
        $domain=$this->request->getPost('domain');
        $legal_name=$this->request->getPost('legal_name');
        $email=$this->request->getPost('email');
        $type=$this->request->getPost('type');
        $google_analytics_id=$this->request->getPost('google_analytics_id');
        $google_adwords_id=$this->request->getPost('google_adwords_id');
        $google_conversion_event_id=$this->request->getPost('google_conversion_event_id');
        $secret_key=$this->request->getPost('secret_key');
        $publisher_key=$this->request->getPost('publisher_key');
        $title=$this->request->getPost('title');
        $header_color=$this->request->getPost('header_color');
        $footer_color=$this->request->getPost('footer_color');
        $introduction=$this->request->getPost('introduction');
        $customer_service=$this->request->getPost('customer_service');
        $address=$this->request->getPost('address');
        $company_id=$this->request->getPost('company_id');
        $footer=$this->request->getPost('footer');
        $terms_condition=$this->request->getPost('terms_condition');
        $privacy_policy=$this->request->getPost('privacy_policy');
        $contact_us=$this->request->getPost('contact_us');
        $why_choose=$this->request->getPost('why_choose');
        $cur=$this->request->getPost('cur');
        $logo     = $this->request->getFile('logo');
        $reviews     = $this->request->getFile('reviews');
        $payment_redirection=$this->request->getPost('payment_redirection');
        $terminals = ($this->request->getPost('terminals'))?implode(',', $this->request->getPost('terminals')):'';
        $status=$this->request->getPost('status');

        // pre(ROOTPATH);
        // exit;


        $allowedTypes = ['png','jpg'];
        $reviewsname='';
        if (in_array($reviews->getExtension(), $allowedTypes)) {
            $reviewsname = $reviews->getRandomName();
            $reviews->move(ROOTPATH.'logos/', $reviewsname);
        }
        
        if (in_array($logo->getExtension(), $allowedTypes)) {
            $logoname='';
            $logoname = $logo->getRandomName();
            $logo->move(ROOTPATH.'logos/', $logoname);
            $data=[
                'short_code'=>$short_code,
                'airport_name'=>$airport_name,
                'web_name'=>$web_name,
                'domain'=>$domain,
                'legal_name'=>$legal_name,
                'email'=>$email,
                'type'=>$type,
                'code'=>$short_code,
                'title'=>$title,
                'logo'=>$logoname,
                'reviews'=>$reviewsname,
                'introduction'=>$introduction,
                'customer_service'=>$customer_service,
                'address'=>$address,
                'company_id'=>$company_id,
                'footer'=>$footer,
                'terms_conditions'=>$terms_condition,
                'privacy_policy'=>$privacy_policy,
                'contact_us'=>$contact_us,
                'why_choose'=>$why_choose,
                'header_color'=>$header_color,
                'footer_color'=>$footer_color,
                'google_analytics_id'=>$google_analytics_id,
                'google_adwords_id'=>$google_adwords_id,
                'google_conversion_event_id'=>$google_conversion_event_id,
                'secret_key'=>$secret_key,
                'publisher_key'=>$publisher_key,
                'cur'=>$cur,
                'payment_redirection'=>$payment_redirection,
                'terminals'=>$terminals,
                'status'=>$status
            ];
            $result=$this->Websites->where('id', $id)->set($data)->update();
            if($result)
            {
                $result=['status'=>true,"message"=>"Domain successfully updated",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on update domain action",'errors'=>null];
            }
        }
        else{
            $data=[
                'short_code'=>$short_code,
                'airport_name'=>$airport_name,
                'web_name'=>$web_name,
                'domain'=>$domain,
                'legal_name'=>$legal_name,
                'email'=>$email,
                'type'=>$type,
                'code'=>$short_code,
                'title'=>$title,
                'reviews'=>$reviewsname,
                'introduction'=>$introduction,
                'customer_service'=>$customer_service,
                'address'=>$address,
                'company_id'=>$company_id,
                'footer'=>$footer,
                'terms_conditions'=>$terms_condition,
                'privacy_policy'=>$privacy_policy,
                'contact_us'=>$contact_us,
                'why_choose'=>$why_choose,
                'header_color'=>$header_color,
                'footer_color'=>$footer_color,
                'google_analytics_id'=>$google_analytics_id,
                'google_adwords_id'=>$google_adwords_id,
                'google_conversion_event_id'=>$google_conversion_event_id,
                'secret_key'=>$secret_key,
                'publisher_key'=>$publisher_key,
                'cur'=>$cur,
                'payment_redirection'=>$payment_redirection,
                'terminals'=>$terminals,
                'status'=>$status

            ];
            // pre($data);
            $result=$this->Websites->where('id', $id)->set($data)->update();
            if($result)
            {
                $result=['status'=>true,"message"=>"Domain successfully updated",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on update domain action",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function duplicate()
    {
        $id = $this->request->getVar('id');
        $id = id_de($id);
        $domain = $this->Websites->where('id', $id)->first();
        $data = $domain;
        $short_code = $domain['short_code'].'-dupli'.random_int(1, 50);;
        $data['short_code'] = $short_code;
        $data['code'] = $short_code;
        $data['id'] = '';
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = '';
        // pre($data);
        $result = $this->Websites->insert($data);
        if ($result) {
            $result = ['status' => true, "message" => "Record successfully dubplicated", 'errors' => null];
        } else {
            $result = ['status' => false, "message" => "Unexpected error on dubplication action", 'errors' => null];
        }
        return redirect()->to('/domains');
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
            
            $user=$this->Websites->where('id',$id)->first();
            if(sizeof($user)>0)
            {   
                $res=$this->Websites->update($id,['password'=>password_hash($password, PASSWORD_DEFAULT)]);
                if($res)
                {
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
}