<?php
namespace App\Controllers;
class Home extends BaseController
{
    public function index()
    {   

        $sql = "SELECT sum(price) as profit FROM `tbl_booking` WHERE date(created_at)=date(now()) AND status='1'";
        $result = $this->db->query($sql)->getResult();

        print_r($result);
        exit();

        $data=[
            "page_title"=>"Home"
        ];        


        return view('home',$data);        
    }
}