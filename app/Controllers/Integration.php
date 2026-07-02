<?php 
namespace App\Controllers;

use App\Models\DriverModel;
use App\Libraries\DataTable;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\I18n\Time;

use App\Libraries\ClickSendService;


class Integration extends BaseController
{	
	use ResponseTrait;
    protected $request;
    protected $Driver;
    protected $clickSend;
    protected $page;

	public function __construct()
    {    	
        $this->Driver  = new DriverModel();

        $this->clickSend = new ClickSendService();

        $this->page=1;

    }

    public function index()
    {   
        $data=[
            "page_title"=>"ClickSend",   
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>'#',"title"=>"Integration","status"=>"","link"=>false],
                ["href"=>base_url('drivers'),"title"=>"ClickSend","status"=>"","link"=>false]]
        ];

        $to = "+61411111111"; //+447984390952 Replace with recipient's number
        $message = "Hello, this is a test message from CodeIgniter 4!";

        $to_date = time();
        // End date as integer
        $from_date = strtotime('-2 months', $to_date);
        // print_r($to_date);echo'<br>twomonth: ';
        // print_r($from_date);echo'<br>';
        
        // $response = $this->clickSend->sendSMS($to, $message);
        // $res1 = $this->clickSend->smsPrice($to, $message);
        $templates = $this->clickSend->smsTemplates($this->page);
        // $res3 = $this->clickSend->smsReceipts($this->page);
        // $res4 = $this->clickSend->smsInbound($this->page);
        // $res5 = $this->clickSend->smsHistory($this->page, '10',$from_date, $to_date);
        // pre($res5);
        $data['templates'] = (isset($templates->data))? $templates->data->data:'';

        return view('integration/clicksend',$data);       
    }

    public function get()
    {
        $data = $this->request->getVar();
        $search=$this->request->getVar('search')['value'];

        $this->page = $this->request->getVar('draw');
        $limit=$this->request->getVar('length');

        $start=$this->request->getVar('start');
        $length=$this->request->getVar('length');

        $pageNumber = ($length > 0) ? ($start / $length) + 1 : 1;
        
        $to_date = time();
        $from_date = strtotime('-2 months', $to_date);

        $resp = $this->clickSend->smsHistory($pageNumber, $limit, $from_date, $to_date);
        // $history = [];
        // foreach ($resp['data'] as $item) {
        //     $history[] = [
        //         'message_id' => $item['message_id'],
        //         'body' => $item['body'],
        //         'status_code' => $item['status_code'],
        //         'status' => $item['status'],
        //         'from' => $item['from'],
        //         'to' => $item['to'],
        //         'date' => date('M d, Y h:i:s A', $item['date']), // Convert timestamp to Y-m-d H:i:s
        //     ];
        // }

        // Sort by date in descending order
        // $history1 = array_reverse($history);
        
        // pre($data);
        $data = array();
        foreach ($resp['data'] as $value) 
        {   
            $row = array(); 
            $created_at = date("M d, Y h:i:s A", $value['date']);
            // $created_at = $value['date'];
            $status_class='';
            if($value['status_code'] == 201): 
                $status_class= 'sent';
            elseif($value['status'] == 'Received'):
                $status_class='received';
            else:
                $status_class = 'failed';
            endif;
            

            // $row[] = $value->_api_username;
            $row[] = $created_at;
            $row[] = $value['from'];
            $row[] = $value['to'];
            $row[] = '<span class="badge status-'.$status_class.'">'.$value['status'].'</span>';
            $row[] = $value['body'];
            
            
            $id=id_en($value['message_id']);
            $data[] = $row; 
        }
        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'length' => intval($this->request->getVar('length')),
            'recordsTotal'=>$resp['total'],
            'recordsFiltered'=>$resp['total'],
            'data'=>$data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_template()
    {   
        $template_id=$this->request->getVar('template_id');
        $response =  $this->clickSend->getTemplateById($template_id);
        // pre($response['body']);
        if($response)
        {
            $result=['status'=>true,"data"=> $response];
        }else{
            $result=['status'=>false,"message"=>"Requested record not found in system"];
        }
        return $this->response->setJSON($result);
    }

    public function sent()
    {
        $phone = $_GET['phone'] ? $_GET['phone'] : '';
        $message = $_GET['message'] ? $_GET['message'] : '';

        if($phone && $message)
        {
            $to=$phone;
            $message=$message;
    
            $result = $this->clickSend->sendSMS($to, $message);
            // pre($result);
            if($result['http_code'] == 200)
            {
                $result=['status'=>true,"message"=>"Message sent successfully",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on sent message action",'errors'=>true];
            }
        }else{
            $result=['status'=>false,"message"=>"Phone or message is missing",'errors'=>true];
        }
        return $this->response->setJSON($result);
    }

    public function sent1()
    {
        $validate = $this->validate(
            [   
                'phone'=>'required',
                'message'=>'required|min_length[2]',
                
            ],
            [
                'phone'=>[
                    'required'=>'Please enter phone number',
                ],
                'message'=>[
                    'required'=>'Please enter message',
                    'min_length'=>'Name must be 2 char long'
                ]
            ]
        );
        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{ 

            $to=$this->request->getPost('phone');
            $message=$this->request->getPost('message');
    
            $result = $this->clickSend->sendSMS($to, $message);
            // pre($result);
            if($result['http_code'] == 200)
            {
                $result=['status'=>true,"message"=>"Message sent successfully",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on sent message action",'errors'=>null];
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
   
}