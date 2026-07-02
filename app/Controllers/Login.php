<?php
namespace App\Controllers;
use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use xeroapi;

class Login extends BaseController
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
        $data=[
            "page_title"=>"Login",
            "welcome_message"=>"Welcome to Alveus!",
            "login_page_text"=>"Please sign-in to your account and start the adventure"
        ];        
        //send_email("luqman.ali27180@gmail.com","Your Parking Booking Confirmation","174");        
        return view('login',$data);       
    }

    public function auth()
    {   

        $validate = $this->validate(
            [
                'email'=>'required|valid_email',
                'password'=>'required|min_length[5]'
            ],
            [
                'email'=>[
                    'required'=>'Please enter email address',
                    'valid_email'=>'Please enter valid email address',
                ],
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
            $post=$this->request->getPost();
            $email=$this->request->getPost('email');
            $password=$this->request->getPost('password');
            $user=$this->Users->where('email',$email)->first();
            if($user)
            {               
                $verify_password=password_verify($password,$user['password']);
                if($verify_password)
                {   
                    if($user['status']=="active")
                    {   
                        $roles=$this->Roles->where('id',$user['role_id'])->first();
                        if ($user['user_type']) 
                        {
                            if ($user['otp_expiry'] ==NULL || date('Y-m-d H:i:s') > $user['otp_expiry'] ) 
                            {
                                //Generate random 6-digit code
                                $otp = rand(100000, 999999);
                                $params['otp_code'] = $otp;
                                $params['otp_expiry'] = date('Y-m-d H:i:s', strtotime('+7 day'));
                                // $params['otp_expiry'] = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                                // pre($params);
                                // Save OTP & expiry in DB
                                $this->Users->update($user['id'], $params);

                                // Ask user to enter OTP
                                $result = [
                                    'status'  => true,
                                    'message' => 'Verification code sent to your email. Please enter the code.',
                                    'user_id'    => $user['id'],
                                    'role_name'    => $roles['description'],
                                ];
                                send_otp_mail($email, $otp);
                            }else{
                                $result=[
                                    'status'=>true,
                                    'message'=>'Successfully logged in',
                                    'user_id'=>'',
                                    'role_name' => $roles['description']
                                ];
                                
                                $user=array_merge($user,['role_name'=>$roles['description'],'role_permissions'=>$roles['permissions'], 'sessionID'=> session_id()]);
                                $this->session->set(['AUTH'=>$user]);

                                $params['session_id'] = session_id();
                                $this->Users->update($user['id'], $params);
                                user_login($user['id'], $email);
                            }
                                
                        }else{
                            $result=[
                                'status'=>true,
                                'message'=>'Successfully logged in',
                                'user_id'=>'',
                                'role_name' => $roles['description']
                            ];
                            
                            $user=array_merge($user,['role_name'=>$roles['description'],'role_permissions'=>$roles['permissions'], 'sessionID'=> session_id()]);
                            $this->session->set(['AUTH'=>$user]);

                            $params['session_id'] = session_id();
                            $this->Users->update($user['id'], $params);
                            user_login($user['id'], $email);
                        }

                    }else if($user['status']=="inactive"){
                        $result=['status'=>false,'message'=>'your account is inactive, please contact with system admin'];
                    }else if($user['status']=="pending"){
                        $result=['status'=>false,'message'=>'your account is pending, please contact with system admin'];
                    }else{
                        $result=['status'=>false,'message'=>'unexpected login error, please contact with system admin'];
                    }
                }else{
                    $result=['status'=>false,'message'=>'invalid password'];
                }
            }else{
                $result=['status'=>false,'message'=>'invalid email or password'];
            }
        }        
        return $this->response->setJSON($result);
    }

    public function auth2()
    {   

        $validate = $this->validate(
            [
                'email'=>'required|valid_email',
                'password'=>'required|min_length[5]'
            ],
            [
                'email'=>[
                    'required'=>'Please enter email address',
                    'valid_email'=>'Please enter valid email address',
                ],
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
            $post=$this->request->getPost();
            $email=$this->request->getPost('email');
            $password=$this->request->getPost('password');
            $user=$this->Users->where('email',$email)->first();
            if($user)
            {               
                $verify_password=password_verify($password,$user['password']);
                if($verify_password)
                {   
                    if($user['status']=="active")
                    {   
                        //Generate random 6-digit code
                        $otp = rand(100000, 999999);
                        $params['otp_code'] = $otp;
                        $params['otp_expiry'] = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                        // pre($params);
                        // Save OTP & expiry in DB
                        $this->Users->update($user['id'], $params);

                        // Ask user to enter OTP
                        $result = [
                            'status'  => true,
                            'message' => 'Verification code sent to your email. Please enter the code.',
                            'step'    => 'otp_required',
                            'user_id'    => $user['id']
                        ];
                        send_otp_mail($email, $otp);

                    }else if($user['status']=="inactive"){
                        $result=['status'=>false,'message'=>'your account is inactive, please contact with system admin'];
                    }else if($user['status']=="pending"){
                        $result=['status'=>false,'message'=>'your account is pending, please contact with system admin'];
                    }else{
                        $result=['status'=>false,'message'=>'unexpected login error, please contact with system admin'];
                    }
                }else{
                    $result=['status'=>false,'message'=>'invalid password'];
                }
            }else{
                $result=['status'=>false,'message'=>'invalid email or password'];
            }
        }        
        return $this->response->setJSON($result);
    }

    public function verifyOtp()
    {
        $userId = $this->request->getPost('user_id');
        $otp    = $this->request->getPost('otp');

        $user = $this->Users->find($userId);

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'User not found']);
        }

        if ($user['otp_code'] == $otp && strtotime($user['otp_expiry']) > time()) {
            //OTP correct → set session
            $roles = $this->Roles->where('id', $user['role_id'])->first();
            $user  = array_merge($user, [
                'role_name'        => $roles['description'],
                'role_permissions' => $roles['permissions'],
                'sessionID'        => session_id()
            ]);

            $this->session->set(['AUTH' => $user]);

            // clear OTP after success
            $this->Users->update($userId, ['otp_code' => null,'session_id'=>session_id()]);
            // $this->Users->update($userId, ['otp_code' => null, 'otp_expiry' => null,'session_id'=>session_id()]);
            
            user_login($user['id'], $user['email']);

            return $this->response->setJSON(['status' => true, 'message' => 'Login successful']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Invalid or expired code']);
    }

    public function defaultUser()
    {   
        // require FCPATH . '/vendor/autoload.php';

        $provider = new \Calcinai\OAuth2\Client\Provider\Xero([
            'clientId'          => 'B35988A8BF3F4B1692AFA5A7B291CC16',
            'clientSecret'      => 'wl6SgWqauB2bhtiXEFCkBfbDFcYL3hKDWm4SxX2uHWeGh0Gf',
            'redirectUri'       => 'https://portal.jibbajabba.uk.com/xero/callback/B35988A8BF3F4B1692AFA5A7B291CC16',
        ]);

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => 'email profile openid accounting.settings accounting.transactions accounting.contacts offline_access'
        ]);

        print_r($authUrl);
        exit();


        print_r($provider);
        exit();

        // Xero API credentials
        $client_id = 'B35988A8BF3F4B1692AFA5A7B291CC16';
        $client_secret = 'wl6SgWqauB2bhtiXEFCkBfbDFcYL3hKDWm4SxX2uHWeGh0Gf';
        $redirect_uri = 'https://portal.jibbajabba.uk.com/xero/callback/B35988A8BF3F4B1692AFA5A7B291CC16';

        // Access token endpoint
        $token_endpoint = 'https://identity.xero.com/connect/token';

        // API endpoint
        $api_endpoint = 'https://api.xero.com/api.xro/2.0/contacts';

        // Request access token
        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'scope' => 'openid profile email accounting.contacts.read',
        );

        $options = array(
            CURLOPT_URL => $token_endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        $token_data = json_decode($response, true);

        // Retrieve contacts
        $access_token = $token_data['access_token'];

        $options = array(
            CURLOPT_URL => $api_endpoint,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
            ),
            CURLOPT_RETURNTRANSFER => true,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        $contacts_data = json_decode($response, true);

        // Process and display the contacts
        if (isset($contacts_data['Contacts'])) {
            $contacts = $contacts_data['Contacts'];

            // Process and display the contacts
            foreach ($contacts as $contact) {
                // Access contact details
                $contactName = $contact['Name'];
                $contactEmail = $contact['EmailAddress'];

                // Do something with the contact details
                echo "Name: $contactName, Email: $contactEmail\n";
            }
        } else {
            // Error handling
            echo "Failed to retrieve contacts.";
        }



         // $email="admin@example.com";
         // $record=$this->Users->where('email', $email)->first();
         //    if(!$record)
         //    {
         //        $data=[
         //            'first_name'=>'Admin',
         //            'last_name'=>'',
         //            'email'=>'admin@example.com',
         //            'phone'=>'12345678',
         //            'password'=>password_hash('developer@123!', PASSWORD_DEFAULT),
         //            'role_id'=>1,
         //            'status'=>'active',
         //            'type'=>'staff'
         //        ];
         //        $result=$this->Users->insert($data);
         //        print_r($result);
         //    }else{
         //        echo "user already exists";
         //    }

         // $permissions=[
         //    "users"=>[
         //        "view"=>true,"actions"=>["search"=>true,"update"=>true,"delete"=>true,"import"=>true]
         //    ]
         // ];
         // $permissions=json_encode($permissions);
         // print_r($permissions);            
    }

    public function set_password()
    {
        $email=$this->request->getGet('email');
        
        $user=$this->Users->where('email',$email)->first();
        if($user)
        {
            $data=[
                "page_title"=>"Set Password",
                "welcome_message"=>"Welcome to Alveus!",
                "login_page_text"=>"Please set password to your account and start the adventure",
                'email' => $email
            ];        
            //send_email("luqman.ali27180@gmail.com","Your Parking Booking Confirmation","174");        
            return view('password_set',$data);
        }else{
            echo 'Email not found. Please try another one';
        }
    }

    public function update_password()
    {   
        $validate = $this->validate(
            [
                'password' => 'required|min_length[5]',
                'confirm_password' => 'required|matches[password]'
            ],
            [
                'password' => [
                    'required'   => 'Please enter password',
                    'min_length' => 'Password must be at least 5 characters long'
                ],
                'confirm_password' => [
                    'required' => 'Please confirm your password',
                    'matches'  => 'Confirm Password must match Password'
                ]
            ]
        );

        if(!$validate)
        {
            $errors=$this->validation->getErrors();
            $result=["status"=>false,"message"=>'',"errors"=>$errors];
        }else{
            $email=$this->request->getPost('email');
            $user=$this->Users->where('email',$email)->first();
            
            $password=$this->request->getPost('password');
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
         
            $result=$this->Users->update($user['id'], $params);
            if($result)
            {
                $result=['status'=>true,"message"=>"Password set successfully ",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on update record",'errors'=>null];
            }
            
        }        
        return $this->response->setJSON($result);
    }

}