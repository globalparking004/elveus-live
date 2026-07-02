<?php

namespace App\Libraries;

use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client;

use CodeIgniter\Session\Session;



class ClickSendService
{
    private $apiInstance;

    public function __construct()
    {
        $AUTH=session()->get('AUTH');
        $username=($AUTH['role_id'] == 1)? ADAPIUSERNAME: APIUSERNAME;
        $apikey=($AUTH['role_id'] == 1)? ADAPIKEY: APIKEY;
    
        $config = Configuration::getDefaultConfiguration()
            ->setUsername($username)
            ->setPassword($apikey);

        $this->apiInstance = new SMSApi(new Client(), $config);
    }

    public function smsInbound($page)
    {
        $q = "q_example"; // string | Your keyword or query.
        // $page = 1; // int | Page number
        $limit = 15; // int | Number of records per page
        try {
            $result = $this->apiInstance->smsInboundGet($q, $page, $limit);

            return json_decode($result, true);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function sendSMS($to, $message)
    {
        $msg = new SmsMessage();
        $msg->setSource("php");
        $msg->setBody($message);
        $msg->setTo($to);
        $msg->setFrom('AirportPark');

        $sms_messages = new SmsMessageCollection();
        $sms_messages->setMessages([$msg]);

        try {
            $result = $this->apiInstance->smsSendPost($sms_messages);

            return json_decode($result, true);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function smsPrice($to, $message)
    {
        $msg = new SmsMessage();
        $msg->setSource("php");
        $msg->setBody($message);
        $msg->setTo($to);

        $sms_messages = new SmsMessageCollection();
        $sms_messages->setMessages([$msg]);

        try {
            $result = $this->apiInstance->smsPricePost($sms_messages);
            return json_decode($result, true);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function smsTemplates($page)
    {
        // $page = 1; // int | Page number
        $limit = 30; // int | Number of records per page
        try {
            $result = $this->apiInstance->smsTemplatesGet($page, $limit);
            return json_decode($result);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function getTemplateById($templateId)
    {
        $client = \Config\Services::curlrequest();

        $url = "https://rest.clicksend.com/v3/sms/templates/{$templateId}";

        $response = $client->get($url, [
            'auth' => [APIUSERNAME, APIKEY],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $result = json_decode($body, true);
            return $result['data'];
        } else {
            return  'error: Failed to retrieve template';
                // 'status' => $response->getStatusCode(),
            // ]);
        }
    }

    public function smsReceipts($page)
    {
        // $page = 1; // int | Page number
        $limit = 15; // int | Number of records per page
        try {
            $result = $this->apiInstance->smsReceiptsGet($page, $limit);
            return json_decode($result, true);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function smsHistory($page, $limit, $date_from, $date_to)
    {
        $q = "order_by=message_id:desc"; // string | Custom query Example: from:{number},status_code:201.
        // $date_from = '1436879372'; // int | Start date
        // $date_to = '1436879372'; // int | End date
        // $page = 1; // int | Page number
        // $limit = 15; // int | Number of records per page
        // 'order_by=date:desc'
        try {
            $result = $this->apiInstance->smsHistoryGet('', 0, 0, $page, $limit);
            $history = json_decode($result, true);
            
            return $history['data'];
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
