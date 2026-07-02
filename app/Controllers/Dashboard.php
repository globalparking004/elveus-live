<?php
namespace App\Controllers;


use CodeIgniter\API\ResponseTrait;
use App\Libraries\PdfGenerator;
use CodeIgniter\Database\BackupUtils;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

ini_set('memory_limit', '-1');

class Dashboard extends BaseController
{
    use ResponseTrait;
    protected $client;
    protected $service;
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(WRITEPATH . 'service_account.json');  // Path to your service account file
        $this->client->addScope(Google_Service_Drive::DRIVE_FILE);

        $this->service = new Google_Service_Drive($this->client);
    }

    public function index()
    {
        // $this->export_database();
        // $this->export_custom_bookings();
        // $AUTH=session()->get('AUTH');
        // pre($AUTH);
        $today = date("Y-m-d");
        
        /////////////////// capacity //////////////////////
        $airportCapacity = get_booking_capacity();
         
        $profitAvg = get_profitAvg();
        $profitAvgW = get_profitAvg(1);
        $profitAvgS = get_profitAvg(2);
        $total_bookings = get_total_bookings();
        $total_bookingW = get_total_bookings('',1);
        $total_bookingS = get_total_bookings('',2);
        $completed_bookings = get_total_bookings(1);
        $completed_bookingW = get_total_bookings(1,1);
        $completed_bookingS = get_total_bookings(1,2);

        $data = [
            "page_title" => "Dashboard",
            "breadcrumb" => [],
            "stats" => [ 
                'profit' => $profitAvg[0], 
                'total_bookings' => $total_bookings, 
                'completed_bookings' => $completed_bookings, 
                'avg' => $profitAvg[1],

                'profitW' => $profitAvgW[0], 
                'total_bookingsW' => $total_bookingW, 
                'completed_bookingsW' => $completed_bookingW, 
                'avgW' => $profitAvgW[1],

                'profitS' => $profitAvgS[0], 
                'total_bookingS' => $total_bookingS, 
                'completed_bookingS' => $completed_bookingS, 
                'avgS' => $profitAvgS[1]
            ],
            //"breadcrumb"=>[["href"=>"#","title"=>"home","status"=>"active","link"=>true],["href"=>"#","title"=>"view","status"=>"","link"=>false]]
            "airportCapacity" => $airportCapacity
        ];
        // print_r($profitAvg);
        // pre($profitAvgS); 
        return view('home', $data);
    }

    public function get_stastics()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = (isset($_GET['DateFrom'])) ? date('Y-m-d', strtotime($_GET['DateFrom'])) : date('Y-m-d');
        $TimeFrom = (isset($_GET['TimeFrom'])) ? $_GET['TimeFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? date('Y-m-d', strtotime($_GET['DateTo'])) : date('Y-m-d');
        $TimeTo = (isset($_GET['TimeTo'])) ? $_GET['TimeTo'] : date('Y-m-d');

        $DateFrom2 = (isset($_GET['DateFrom2'])) ? date('Y-m-d', strtotime($_GET['DateFrom2'])) : date('Y-m-d');
        $DateTo2 = (isset($_GET['DateTo2'])) ? date('Y-m-d', strtotime($_GET['DateTo2'])) : date('Y-m-d');

        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'source',
            1 => 'airport',
        ];

        $SQLWebsiteCondition = " AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source NOT IN ('CPD' ,'CTAP' ,'P4U', 'Holiday Extras','ParkVia','Park&Fly','FreeToMove','Airport Parking With Us','JBF','Cash Booking','YTE' ,'HCP' ,'CYP', 'https://longtermparking.ie/','Go Comparison','goairportparking.com','www.ca.vu' ,'skyparkingservices.co.uk') ";

        // $SQLWebsiteCondition1 = "  AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP'  OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu') ";
        $SQLWebsiteCondition1 = "  AND (reference IS NOT NULL AND reference !='') AND reference NOT LIKE 'GL-%' AND reference NOT LIKE 'GL %' AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP' OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu' OR source='skyparkingservices.co.uk') ";


        $SQLstatus = " AND status='1' ";
     
        $SQLFilterDate = "AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        $SQLFilterDate1 = "AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";

        // $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate1 = "AND date(booked_at)='$DateFrom'";
        

        $sql_count = "SELECT count(*) as total FROM `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus";
        $sql_data = "SELECT airport,count(*) as totalQTY,SUM(price) as totAmount FROM `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus $SQLWebsiteCondition";
        $sql_data2 = "SELECT airport,count(*) as totalQTY2,SUM(price) as totAmount2 FROM `tbl_booking` WHERE 1=1  $SQLFilterDate1 $SQLstatus $SQLWebsiteCondition1";

        // exit($sql_data2);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'source') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
            // pre($condition);
        }

        $GroupBy = " GROUP BY airport";
        $sql_count = $sql_count . $condition. $GroupBy;

        $sql_data = $sql_data . $condition;
        $sql_data2 = $sql_data2 . $condition;
        

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY airport ASC";


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $GroupBy. $OrderBy  . $Limit;
        $sql_data2 .= $GroupBy. $OrderBy  . $Limit;

        // pre($sql_data);
        $result = $this->db->query($sql_data)->getResult();
        $result2 = $this->db->query($sql_data2)->getResult();

        $sql_data3="SELECT short_code as airport,web_name,airport_name  FROM `tbl_websites` WHERE status=1 GROUP BY short_code ORDER BY short_code ASC";
        $result3 = $this->db->query($sql_data3)->getResult();

        $data = array();
        $totalll_count = 0;
        $grand_totall = array();

        $bookings=0;
        $sbookings=0;
        $amount=0;
        $samount=0;

        $merged = mergeAirportData($result, $result2, $result3);

        // pre($merged);
        foreach ($merged as $key => $value) 
        {
            if ($value->airport) {
                $row = array();
                $bookings+= $value->totalQTY;
                $sbookings+= $value->totalQTY2;
                $amount+= $value->totAmount;
                $samount+= $value->totAmount2;

                $classLeft  = ($value->totalQTY  > $value->totalQTY2) ? ' class="high-price"' : 'low-price';
                $classRight = ($value->totalQTY2 > $value->totalQTY) ? ' class="high-price"' : 'low-price';

                $row[] = $value->airport;
                $row[] = $value->totalQTY;
                $row[] = $value->totAmount;
                $row[] = $value->totalQTY2;
                $row[] = $value->totAmount2;
                $row[] = $value->totalQTY+$value->totalQTY2;

                $data[] = $row;
            }
        }
        $finalAmount = round($amount+$samount,2);
        $finalTotal= $bookings+$sbookings." (".$finalAmount.")";
        $tfooter = array("Total",$bookings,round($amount,2),$sbookings,round($samount,2),$finalTotal);
        if($data):
            array_push($data, $tfooter);
        endif;

        $totalll_count_result = ($total_count) ? $total_count->total:0;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_stastics2()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = (isset($_GET['DateFrom'])) ? date('Y-m-d', strtotime($_GET['DateFrom'])) : date('Y-m-d');
        $TimeFrom = (isset($_GET['TimeFrom'])) ? $_GET['TimeFrom'] : '';
        $DateTo = (isset($_GET['DateTo'])) ? date('Y-m-d', strtotime($_GET['DateTo'])) : date('Y-m-d');
        $TimeTo = (isset($_GET['TimeTo'])) ? $_GET['TimeTo'] : date('Y-m-d');

        $DateFrom = date('Y-m-d H:i:s', strtotime($DateFrom.' '.$TimeFrom));
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo.' '.$TimeTo));

        $condition = "";
        $table_map = [
            0 => 'source',
            1 => 'airport',
        ];

        $SQLWebsiteCondition = " AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source NOT IN ('CPD' ,'CTAP' ,'P4U', 'Holiday Extras','ParkVia','Park&Fly','FreeToMove','Airport Parking With Us','JBF','Cash Booking','YTE' ,'HCP' ,'CYP', 'https://longtermparking.ie/','Go Comparison','goairportparking.com','www.ca.vu', 'skyparkingservices.co.uk') ";

        $SQLWebsiteCondition1 = "  AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP'  OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu' OR source='skyparkingservices.co.uk') ";


        $SQLstatus = " AND status='1' ";
     
        $SQLFilterDate = "AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";
        $SQLFilterDate1 = "AND booked_at BETWEEN '$DateFrom' AND '$DateTo'";

        // $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        // $SQLFilterDate1 = "AND date(booked_at)='$DateFrom'";
        

        $sql_count = "SELECT count(*) as total FROM `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus";
        $sql_data = "SELECT airport,count(*) as totalQTY,SUM(price) as totAmount FROM `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus $SQLWebsiteCondition";
        $sql_data2 = "SELECT airport,count(*) as totalQTY2,SUM(price) as totAmount2 FROM `tbl_booking` WHERE 1=1  $SQLFilterDate1 $SQLstatus $SQLWebsiteCondition1";

        // exit($sql_data2);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'source') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
            // pre($condition);
        }

        $GroupBy = " GROUP BY airport";
        $sql_count = $sql_count . $condition. $GroupBy;

        $sql_data = $sql_data . $condition;
        $sql_data2 = $sql_data2 . $condition;
        

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY airport ASC";


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $GroupBy. $OrderBy  . $Limit;
        $sql_data2 .= $GroupBy. $OrderBy  . $Limit;

        // pre($sql_data);
        $result = $this->db->query($sql_data)->getResult();
        $result2 = $this->db->query($sql_data2)->getResult();

        $sql_data3="SELECT short_code as airport,web_name,airport_name  FROM `tbl_websites` WHERE status=1 GROUP BY short_code ORDER BY short_code ASC";
        $result3 = $this->db->query($sql_data3)->getResult();

        $data = array();
        $totalll_count = 0;
        $grand_totall = array();

        $bookings=0;
        $sbookings=0;
        $amount=0;
        $samount=0;

        $merged = mergeAirportData($result, $result2, $result3);

        // pre($merged);
        foreach ($merged as $key => $value) 
        {
            if ($value->airport) {
                $row = array();
                $bookings+= $value->totalQTY;
                $sbookings+= $value->totalQTY2;
                $amount+= $value->totAmount;
                $samount+= $value->totAmount2;

                $classLeft  = ($value->totalQTY  > $value->totalQTY2) ? ' class="high-price"' : 'low-price';
                $classRight = ($value->totalQTY2 > $value->totalQTY) ? ' class="high-price"' : 'low-price';

                $row[] = $value->airport;
                $row[] = $value->totalQTY;
                $row[] = $value->totAmount;
                $row[] = $value->totalQTY2;
                $row[] = $value->totAmount2;
                $row[] = $value->totalQTY+$value->totalQTY2;

                $data[] = $row;
            }
        }
        $finalAmount = round($amount+$samount,2);
        $finalTotal= $bookings+$sbookings." (".$finalAmount.")";
        $tfooter = array("Total",$bookings,round($amount,2),$sbookings,round($samount,2),$finalTotal);
        if($data):
            array_push($data, $tfooter);
        endif;

        $totalll_count_result = ($total_count) ? $total_count->total:0;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function get_go_stastics()
    {
        $AUTH=session()->get('AUTH');
        
        $data = $this->request->getVar();
        $search = $this->request->getVar('search')['value'];

        $DateFrom = date('Y-m-d');
        $DateTo = date('Y-m-d');

        $filter_date = (isset($_GET['filter_date'])) ? $_GET['filter_date'] : '';
        $website = (isset($_GET['website'])) ? $_GET['website'] : '';


        $condition = "";
        $table_map = [
            0 => 'source',
            1 => 'airport',
        ];

        $SQLWebsiteCondition = " AND source ='goairportparking.com'";


        $SQLstatus = " AND status='1' ";
        $SQLFilterDate = "AND date(booked_at) BETWEEN '$DateFrom' AND '$DateTo'";
        

        $sql_count = "SELECT count(*) as total FROM `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus";
        $sql_data = "SELECT airport,count(*) as totalQTY,SUM(price) as totAmount FROM  `tbl_booking` WHERE 1=1  $SQLFilterDate $SQLstatus $SQLWebsiteCondition";

        // exit($sql_data);

        if (!empty($search)) {
            foreach ($table_map as $key => $val) {
                if ($table_map[$key] == 'source') {
                    $condition .= " AND ( " . $val . " LIKE '%" . $search . "%'";
                } else {
                    $condition .= " OR " . $val . " LIKE '%" . $search . "%'";
                }
            }
            $condition .= " )";
            // pre($condition);
        }

        $GroupBy = " GROUP BY airport";
        $sql_count = $sql_count . $condition. $GroupBy;

        $sql_data = $sql_data . $condition;
        

        $total_count = $this->db->query($sql_count)->getRow();
        
        $OrderBy = " ORDER BY airport ASC";


        $Limit = " LIMIT " . $this->request->getVar('start') . "," . $this->request->getVar('length');
        
        $sql_data .= $GroupBy. $OrderBy  . $Limit;

        // pre($sql_data);
        $result = $this->db->query($sql_data)->getResult();

        $sql_data3="SELECT short_code as airport,web_name,airport_name  FROM `tbl_websites` WHERE status=1 GROUP BY short_code ORDER BY short_code ASC";
        $result3 = $this->db->query($sql_data3)->getResult();

        $data = array();
        $totalll_count = 0;
        $grand_totall = array();

        $bookings=0;
        $sbookings=0;
        $amount=0;
        $samount=0;

        $merged = mergeGoAirportData($result, $result3);

        // pre($merged);
        foreach ($result as $key => $value) 
        {
            if ($value->airport) {
                $row = array();
                $bookings+= $value->totalQTY;
                $amount+= $value->totAmount;

                $row[] = $value->airport;
                $row[] = $value->totalQTY;
                $row[] = $value->totAmount;

                $data[] = $row;
            }
        }
        $tfooter = array("Total",$bookings,round($amount,2));
        if($data):
            array_push($data, $tfooter);
        endif;

        $totalll_count_result = ($total_count) ? $total_count->total:0;

        $output = [
            'draw' => intval($this->request->getVar('draw')),
            'recordsTotal' => $totalll_count_result,
            'recordsFiltered' => $totalll_count_result,
            'data' => $data
        ];
        return $this->setResponseFormat('json')->respond($output);
    }

    public function export_bookings()
    {
        // $sql2 = "SELECT firstName, surname, email, contactNumber, created_at, airport FROM `tbl_booking` WHERE airport IS NOT NULL";
        $sql2 = "SELECT firstName, surname, email, contactNumber, source, created_at, airport FROM `tbl_booking` WHERE airport IS NOT NULL";
        $bookings = $this->db->query($sql2)->getResult();
        // pre($bookings);

        // $date = date('Y-m-d_H-i-s');
        $date = date('Y-m-d');
        $filePath = WRITEPATH . 'exports/';
        $fileName = 'bookings_export_-'.$date.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV
        fputcsv($file, ['First Name', 'Last Name', 'Email', 'Contact No', 'Source', 'Created At', 'Airport']);

        // Add rows to the CSV file
        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking->firstName,
                $booking->surname,
                $booking->email,
                $booking->contactNumber,
                $booking->source,
                $booking->created_at,
                $booking->airport
            ]);
        }

        fclose($file);
        // file_put_contents($filePath . $fileName, $file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    public function export_custom_bookings()
    {
        // $sql2 = "SELECT firstName, surname, email, contactNumber, created_at, airport FROM `tbl_booking` WHERE airport IS NOT NULL";
        // $sql2 = "SELECT reference,depart_at,price,product_id FROM `tbl_booking` WHERE airport='DUB' AND operator_id=7 AND date(depart_at) BETWEEN '2025-02-01' AND '2025-02-28' AND status=1";
         // $sql2= "SELECT id,reference,airport,carReg,depart_at,return_at FROM `tbl_booking` WHERE airport='DUB' AND date(depart_at) < date('Y-m-d') AND date(return_at) > date('Y-m-d')";
         $sql_query= "SELECT * FROM `tbl_booking` WHERE date(created_at) < '2025-11-31' AND date(created_at) > '2025-11-01'";
        $date = date('Y-m-d');

        // $sql_query = "SELECT  p.name, b.airport,b.reference, b.depart_at, b.return_at,b.carReg,b.status, b.show_status,bc.status as bcstatus FROM `tbl_products` p LEFT JOIN `tbl_booking` b ON p.id=b.product_id 
        //         LEFT JOIN (
        //             SELECT *
        //             FROM tbl_booking_collect c
        //             WHERE c.id = (
        //                 SELECT MIN(id) 
        //                 FROM tbl_booking_collect 
        //                 WHERE booking_id = c.booking_id ORDER BY id desc
        //             )
        //             AND (c.status = 'collected' OR c.status = 'returned')
        //         ) bc ON b.id = bc.booking_id  
        //         WHERE b.airport='DUB' AND b.depart_at < '$date 12:00:00' AND b.return_at > '$date 12:00:00' AND b.status=1 ORDER BY b.return_at ASC";

        $bookings = $this->db->query($sql_query)->getResult();
        // pre(count($bookings));

        // $date = date('Y-m-d_H-i-s');
        $date = date('Y-m-d');
        $filePath = WRITEPATH . 'exports/';
        $fileName = 'bookings_export_-'.$date.'.csv';
        if (! is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        
        $file = fopen($filePath . $fileName, 'w');

        // Add the header of the CSV
        fputcsv($file, ['id','reference', 'airport', 'Firstname', 'Surname', 'email', 'contactNumber', 'price', 'depart_at', 'return_at','carReg','carMake','carModel', 'carColour']);

        // Add rows to the CSV file
        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking->id,
                $booking->reference,
                $booking->airport,
                $booking->firstName,
                $booking->surname,
                $booking->email,
                $booking->contactNumber,
                $booking->price,
                $booking->depart_at,
                $booking->return_at,
                $booking->carReg,
                $booking->carMake,
                $booking->carModel,
                $booking->carColour,
            ]);
        }

        fclose($file);
        // file_put_contents($filePath . $fileName, $file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }

    public function export_database2()
    {
        // 0 0 * * * cd /var/www/html/booking && php spark Dashboard:export_database >> writable/logs/cron.log 2>&1
        helper('download');
        helper('query');
        // Database configuration
        $db_host = env('database.default.hostname');
        $db_user = env('database.default.username');
        $db_pass = env('database.default.password');
        $db_name = env('database.default.database');
        
        // Create backup directory if it doesn't exist
        $backupDir = WRITEPATH . 'DBbackups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        // Set backup filename
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $backup_file = $backupDir . $filename;

        
        // Build the command
        $command = "mysqldump --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name} > {$backup_file}";
        
        // Execute the command
        system($command, $output);
        
        if ($output === 0) {
            if (file_exists($backup_file)) {
                // $file_id = $this->upload_gd($filename, $backup_file);
                $file_id='';
                if ($file_id) {
                    // unlink($backup_file); 
                    // $to = ['dallen.airportparking@gmail.com', 'globalparking004@gmail.com'];
                    send_single_email('dallen.airportparking@gmail.com', "Database Backup",'Database Backup successful! Uploaded to Google Drive.');
                    send_single_email('globalparking004@gmail.com', "Database Backup",'Database Backup successful! Uploaded to Google Drive.',$backup_file);
                    echo'Backup successful! Uploaded to Google Drive.';
                }else{
                    // send_single_email('dallen.airportparking@gmail.com', "Database Backup",'Database Backup error.');
                    send_single_email('hafizhassan229@gmail.com', "Database Backup",'Database Backup error.',$backup_file);
                    $isSend = send_single_email('globalparking004@gmail.com', "Database Backup",'Database Backup error.',$backup_file);
                    pre($isSend);
                    if ($isSend) {
                        echo'send email';
                        print_r($backup_file);die;
                    }else{
                        echo'Email not send';die;
                    }
                }
                
                // return redirect()->to('reports/exports')->with('success', 'Backup successful! Uploaded to Google Drive.');
                
                // return $this->response->download($backup_file, null)->setFileName($filename);
            }
        } else {
            // log_message('error', 'Database backup failed with output: ' . $output);
            return redirect()->to('reports/exports')->with('error', 'Error creating database backup.');
        }

        // return $this->response->setJSON($result);
    }
    public function export_database()
    {
        helper('download');
        helper('query');

        // DB config from env
        $db_host = env('database.default.hostname');
        $db_user = env('database.default.username');
        $db_pass = env('database.default.password');
        $db_name = env('database.default.database');

        // Paths
        $backupDir = WRITEPATH . 'DBbackups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d-H-i-s');
        $sqlFile = $backupDir . "backup-{$timestamp}.sql";
        $zipFile = $backupDir . "backup-{$timestamp}.zip";

        // Remove any leftover files with same names (rare)
        if (file_exists($sqlFile)) { unlink($sqlFile); }
        if (file_exists($zipFile)) { unlink($zipFile); }

        // Build safe command (avoid exposing password on cmdline).
        // Use escapeshellarg for host/user/name. Keep password in env MYSQL_PWD.
        $hostEsc  = escapeshellarg($db_host);
        $userEsc  = escapeshellarg($db_user);
        $nameEsc  = escapeshellarg($db_name);
        $mysqldumpCmd = "mysqldump --host={$hostEsc} --user={$userEsc} {$nameEsc}";

        // Use proc_open to write stdout directly to file and set MYSQL_PWD in env
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $sqlFile, 'w'],
            2 => ['pipe', 'w']
        ];
        $env = array_merge($_ENV ?? [], ['MYSQL_PWD' => $db_pass]);

        $process = proc_open($mysqldumpCmd, $descriptors, $pipes, null, $env);

        if (!is_resource($process)) {
            log_message('error', 'Could not start mysqldump process.');
            // return redirect()->to('reports/exports')->with('error', 'Could not start mysqldump.');
        }

        // close stdin if opened
        if (isset($pipes[0])) { fclose($pipes[0]); }

        // capture stderr
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        if ($returnCode !== 0) {
            // cleanup partial file
            if (file_exists($sqlFile)) { unlink($sqlFile); }
            log_message('error', "mysqldump failed (code {$returnCode}): {$stderr}");
            // return redirect()->to('reports/exports')->with('error', 'Database dump failed.');
        }

        // optionally compress with gzip first (smaller than zip for SQL):
        $gzFile = $sqlFile . '.gz';
        $gzSuccessful = false;
        if (function_exists('gzopen')) {
            $in = fopen($sqlFile, 'rb');
            $out = gzopen($gzFile, 'wb9'); // best compression
            if ($in && $out) {
                while (!feof($in)) {
                    gzwrite($out, fread($in, 1024 * 512));
                }
                fclose($in);
                gzclose($out);
                $gzSuccessful = file_exists($gzFile);
            }
        }

        // Create zip and add either .gz (preferred) or .sql
        if (!extension_loaded('zip')) {
            // cleanup
            if (file_exists($sqlFile)) unlink($sqlFile);
            if (file_exists($gzFile)) unlink($gzFile);
            log_message('error', 'PHP Zip extension not available.');
            // return redirect()->to('reports/exports')->with('error', 'Zip extension not available.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE) !== true) {
            if (file_exists($sqlFile)) unlink($sqlFile);
            if (file_exists($gzFile)) unlink($gzFile);
            log_message('error', 'Failed to create zip: ' . $zipFile);
            // return redirect()->to('reports/exports')->with('error', 'Could not create zip.');
        }

        if ($gzSuccessful) {
            $zip->addFile($gzFile, basename($gzFile));
        } else {
            $zip->addFile($sqlFile, basename($sqlFile));
        }
        $zip->close();

        // Remove raw files to save space (keep only zip)
        if (file_exists($sqlFile)) unlink($sqlFile);
        if (file_exists($gzFile)) unlink($gzFile);

        // At this point $zipFile exists. Attach/send it.
        // send_single_email should accept attachment path; adjust if different
        $recipients = [
            'globalparking004@gmail.com',
            'hafizhassan229@gmail.com',
            'shiningwaseem2016@gmail.com'
        ];

        $subject = 'Database Backup';
        $message = 'Database backup attached.';
        $results = [];

        foreach ($recipients as $email) {
            $results[$email] = send_single_email($email, $subject, $message, $zipFile);
        }

        $primaryResult = $results['globalparking004@gmail.com'];

        if ($primaryResult === true || $primaryResult === 1) {
            echo 'Backup created and email sent.';
        } else {
            log_message('error', 'Email send failed: ' . print_r($primaryResult, true));
        }

        // Optional: log failed recipients
        foreach ($results as $email => $result) {
            if ($result !== true && $result !== 1) {
                log_message('error', "Email failed for {$email}: " . print_r($result, true));
            }
        }
    }

    // googleDrive
    public function upload_gd($fileName, $filePath)
    {
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            // Optional: upload to a specific folder
            'parents' => ['1ZOkG98YCi_hIP954hduS_dm59qK7nTyV']
        ]);

        $content = file_get_contents($filePath);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/sql',
            'uploadType' => 'multipart'
        ]);
        // echo'<pre>';print_r($file);die;
        return $file->id;
    }

    public function settings()
    {
        $sql_data="SELECT * FROM tbl_settings";
        $result=$this->db->query($sql_data)->getRow();
        $data=[
            "page_title"=>"Settings",
            'result'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('settings'),"title"=>"Settings","status"=>"","link"=>false]]
        ];
        return view('settings',$data);      
    }

    public function settings_save()
    {
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        if($data)
        {
            $smtphost=$data['SMTPHost'];
            $smtpuser=$data['SMTPUser'];
            $smtppass=$data['SMTPPass'];
            $smtpport=$data['SMTPPort'];
            $bccemail=$data['bccEmail'];
            $capacity_limit=$data['capacity_limit'];
            
            $query ="UPDATE `tbl_settings` SET `smtphost`='$smtphost',`smtpuser`='$smtpuser',`smtppass`='$smtppass',`smtpport`='$smtpport',`bccemail`='$bccemail',`capacity_limit`='$capacity_limit' WHERE `id`='$id'";
            $result=$this->db->query($query);
            if($result)
            {
                $result=['status'=>true,"message"=>"Settings successfully updated",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on update settings action",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function gopakistan()
    {
        $result=[];
        $data=[
            "page_title"=>"Go Pakistan",
            'roles'=>$result,      
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('gopakistan'),"title"=>"Go Pakistan","status"=>"","link"=>false]]
        ];
        // $cp = get_booking_capacity2();
        // pre($cp);
        // $this->export_database();
        return view('gopakistan',$data);      
    }

    public function gopakistan_get()
    {
        $externalUrl = 'https://rfhnlgkvqyjokujqrakg.supabase.co/functions/v1/requests-json';
        $apiPayloadKey = 'data'; // change if API returns records under a different key

        // Build request payload for external API (send search/paging if API supports it)
        $payload = [
            'search' => $this->request->getVar('search')['value'] ?? '',
            'start' => (int)($this->request->getVar('start') ?? 0),
            'length' => (int)($this->request->getVar('length') ?? 10),
            'draw' => (int)($this->request->getVar('draw') ?? 0)
        ];

        // POST to external API
        $ch = curl_init($externalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($curlErr)) {
            // return empty result on API error
            $output = [
                'draw' => $payload['draw'],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ];
            return $this->setResponseFormat('json')->respond($output);
        }

        $json = json_decode($response, true);
        if ($json === null) {
            $output = [
                'draw' => $payload['draw'],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ];
            return $this->setResponseFormat('json')->respond($output);
        }

        $records = $json[$apiPayloadKey] ?? $json; // fallback to root if data key missing
        $totalCount = isset($json['recordsTotal']) ? (int)$json['recordsTotal'] : count($records);

        $data = [];
        foreach ($records as $value) {
            // normalize keys (adjust if API uses different names)
            $name = $value['Name'] ?? ($value['Name'] ?? '');
            $email = $value['Email'] ?? '';
            $phone = $value['Phone'] ?? '';
            $services = $value['Services'] ?? ($value['Services'] ?? '');
            $arrival = $value['Arrival'] ?? null;
            $departure = $value['Departure'] ?? null;

            $row = [];
            $row[] = $name;
            $row[] = "<a href='mailto:".$email."'>".$email."</a>";
            $row[] = $phone;
            $row[] = $services;
            $row[] = $arrival ? date('d-m-Y', strtotime($arrival)) : '';
            $row[] = $departure ? date('d-m-Y', strtotime($departure)) : '';

            $safeEmail = htmlspecialchars($email, ENT_QUOTES);


            // $action = "<button class=\"btn btn-sm btn-primary\" onclick=\"send_email('{$encId}','{$safeEmail}')\">Send Email</button>";

            // $row[] = $action;
            $data[] = $row;
        }

        $output = [
            'draw' => (int)($payload['draw']),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $totalCount,
            'data' => $data
        ];

        array_walk_recursive($output, function (&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        });

        return $this->setResponseFormat('json')->respond($output);
    }


    public function reviews()
    {
        $data=[
            "page_title"=>"Reviews",
            "breadcrumb"=>[
                ["href"=>base_url('dashboard'),"title"=>"Home","status"=>"active","link"=>true],
                ["href"=>base_url('reviews'),"title"=>"Reviews","status"=>"","link"=>false]]
        ];

        $params = [
            ['JMXNPN', '19/09/2025', '3:45', '24/09/2025', '16:45', 37.09],
            ['JMXBHS', '17/10/2025', '4:15', '23/10/2025', '1:10', 42.47],
            ['JMWXTD', '04/07/2025', '7:55', '09/07/2025', '21:10', 41.27],
            ['JMWRQW', '26/06/2025', '13:00', '01/07/2025', '13:00', 38.88],
            ['JMSYJQ', '25/06/2025', '5:00', '28/06/2025', '11:30', 16.15],
            ['JMVLFB', '22/07/2025', '8:25', '29/07/2025', '18:30', 49.05],
            ['JMTNNF', '25/06/2025', '12:00', '12/07/2025', '10:00', 77.77],
            ['JMSRNH', '14/07/2025', '6:00', '24/07/2025', '14:00', 59.82],
            ['JMRVBY', '25/06/2025', '4:00', '30/06/2025', '20:00', 25.12],
            ['JMRRHK', '28/06/2025', '14:30', '03/07/2025', '16:00', 25.12],
            ['JMRKGP', '01/07/2025', '5:30', '11/07/2025', '8:30', 50.85],
            ['JMRDCB', '22/06/2025', '14:30', '05/07/2025', '0:30', 60.42],
            ['JMQSTK', '23/06/2025', '9:00', '24/06/2025', '18:30', 12.56],
            ['JNRTTW', '14/07/2025', '8:00', '18/07/2025', '17:00', 19.14],
            ['JNQBHP', '11/07/2025', '3:30', '15/07/2025', '9:00', 31.11],
            ['JNPVSH', '03/06/2026', '17:05', '10/06/2026', '19:10', 48.46],
            ['JHMQKL', '13/07/2025', '15:00', '17/07/2025', '23:40', 38.49],
            ['JNNNSP', '21/12/2025', '5:00', '28/12/2025', '14:05', 48.46],
            ['JNMMTD', '03/07/2025', '17:00', '21/07/2025', '11:00', 111.87],
            ['JNMMNB', '18/08/2025', '4:00', '23/08/2025', '0:00', 37.09],
            ['JNMMHD', '26/07/2025', '3:00', '05/08/2025', '17:30', 59.82],
            ['JNMHTK', '09/07/2025', '12:30', '23/07/2025', '17:00', 70.59],
            ['JNMHMS', '01/07/2025', '12:00', '08/07/2025', '23:30', 32.9],
        ];
        // foreach ($params as $row) {
        //     $depart_at = date('Y-m-d '.$row[2], strtotime($row[1]));
        //     $return_at = date('Y-m-d '.$row[4], strtotime($row[3]));
        //     $price = $row[5];
        //     $sql = "UPDATE `tbl_booking` SET depart_at='$depart_at', return_at='$return_at', price='$price', status=1 WHERE reference='$row[0]'";
        //     // pre($sql);
        //     $this->db->query($sql);
        // }

        // review_mail_send();
        $data['review_stats']= get_airport_review_stats();
        
        return view('general/reviews',$data);      
    }

    public function reviews_get()
    {
        $data = $this->request->getVar(); 
        $search=$this->request->getVar('search')['value'];

        $airport = $_GET['airport'] ? $_GET['airport'] : '';
        $rating = $_GET['rating'] ? $_GET['rating'] : '';

        $condition="";
        $table_map = [
            0 => 'b.reference',
            1 => 'b.airport',
            2 => 'br.rating',
        ];

        $SQLairport = "";
        if (trim($airport) != "" && trim($airport) != "*") {
            $SQLairport = " AND b.airport='$airport'";
        }

        $SQLrating = "";
        if (trim($rating) != "" && trim($rating) != "*") {
            $SQLrating = " AND br.rating='$rating'";
        }

        $sql_count="SELECT count(br.id) as total FROM tbl_booking_reviews br LEFT JOIN tbl_booking b ON b.reference=br.reference WHERE 1=1 AND br.status!=3 $SQLairport $SQLrating";
        $sql_data="SELECT br.*, b.airport, b. firstName, b.surname FROM tbl_booking_reviews br LEFT JOIN tbl_booking b ON b.reference=br.reference WHERE br.status!=3 $SQLairport $SQLrating";
        if(!empty($search))
        {
            foreach($table_map as $key => $val)
            {
                if($table_map[$key]=='b.reference')
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
        $OrderBy=" ORDER BY br.created_at DESC";
        // $SortBy=" ".$this->request->getVar('order')[0]['dir'];
        $SortBy=" ";
        $Limit=" LIMIT ".$this->request->getVar('start').",".$this->request->getVar('length');
        $sql_data.=$OrderBy.$SortBy.$Limit;
        
        // pre($sql_data);
        $result=$this->db->query($sql_data)->getResult();
        $data = array();

        foreach ($result as $value) 
        {   
            $row = array();
            $rating= 0;
            switch ($value->rating) {
                case 5:
                    $rating='<span class="text-warning" style="font-size: large">★★★★★</span>';
                    break;
                case 4:
                    $rating='<span class="text-warning" style="font-size: large">★★★★☆</span>';
                    break;
                case 3:
                    $rating='<span class="text-warning" style="font-size: large">★★★☆☆</span>';
                    break;
                case 2:
                    $rating='<span class="text-warning" style="font-size: large">★★☆☆☆</span>';
                    break;
                
                default:
                    $rating='<span class="text-warning" style="font-size: large">★☆☆☆☆</span>';
                    break;
            }
            $created_at = date("d-m-Y", strtotime($value->created_at));

            $row[] = $value->reference;
            $row[] = $value->airport;
            $row[] = $value->firstName.' '.$value->surname;
            $row[] = $rating;
            
            $row[] = substr($value->description,0,100).'...';
                   
            if ($value->status) {
               $row[] = '<span class="badge badge-glow bg-success">Published</span>';
            }else{
                $row[] = '<span class="badge badge-glow bg-danger">In-Active</span>';
            }
            $row[] = $created_at;
            $id=id_en($value->id);
            $action = "<div class=\"btn-group\">
                    <a href=\"javascript:void(0);\" class=\"btn btn-primary dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Actions
                    </a>
                    <div class=\"dropdown-menu\">                      
                      <a class=\"dropdown-item\" onclick=\"view_review(`$value->description`);\" href=\"javascript:void(0);\"><i data-feather='eye'></i> View Review</a>
                      <a class=\"dropdown-item\" onclick=\"publish_review(`$id`,1);\" href=\"javascript:void(0);\"><i data-feather='check-circle'></i> Publish Review</a>
                      <a class=\"dropdown-item\" onclick=\"publish_review(`$id`,0);\" href=\"javascript:void(0);\"><i data-feather='x-circle'></i> In-Active</a>
                      <a hidden class=\"dropdown-item\" onclick=\"delete_review(`$id`);\" href=\"javascript:void(0);\"><i data-feather='trash'></i> Delete</a>
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
        // Add this before sending the response
        array_walk_recursive($output, function(&$value) {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                // Or alternatively remove invalid characters:
                // $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
            }
        });
        return $this->setResponseFormat('json')->respond($output);
    }
    
    public function reviews_publish()
    {
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        if($data)
        {
            $status = $data['status'];
            $query ="UPDATE `tbl_booking_reviews` SET `status`='$status' WHERE `id`='$id'";
            $result=$this->db->query($query);
            if($result)
            {
                $result=['status'=>true,"message"=>"Review publish successfully",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on publish review",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }

    public function reviews_delete()
    {
        $data=$this->request->getVar();
        $id=id_de($data['id']);
        if($id)
        {
            $query ="UPDATE `tbl_booking_reviews` SET `status`=3 WHERE `id`='$id'";;
            // $query ="DELETE FROM `tbl_booking_reviews` WHERE `id`='$id'";
            $result=$this->db->query($query);
            if($result)
            {
                $result=['status'=>true,"message"=>"Review deleted successfully",'errors'=>null];
            }else{
                $result=['status'=>false,"message"=>"Unexpected error on delete review",'errors'=>null];
            }
        }
        return $this->response->setJSON($result);
    }
}