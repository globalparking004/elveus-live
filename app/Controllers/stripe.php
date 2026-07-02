<?php
namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\InteliquentModel;
use CodeIgniter\API\ResponseTrait;
use xeroapi;

class stripe extends BaseController
{
    use ResponseTrait;
    protected $Users;
    protected $Roles;
    public function __construct()
    {

    }

    public function index()
    {
        $stripeToken = $_GET['stripeToken'];
        $amount = $_GET['amount'];
        $amount = $amount * 100;
        $ref_id = $_GET['ref_id'];
        $airport_code=$_GET['airport_code'];
        $currency=$_GET['currency'];
        if(trim($currency)=="")
        {
            $currency="eur";
        }
        $sql_data = "SELECT * FROM `tbl_websites` WHERE `short_code`='$airport_code' LIMIT 1";
        $result = $this->db->query($sql_data)->getRow();
       if(isset($result->secret_key))
       {


        $secret_key=$result->secret_key;
        $source = $stripeToken; // Replace with a valid token from Stripe.js or Elements
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "amount=$amount&currency=$currency&source=$source&description=$ref_id");
        curl_setopt($ch, CURLOPT_USERPWD, "$secret_key:");
        
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        curl_close($ch);
        
        $result=json_decode($result);
        
        if(isset($result->status) && ($result->status=='succeeded'))
        {
        
            $id = $result->id;
        //   echo   $status = $result->status;

          $sql_data = "update tbl_booking set stripe_ref_id='$id' where reference ='$ref_id' ";
              $result = $this->db->query($sql_data);
              echo json_encode(['status' => true, 'msg' => "Payment successfully completed",'id'=>"id"]);
        
        
        }

    }

        exit;


        // $stripeConfig = new \Config\Stripe();
        // \Stripe\Stripe::setApiKey($stripeConfig->secretKey);

        // $charge = \Stripe\Charge::create([
        //     'amount' => $amount,
        //     // Amount in cents
        //     'currency' => $currency,
        //     'source' => $stripeToken,
        //     // A test token representing a card
        //     'description' => "$ref_id"
        // ]);

        // if (isset($charge->status) && ($charge->status == 'succeeded')) 
        // {
        //     $id=$charge->id;
        //     $sql_data = "update tbl_booking set stripe_ref_id='$id' where reference ='$ref_id' ";
        //     $result = $this->db->query($sql_data);
        //     echo json_encode(['status' => true, 'msg' => "Payment successfully completed",'id'=>"id"]);
        // }




    }

    public function test(){

        $sql_data = "SELECT * FROM `tbl_websites` WHERE `short_code`='DUB' LIMIT 1";
        $result = $this->db->query($sql_data)->getRow();
       if(isset($result->secret_key))
       {

      echo  $secret_key=$result->secret_key;


       } 
        exit;


$secret_key = 'sk_test_51NuBjeCwpdqMgNLw07bV37ZX2ePtEgIZhwFew6Sah7CTOt2sNb8CkVWoIIHUPpPxxfeAgLUPnhKw11uvZI7WXmd500BQasZnKq'; // Replace with your actual Secret Key
$amount = 2000; // Amount in cents (e.g., $20.00)
$currency = 'usd';
$source = 'tok_1NuzzjCwpdqMgNLwXuHcVXqx'; // Replace with a valid token from Stripe.js or Elements

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "amount=$amount&currency=$currency&source=$source");
curl_setopt($ch, CURLOPT_USERPWD, "$secret_key:");

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);

$result=json_decode($result);

// print_r($result);

// $id = $result->id;
// $status = $result->status;

// echo  $id = $result->id;
// echo   $status = $result->status;
if(isset($result->status) && ($result->status=='succeeded'))
{

  echo  $id = $result->id;
  echo   $status = $result->status;


}




    }

}