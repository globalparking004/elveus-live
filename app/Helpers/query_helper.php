<?php 
use CodeIgniter\Config\Services;

function send_otp_mail($email, $otp)
{
    $filename = APPPATH."/Helpers/templates/otp.html"; // Replace 'example.txt' with the path to your file
    $fileContent = file_get_contents($filename);
    if ($fileContent !== false) 
    {
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
    }

    if ($fileContent === false) {
        $message="";
    } else {
        // Process or display the file content as needed
        $message=$fileContent;
    }

    $subject="Your Login Verification Code";
    $message=str_replace("{otp}", $otp, $message);
    $message=str_replace("{web_name}", 'Globalparking', $message);

    send_review_email($email, $subject, $message,'globalparking');

    return true;
} 

function review_mail_send()
{
    $db = \Config\Database::connect();
    $today = date("Y-m-d");
    $yesterday = date("Y-m-d", strtotime("-1 day"));
    // AND c.status='returned'

    $sql = "SELECT c.*,b.reference,b.airport,b.source,b.email,b.firstName,b.surname FROM `tbl_booking_collect` c LEFT JOIN tbl_booking b ON b.id=c.booking_id WHERE (date(c.date_added)='$today' OR date(c.date_added)='$yesterday') AND TIMESTAMPDIFF(MINUTE, c.date_added, NOW()) >= 238 AND c.is_send_mail IS NULL";//after 3:55:00 


    $result = $db->query($sql)->getResult();
    // pre($result);
    foreach ($result as $key => $r) 
    {
        if ($r->source && isDomainName($r->source) && $r->email) {
            $sql = "SELECT * FROM `tbl_booking_reviews` WHERE reference='$r->reference'";
            $review = $db->query($sql)->getRow();

            $sql = "SELECT domain,web_name,email FROM `tbl_websites` WHERE domain='$r->source'";
            $website = $db->query($sql)->getRow();

            if (empty($review) && $website) 
            {
                // echo $r->source.', Email: '.$r->email.'<br>';
                $filename = APPPATH."/Helpers/templates/review.html"; // Replace 'example.txt' with the path to your file
                $fileContent = file_get_contents($filename);
                if ($fileContent !== false) 
                {
                    $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
                }

                if ($fileContent === false) {
                    $message="";
                } else {
                    // Process or display the file content as needed
                    $message=$fileContent;
                }
                $link = 'https://'.$r->source . '/review.php?ref=' . $r->reference;
                $reviewLink = anchor($link, $link, ['target' => '_blank']);

                $subject="We'd Love Your Feedback on Your Parking Experience";
                $message=str_replace("{firstName}", $r->firstName, $message);
                $message=str_replace("{surname}", $r->surname, $message);
                $message=str_replace("{review_link}", $reviewLink, $message);
                $message=str_replace("{web_name}", $website->web_name??'', $message);
                
                if(send_review_email($r->email, $subject, $message,$website->web_name)):
                // if(send_single_email('trendifyquery@gmail.com', $subject, $message)):
                    $sql_query = "UPDATE `tbl_booking_collect` SET `is_send_mail`=1 WHERE id='$r->id'";
                    $db->query($sql_query);
                    // echo'email send!!<br>';
                endif;
            }
        }
    }
    // reminder_mail_send();
   return true;
}

function reminder_review_mail_send()
{
    $db = \Config\Database::connect();
    $today = date("Y-m-d");

    $sql = "SELECT c.*,b.reference,b.airport,b.source,b.email,b.firstName,b.surname FROM `tbl_booking_collect` c 
            LEFT JOIN tbl_booking b ON b.id=c.booking_id 
            LEFT JOIN tbl_booking_reviews r ON r.reference=b.reference 
            WHERE TIMESTAMPDIFF(HOUR, c.date_added, NOW()) >=48 AND c.is_send_mail=1 AND r.reference IS NULL"; //set for after 2days AND c.status='returned'

    $result = $db->query($sql)->getResult();
    
    foreach ($result as $key => $r) {
        if (isDomainName($r->source) && $r->email) {
            $sql = "SELECT * FROM `tbl_booking_reviews` WHERE reference='$r->reference'";
            $review = $db->query($sql)->getRow();

            $sql = "SELECT domain,web_name,email FROM `tbl_websites` WHERE domain='$r->source'";
            $website = $db->query($sql)->getRow();

            if (empty($review)) 
            {
                // echo $r->source.', Email: '.$r->email.'<br>';
                $filename = APPPATH."/Helpers/templates/review.html"; // Replace 'example.txt' with the path to your file
                $fileContent = file_get_contents($filename);
                if ($fileContent !== false) 
                {
                    $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
                }

                if ($fileContent === false) {
                    $message="";
                } else {
                    // Process or display the file content as needed
                    $message=$fileContent;
                }
                $link = 'https://'.$r->source . '/review.php?ref=' . $r->reference;
                $reviewLink = anchor($link, $link, ['target' => '_blank']);

                $subject="We'd Love Your Feedback on Your Parking Experience";
                $message=str_replace("{firstName}", $r->firstName, $message);
                $message=str_replace("{surname}", $r->surname, $message);
                $message=str_replace("{review_link}", $reviewLink, $message);
                $message=str_replace("{web_name}", $website->web_name, $message);
                
                if(send_review_email($r->email, $subject, $message,$website->web_name)):
                // if(send_single_email('trendifyquery@gmail.com', $subject, $message)):
                    $sql_query = "UPDATE `tbl_booking_collect` SET `is_send_mail`=2 WHERE id='$r->id'";
                    $db->query($sql_query);
                    // echo'email send!!<br>';
                endif;
            }
        }
    }
   return true;
}

function set_password_email($firstName, $lastName,$email, $id)
{
    $filename = APPPATH."/Helpers/templates/password_set.html"; // Replace 'example.txt' with the path to your file
    $fileContent = file_get_contents($filename);

    if ($fileContent === false) {
        $message="";
    } else {
        // Process or display the file content as needed
        $message=$fileContent;
    }
    $set_password_link = base_url('set_password?email='. $email);
    $web_name = 'globalparking';

    $subject="Set Your Password";
    $message=str_replace("{firstName}", $firstName, $message);
    $message=str_replace("{lastName}", $lastName, $message);
    $message=str_replace("{set_password_link}", $set_password_link, $message);
    $message=str_replace("{web_name}", $web_name, $message);

    if(send_set_password_email($email, $subject, $message,$web_name)):
        return true;
    endif;
}

function isDomainName(string $source)
{
    return (bool) preg_match(
        '/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
        strtolower($source)
    );
}

function get_booking_capacity()
{
    $db = \Config\Database::connect();

    $airportCapacity = array(); // For storing combined data
    $depart_at = date('Y-m-d 23:59:00');
    $return_at = date('Y-m-d 00:01:00');

    $sql = "SELECT bk.operator_id, COALESCE(op.description, 'Unknown') AS op_name, COUNT(*) AS bookingCount, COALESCE(prod.all_capacity, 0) AS all_capacity, prod.capacity_threshold_one FROM tbl_booking AS bk 
            LEFT JOIN tbl_operators AS op ON op.id = bk.operator_id 
            LEFT JOIN ( SELECT p.operator_id, p.id AS product_id, SUM(p.capacity) AS all_capacity, MAX(p.capacity_threshold_one) AS capacity_threshold_one FROM tbl_products p 
            GROUP BY p.operator_id, p.id ) prod ON prod.operator_id = bk.operator_id AND prod.product_id = bk.product_id 
            WHERE bk.depart_at <= '$depart_at' AND bk.return_at > '$return_at' AND bk.status = '1' AND bk.show_status = 1 GROUP BY bk.operator_id, op.description ORDER BY op.description ASC";
    $result = $db->query($sql)->getResult(); 

    foreach ($result as $r) 
    {
        $all_capacity = $r->all_capacity;
        $capacity_threshold_one = $r->capacity_threshold_one;

        ////////////////////////////////////////////////////
        if(!empty($r->operator_id))
        {
            $sql_data_operator = "SELECT  *  FROM `tbl_operators` WHERE id=$r->operator_id";
            $sql_data_operator_res = $db->query($sql_data_operator)->getRow();
            $all_capacity = $sql_data_operator_res->capacity;
        }
        // $percentage=0;
        // if($all_capacity != 0 or !empty($all_capacity))
        // {
        //     $percentage = round(($r->bookingCount /$all_capacity)*100, 2);
        // }
        // if ($percentage >= $capacity_threshold_one) 
        // { 
        //     $badge = "badge badge-glow bg-warning"; 
        //     $bookingCount = "<span class='$badge'>" . $r->bookingCount . "</span>"; 
        // }else { 
        //     $badge = "badge badge-glow bg-success"; 
        //     $bookingCount = "<span class='$badge'>" . $r->bookingCount . "</span>"; 
        // }
        if ($r->bookingCount > 5) {
            // Prepare combined data
            $airportCapacity[] = array(
                'web_name' => $r->op_name,
                'booking_count' => $r->bookingCount,
                'airport_capacity' => $all_capacity
            );
        }
    }

    return $airportCapacity;
}

function get_booking_capacity2()
{
    $db = \Config\Database::connect();

    $airportCapacity = array(); // For storing combined data

    $sql = "SELECT short_code, web_name FROM tbl_websites GROUP BY short_code ORDER BY web_name ASC";
    $fetch_websites = $db->query($sql)->getResult();

    foreach ($fetch_websites as $website) {
        $short_code = $website->short_code;

        $sql = "SELECT SUM(capacity) AS sum FROM tbl_products WHERE parent='$short_code' AND adjust_prices_by_capacity=2";
        $result_product = $db->query($sql)->getResult();
        
        $DateFrom = date('Y-m-d');

        if (!empty($result_product) && $result_product[0]->sum != 0) {
            $airport_capacity = $result_product[0]->sum;

            $sql_data = "SELECT COUNT(*) AS bookingCount FROM tbl_booking WHERE (depart_at <= '$DateFrom 00:00:00' AND return_at > '$DateFrom 00:00:00') AND status='1' AND airport='$short_code'";
            $result_booking = $db->query($sql_data)->getRow();

            // Prepare combined data
            $airportCapacity[] = array(
                'web_name' => $website->web_name,
                'booking_count' => $result_booking->bookingCount,
                'airport_capacity' => $airport_capacity
            );
        }
    }

    return $airportCapacity;
}

function get_profitAvg($supplier='')
{
   $options=array(); 
    $db = \Config\Database::connect();
    $today = date("Y-m-d");
    $profit = 0; $avg=0;

    $sql = "SELECT sum(price) as profit,avg(price) as average FROM `tbl_booking` WHERE date(booked_at)='$today' AND status='1'";
    if ($supplier==2) {
    	$sql = "SELECT sum(price) as profit,avg(price) as average FROM `tbl_booking` WHERE date(booked_at)='$today' AND status='1' AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP'  OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu' OR source='skyparkingservices.co.uk')";
    }elseif ($supplier==1) {
        $sql = "SELECT sum(price) as profit,avg(price) as average FROM `tbl_booking` WHERE date(booked_at)='$today' AND status='1' AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source NOT IN ('CPD' ,'CTAP' ,'P4U', 'Holiday Extras','ParkVia','Park&Fly','FreeToMove','Airport Parking With Us','JBF','Cash Booking','YTE' ,'HCP' ,'CYP', 'https://longtermparking.ie/','www.ca.vu','skyparkingservices.co.uk') ";
    }
    $result = $db->query($sql)->getRow();
    if ($result) {
        $profit = $result->profit;
        $avg = $result->average;
    }
   return [$profit, $avg];
}

function get_total_bookings($status='',$supplier='')
{
   $options=array();
    $db = \Config\Database::connect();
    $today = date("Y-m-d");
    $total_bookings = 0;
 
    $sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(booked_at)='$today'";
    if ($status) {
    	$sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(booked_at)='$today' AND status='$status'";
    }elseif ($supplier == 1) { 
    	$sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(booked_at)='$today' AND (source ='Dashboard' OR source NOT LIKE '%Dashboard') AND source NOT IN ('CPD' ,'CTAP' ,'P4U', 'Holiday Extras','ParkVia','Park&Fly','FreeToMove','Airport Parking With Us','JBF','Cash Booking','YTE' ,'HCP' ,'CYP', 'https://longtermparking.ie/','www.ca.vu', 'skyparkingservices.co.uk') ";
    }elseif ($supplier == 2) {
        $sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(booked_at)='$today' AND status=1 AND (reference IS NOT NULL AND reference !='') AND reference NOT LIKE 'GL-%' AND reference NOT LIKE 'GL %' AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP' OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu' OR source='skyparkingservices.co.uk') ";
    }
    
    if ($status && $supplier)
    {
    	$sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(booked_at)='$today' AND status='$status' AND (reference IS NOT NULL AND reference !='') AND reference NOT LIKE 'GL-%' AND reference NOT LIKE 'GL %' AND (source='CPD' OR source='P4U' OR source='Holiday Extras' OR source='ParkVia' OR source='FreeToMove' OR (source LIKE '%Dashboard' AND source!='Dashboard') OR source='CTAP' OR source='Park&Fly' OR source='Airport Parking With Us' OR source='JBF' OR source='Cash Booking' OR source='YTE' OR source='HCP' OR source='CYP' OR source ='https://longtermparking.ie/' OR source='Go Comparison' OR source='www.ca.vu' OR source='skyparkingservices.co.uk') ";
    }
    $result = $db->query($sql)->getResult();
    if ($result) {
        $total_bookings = $result[0]->total_bookings;
    }
   return $total_bookings;
}

function get_airports()
{
   $options=array();
    $db = \Config\Database::connect();

    $sql="SELECT short_code, airport_name FROM  tbl_websites ORDER BY short_code";
    $result =  $db->query($sql)->getResult();
    foreach ($result as $key => $r) {
        $options[$r->short_code] = $r->airport_name.' ('.$r->short_code.')';
    }
   return $options;
}

function filter_uncollected_bookings($bookings,$type)
{
    $db = \Config\Database::connect();

    $bookingIds = array_column($bookings, 'id');

    if (empty($bookingIds)) {
        return $bookings;
    }
    $status = 'collected';
    if ($type=='return_at') {
        $status='returned';
    }

    // Get collected booking IDs
    $collectedRows = $db->table('tbl_booking_collect')
        ->select('booking_id')
        ->whereIn('booking_id', $bookingIds)
        ->where('status', $status)
        ->get()
        ->getResultArray();

    $collectedIds = array_column($collectedRows, 'booking_id');
    $filteredBookings = array_filter($bookings, function ($booking) use ($collectedIds) {
        $booking = (array) $booking;
        return !in_array($booking['id'], $collectedIds);
    });
    // pre($filteredBookings);
    return $filteredBookings;
}

// Email
function send_single_email($to_emails,$subject,$message, $attachment_path = null)
{ 
    $email = \Config\Services::email();
    $db = \Config\Database::connect();


    $sql_data="SELECT * FROM tbl_settings";
    $settings=$db->query($sql_data)->getRow();

    $config['protocol']='smtp';
    $config['SMTPHost']=$settings->smtphost;//smtp.hostinger.com
    $config['SMTPUser']=$settings->smtpuser;
    $config['SMTPPass']=$settings->smtppass;
    $config['SMTPPort']=$settings->smtpport;// SMTP port, commonly 587 for TLS, or 465 for SSL
    $config['SMTPCrypto']='tls';// 'tls' or 'ssl'

    $email->initialize($config);
    $email->setFrom($settings->smtpuser, 'Global Parking Management');
    // $email->setFrom($from, 'Global Parking Management');
      // If $to_emails is array, set all recipients
    // if (is_array($to_emails)) {
    //     $email->setTo($to_emails);
    // } else {
    //     $email->setTo($to_emails);
    // }
    $email->setTo($to_emails);

    $email->setSubject($subject);
    $email->setMessage($message);
    // Attach SQL file if provided
    if ($attachment_path && file_exists($attachment_path)) {
        $email->attach($attachment_path);
    }

    if ($email->send()) 
    {
        if ($settings->bccemail) {  
            // $email->setBCC($settings->bccemail);
            send_bccmail($settings->bccemail, $subject, $message,'Global Parking Management');
        }
        return true;
    } else {       
        // Print debug info to logs
        $debug = $email->printDebugger(['headers', 'subject', 'body']);
        log_message('error', "Email failed: " . $debug);

        // Optionally, you can echo it to see immediately (for dev environment)
        // echo "<pre>Email Debug Info:\n" . $debug . "</pre>";
        return false;
    }
}

function send_set_password_email($to,$subject,$message,$fromName)
{ 
    $email = \Config\Services::email();
    $db = \Config\Database::connect();

    $sql_data="SELECT * FROM tbl_settings";
    $settings=$db->query($sql_data)->getRow();

    $config = [
        'protocol'  => 'smtp',
        'SMTPHost'  => $settings->smtphost,
        'SMTPUser'  => $settings->smtpuser,
        'SMTPPass'  => $settings->smtppass,
        'SMTPPort'  => $settings->smtpport,
        'SMTPCrypto'=> 'tls', // use 'ssl' if port 465
        'mailType'  => 'html',
        'charset'   => 'utf-8',
        'wordWrap'  => true,
    ];

    $email->initialize($config);
    $email->setFrom($settings->smtpuser, $fromName);
    $email->setTo($to);

    $email->setSubject($subject);
    $email->setMessage($message);

    if ($email->send()) 
    {
        return true;
    } else {    
        // Get the error message
        // echo $email->printDebugger(['headers', 'subject', 'body']);
        return false;
    }
}

function send_review_email($to,$subject,$message,$fromName)
{ 
    $email = \Config\Services::email();
    $db = \Config\Database::connect();


    $sql_data="SELECT * FROM tbl_settings";
    $settings=$db->query($sql_data)->getRow();

    $config['protocol']='smtp';
    $config['SMTPHost']=$settings->smtphost;//smtp.hostinger.com
    $config['SMTPUser']=$settings->smtpuser;
    $config['SMTPPass']=$settings->smtppass;
    $config['SMTPPort']=$settings->smtpport;// SMTP port, commonly 587 for TLS, or 465 for SSL
    $config['SMTPCrypto']='tls';// 'tls' or 'ssl'

    $email->initialize($config);
    $email->setFrom($settings->smtpuser, $fromName);
    $email->setTo($to);

    $email->setSubject($subject);
    $email->setMessage($message);
    $email->setMailType('html');

    if ($email->send()) 
    {
        return true;
    } else {    
        // Get the error message
        echo 'Email sending failed: ';print_r($email);

        return false;
    }
}

// if (!mail($to, $subject, $message, $headers)) {
//     error_log("Mail sending failed: " . print_r(error_get_last(), true));
//     return false;
// }
// return true

function send_email_port($to, $subject, $id, $from)
{
    $email = \Config\Services::email();
    $db = \Config\Database::connect();

    $sql_data="SELECT * FROM tbl_settings";
    $settings=$db->query($sql_data)->getRow();


    //exit(APPPATH."/Helpers/templates/confirm.html");


    $filename = APPPATH."/Helpers/templates/confirm_port.html"; // Replace 'example.txt' with the path to your file
    $fileContent = file_get_contents($filename);
    if ($fileContent !== false) 
    {
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
    }

    if ($fileContent === false) {
        $message="";
    } else {
        // Process or display the file content as needed
        $message=$fileContent;
    }


    $sql="SELECT * FROM  tbl_booking WHERE id='$id' LIMIT 1";
    $result =  $db->query($sql)->getResult();

    $query =  "SELECT bad.*, pad.addon_name FROM `tbl_booking_addons` bad LEFT JOIN `tbl_product_addons` pad ON bad.addon_id=pad.id WHERE bad.booking_id='$id'";
    $addons = $db->query($query)->getResult();
    $addon_name='';
    if ($addons) {
        foreach ($addons as $key => $ad) {
            $addon_name .= $ad->addon_name.', ';
        }
    }
    if($result)
    {
        $result=$result[0];
    }
    
    $product=get_product($result->product_id);
       
    $message=str_replace("{price}", $result->price, $message);
    $message=str_replace("{product_name}", $product->name, $message);
    $message=str_replace("{firstName}", $result->firstName, $message);
    $message=str_replace("{surname}", $result->surname, $message);
    $message=str_replace("{email}", $result->email, $message);
    $message=str_replace("{reference}", $result->reference, $message);
    $message=str_replace("{OutTerminal}", $result->OutTerminal, $message);
    $message=str_replace("{RetTerminal}", $result->RetTerminal, $message);
    $message=str_replace("{OutFltNo}", $result->OutFltNo, $message);


    // $datetime1 = new DateTime($result->depart_at);
    // $datetime2 = new DateTime($result->return_at);
    // $interval = $datetime1->diff($datetime2);
    // $daysDifference = $interval->days;
    $daysDifference = getDaysBetweenDates($result->depart_at, $result->return_at);


    $message=str_replace("{total_days}", $daysDifference, $message);

    $message=str_replace("{addons}", $addon_name, $message);
    $message=str_replace("{contactNumber}", $result->contactNumber, $message);
    $message=str_replace("{carMake}", $result->carMake, $message);
    $message=str_replace("{carModel}", $result->carModel, $message);
    $message=str_replace("{carColour}", $result->carColour, $message);
    $message=str_replace("{carReg}", $result->carReg, $message);
    $airports=get_airports();
    $airport=$airports[$result->airport];
    $message=str_replace("{airport}", $airport, $message);
    $message=str_replace("{depart_at}", date("l, F j, Y g:i A", strtotime($result->depart_at)), $message);
    $message=str_replace("{return_at}", date("l, F j, Y g:i A", strtotime($result->return_at)), $message);

    $useful_information = $product->useful_information;
    $driver_contact = $product->driver_contact;
    $parking_facility_contact = $product->parking_facility_contact;
    $what_to_do_when_you_arrive = $product->what_to_do_when_you_arrive;
    $what_to_do_when_you_return = $product->what_to_do_when_you_return;
    $security_information = $product->security_information;

    // if ($result->airport == 'SOU')
    // {
    //     $dateString = $result->depart_at;
    //     $date = strtotime($dateString);
    //     $dayName = strtolower(date('l', $date));
    //     $formated_arrive_date = date('Y-m-d', $date);
    //     $arrivalTime = date('H:i:s', strtotime($result->depart_at));
    //     $formatted_departureTime_Time = $this->formatTime($arrivalTime);

    //     $cal_capacity = "SELECT count(*) as count FROM `tbl_booking` WHERE  `product_id`= $result->product_id and (`depart_at`<= '$formated_arrive_date 23:59:00' AND return_at>'$formated_arrive_date $formatted_departureTime_Time:00')  and status='1'";
    //     $cal_capacity_r = $db->query($cal_capacity)->getRow();
    //     $cal_capacity_result = $cal_capacity_r->count;
    //     print_r($cal_capacity);die('capacityCount');

        
    //     if ($settings->capacity_limit > $cal_capacity_result) 
    //     {
    //         $sql_query = "SELECT * FROM `tbl_product_email_config` WHERE product_id='$result->product_id'";
    //         $emailConfig = $db->query($sql_query)->getRow();
    //         print_r($emailConfig);die('emailConfig');
    //         if ($emailConfig) 
    //         {
    //             $useful_information = $emailConfig->useful_information;
    //             $parking_facility_contact = $emailConfig->parking_facility_contact;
    //             $what_to_do_when_you_arrive = $emailConfig->what_to_do_when_you_arrive;
    //             $what_to_do_when_you_return = $emailConfig->what_to_do_when_you_return;
    //             $security_information = $emailConfig->security_information;
    //         }
    //     }
    // }
    
    $message=str_replace("{useful_information}", $useful_information, $message);
    $message=str_replace("{driver_contact}", $driver_contact, $message);
    $message=str_replace("{parking_facility_contact}", $parking_facility_contact, $message);
    $message=str_replace("{what_to_do_when_you_arrive}", $what_to_do_when_you_arrive, $message);
    $message=str_replace("{what_to_do_when_you_return}", $what_to_do_when_you_return, $message);
    $message=str_replace("{security_information}", $security_information, $message);

    $config['protocol']='smtp';
    $config['SMTPHost']=$settings->smtphost;
    $config['SMTPUser']=$settings->smtpuser;//$settings->smtpuser
    $config['SMTPPass']=$settings->smtppass;//$settings->smtppass
    $config['SMTPPort']=$settings->smtpport;// SMTP port, commonly 587 for TLS, or 465 for SSL. $settings->smtpport
    $config['SMTPCrypto']='tls';// 'tls' or 'ssl'

    $email->initialize($config);

    $email->setFrom($from, 'Port Parking');
    $email->setTo($to);
        
    $email->setSubject($subject);
    $email->setMessage($message);
    if ($email->send()) {
        if ($settings->bccemail) {  
            // $email->setBCC($settings->bccemail);
            send_bccmail($settings->bccemail, $subject, $message,'Port Parking');
        }
        return true;
    } else {
        //echo 'Email sending failed. Error message: ' . $email->printDebugger();
        return false;
    }
}

function send_email($to,$subject,$id, $from,$webtype)
{
    $db = \Config\Database::connect();

    $sql="SELECT * FROM  tbl_booking WHERE id='$id' LIMIT 1";
    $result =  $db->query($sql)->getResult();
    $airport="";
    if($result)
    {
        $airport=$result[0]->airport;
    }
    $webtype = strtolower($webtype);
    // print_r($to);die;
    if($airport=="SOP" || $webtype!="airport")
    {
        return send_email_port($to,$subject,$id,$from);
    }else{
        return send_email_airport($to,$subject,$id,$from);
    }
}

function send_email_airport($to, $subject, $id, $from)
{
    $email = \Config\Services::email();
    $db = \Config\Database::connect();


    $sql_data="SELECT * FROM tbl_settings";
    $settings=$db->query($sql_data)->getRow();


    //exit(APPPATH."/Helpers/templates/confirm.html");

 
    $filename = APPPATH."/Helpers/templates/confirm.html"; // Replace 'example.txt' with the path to your file
    $fileContent = file_get_contents($filename);
    if ($fileContent !== false) 
    {
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1');
    }

    if ($fileContent === false) {
        $message="";
    } else {
        // Process or display the file content as needed
        $message=$fileContent;
    }


    $sql="SELECT * FROM  tbl_booking WHERE id='$id' LIMIT 1";
    $result =  $db->query($sql)->getResult();
    if($result)
    {
        $result=$result[0];
    }
    
    $product=get_product($result->product_id);
    

    $currency = ($result->airport == 'DUB') ? '€' : ( ($result->airport == 'DXB') ? 'AED' : '£' );
    $price = $currency.$result->price;

    $message=str_replace("{price}", $price, $message);
    $message=str_replace("{product_name}", $product->name, $message);
    $message=str_replace("{firstName}", $result->firstName, $message);
    $message=str_replace("{surname}", $result->surname, $message);
    $message=str_replace("{email}", $result->email, $message);
    $message=str_replace("{reference}", $result->reference, $message);
    $message=str_replace("{OutTerminal}", $result->OutTerminal, $message);
    $message=str_replace("{RetTerminal}", $result->RetTerminal, $message);
    $message=str_replace("{OutFltNo}", $result->OutFltNo, $message);


    // $datetime1 = new DateTime($result->depart_at);
    // $datetime2 = new DateTime($result->return_at);
    // $interval = $datetime1->diff($datetime2);
    // $daysDifference = $interval->days;
    $daysDifference = getDaysBetweenDates($result->depart_at, $result->return_at);


    $message=str_replace("{total_days}", $daysDifference, $message);

    $message=str_replace("{contactNumber}", $result->contactNumber, $message);
    $message=str_replace("{carMake}", $result->carMake, $message);
    $message=str_replace("{carModel}", $result->carModel, $message);
    $message=str_replace("{carColour}", $result->carColour, $message);
    $message=str_replace("{carReg}", $result->carReg, $message);
    $airports=get_airports();
    $airport=$airports[$result->airport];
    $message=str_replace("{airport}", $airport, $message);
    $message=str_replace("{depart_at}", date("l, F j, Y g:i A", strtotime($result->depart_at)), $message);
    $message=str_replace("{return_at}", date("l, F j, Y g:i A", strtotime($result->return_at)), $message);

    $message=str_replace("{useful_information}", $product->useful_information, $message);
    $message=str_replace("{driver_contact}", $product->driver_contact, $message);
    $message=str_replace("{parking_facility_contact}", $product->parking_facility_contact, $message);
    $message=str_replace("{what_to_do_when_you_arrive}", $product->what_to_do_when_you_arrive, $message);
    $message=str_replace("{what_to_do_when_you_return}", $product->what_to_do_when_you_return, $message);
    $message=str_replace("{security_information}", $product->security_information, $message);

    $config['protocol']='smtp';
    $config['SMTPHost']=$settings->smtphost;//smtp.hostinger.com
    $config['SMTPUser']=$settings->smtpuser;//$settings->smtpuser no_reply@parkingmanagment.com
    $config['SMTPPass']=$settings->smtppass;//$settings->smtppass Hakuyasha@123
    $config['SMTPPort']=$settings->smtpport;// SMTP port, commonly 587 for TLS, or 465 for SSL.$settings->smtpport
    $config['SMTPCrypto']='tls';// 'tls' or 'ssl'

    // print_r($message);die;
    
    $email->initialize($config);

    $email->setFrom($from, 'Airport Parking');
    $email->setTo($to);
    
    $email->setSubject($subject);
    $email->setMessage($message);
    if ($email->send()) {
        if ($settings->bccemail) {
            // $email->setBCC($settings->bccemail);
            send_bccmail($settings->bccemail, $subject, $message, 'Airport Parking');
        }
        return true;
    } else {
        // echo 'Email sending failed. Error message: ' . $email->printDebugger();die;
        return false;
    }
}

function send_bccmail($to, $subject, $message, $fromName)
{
    $email = \Config\Services::email();

    $config['protocol']='smtp';
    $config['SMTPHost']='smtp.hostinger.com';//smtp.hostinger.com
    $config['SMTPUser']='no_reply@parkingmanagment.com';//$settings->smtpuser no_reply@parkingmanagment.com
    $config['SMTPPass']='Hakuyasha@123';//$settings->smtppass Hakuyasha@123
    $config['SMTPPort']='587';// SMTP port, commonly 587 for TLS, or 465 for SSL.$settings->smtpport
    $config['SMTPCrypto']='tls';// 'tls' or 'ssl'

    $email->initialize($config);

    $email->setFrom('no_reply@parkingmanagment.com', $fromName);
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage($message);

    if ($email->send()) {
        return true;
    } else {
        echo 'BCC Email sending failed. Error message: ' . $email->printDebugger();
        return false;
    }
}

function get_operator($id)
{   
    $db = \Config\Database::connect();
    $sql="SELECT * FROM tbl_operators WHERE id='$id' LIMIT 1";
    $result =  $db->query($sql)->getResult();
    if($result)
    {
        $result=$result[0];
    }
    return $result;
}

function get_product($id)
{   
    $db = \Config\Database::connect();
    $sql="SELECT * FROM tbl_products WHERE id='$id' LIMIT 1";
    $result =  $db->query($sql)->getResult();
    if($result)
    {
        $result=$result[0];
    }
    return $result;
}

function get_website_type($code)
{
    $db = \Config\Database::connect();
    $sql="SELECT * FROM tbl_websites WHERE code='$code' LIMIT 1";
    $result =  $db->query($sql)->getResult();
    if($result)
    {
        $result=$result[0]->type;
    }
    return $result;
}

function get_account_bookings($dateFrom,$dateTo, $airport)
{
    $db = \Config\Database::connect();

    $SQLFilterDate = "and date(booked_at) BETWEEN '$dateFrom' AND '$dateTo'";
    $SQLref = " AND (reference IS NOT NULL AND reference !='') AND reference LIKE 'GL-%' ";
    $SQLairport = "AND airport='$airport'";
    $SQLstatus = 'AND status=1';

    $sql_query = "SELECT id,reference, airport,price,google_cost  FROM `tbl_booking`  WHERE 1=1  $SQLFilterDate $SQLstatus $SQLairport $SQLref";
    $bookings = $db->query($sql_query)->getResult();

    return $bookings;
}

function identify_low_price($depart_at,$return_at,$product_id,$price)
{
    $db = \Config\Database::connect();
    $priceOrignal='';
    $priceMargin='';
    $rowClass='';
    $linked_price=0;

    $sql_data = "SELECT id,product_code,name,linked_price,linked_product_code FROM `tbl_products` WHERE `id` = $product_id";
    $resultPro = $db->query($sql_data)->getRow();
    $linked_price= $resultPro->linked_price;
 
    if ($resultPro->linked_product_code) 
    {
        $sql_data = "SELECT id,product_code,name FROM `tbl_products` WHERE `product_code` = '$resultPro->linked_product_code'";
        $resultPro = $db->query($sql_data)->getRow();
    }
    

    $dayName = strtolower(date('l', strtotime($depart_at)));
    $formated_arrive_date = date('Y-m-d', strtotime($depart_at));
    $sql_data = "SELECT * FROM `tbl_ranges` WHERE `product_id` = '$resultPro->id' AND (`dfrom` <= '$formated_arrive_date' AND `dto` >= '$formated_arrive_date') limit 1";
    $resultR = $db->query($sql_data)->getRow();
    // echo'day: '.$dayName;
    // pre($resultR);
    if (isset($resultR->$dayName) && !empty($resultR->$dayName)) 
    {
        $range = $resultR->$dayName;
        $date1 = new \DateTime($depart_at);
        $date2 = new \DateTime($return_at);
        $interval = $date1->diff($date2);
        $number_of_days = $interval->format('%a') + 1;

        $sql_data = "SELECT (`day_rate`) as price FROM `tbl_product_band` WHERE `master_id` =$range AND `name`=$number_of_days";
        $resultP = $db->query($sql_data)->getRow();
        // echo'priceAr'.$price;print_r($resultP);die;
        if ($resultP) {
            $price2 = $resultP->price;//2 static add for website price
            $priceOrignal = $price2+$linked_price; 
            // $priceOrignal = $resultP->price; 
            if ($price2 > $price) {
                $priceMargin = round($resultP->price-$price,2);
                $rowClass = 'low-price';
            } 
        }
    }

    return [$rowClass, $priceOrignal, $priceMargin];
}

function identify_repeated_customer($email)
{
    $db = \Config\Database::connect();
    $rowClass = '';
    $sql_data = "SELECT id,airport,email,firstName,surname FROM `tbl_booking` WHERE `email` = '$email' HAVING COUNT(email) > 1 ";
    $result = $db->query($sql_data)->getRow();
    // pre($result);
    if ($result) {
        $rowClass='row_repeated';
    }
    return $rowClass;
}

function get_airport_review_stats()
{
    $db = \Config\Database::connect();
    
    // Query to get counts for each review type (assuming 1-5 star ratings) 
    $sql = "SELECT b.airport,
            COUNT(br.id) as total_reviews,
            SUM(CASE WHEN br.rating = 1 THEN 1 ELSE 0 END) as one_star,
            SUM(CASE WHEN br.rating = 2 THEN 1 ELSE 0 END) as two_stars,
            SUM(CASE WHEN br.rating = 3 THEN 1 ELSE 0 END) as three_stars,
            SUM(CASE WHEN br.rating = 4 THEN 1 ELSE 0 END) as four_stars,
            SUM(CASE WHEN br.rating = 5 THEN 1 ELSE 0 END) as five_stars,
            AVG(br.rating) as average_rating
            FROM tbl_booking_reviews br LEFT JOIN tbl_booking b ON b.reference=br.reference
            WHERE br.status!=3 GROUP BY b.airport";
    
    $result = $db->query($sql)->getResult();
    // pre($result);
    if (!$result) {
        return [
            'airport' => '',
            'total_reviews' => 0,
            'one_star' => 0,
            'two_stars' => 0,
            'three_stars' => 0,
            'four_stars' => 0,
            'five_stars' => 0,
            'average_rating' => 0
        ];
    }
    
    return $result;
}

function update_ref2($reference) 
{
    $db = \Config\Database::connect();
    $afterDash = preg_replace('/^.*-/', '', $reference);
    $ref2 = preg_replace('/^[A-Za-z]+/', '', $afterDash);
    if(ctype_alpha($afterDash)) {
        $ref2 = $afterDash;
    }
    if (ctype_alpha($reference)) {
        $ref2 = $reference;
    }
    

    $sql = "UPDATE `tbl_booking` SET reference2='$ref2' WHERE reference='$reference'";
    $db->query($sql);
    return true;
}

function update_fb_source($promocode) 
{
    $db = \Config\Database::connect();
    
    if ($promocode== 'FB10' || $promocode == 'FB20' || $promocode=='FAB10') {
        $sql = "UPDATE `tbl_booking` SET traffic_source='facebook' WHERE promocode='$promocode'";
        $db->query($sql);
    }

    
    return true;
}


function logActivity($userId, $reference, $action, $details = null)
{
    $db = \Config\Database::connect();
    $builder = $db->table('activity_logs');

    $data = [
        'user_id'    => $userId,
        'reference'     => $reference,
        'action'     => $action,
        'details'    => $details,
    ];

    $builder->insert($data);

    return $db->insertID(); // Return the log ID
}

function user_login($userId, $email)
{
    $db = \Config\Database::connect();
    $request = Services::request();

    $loginTime = date("Y-m-d H:i:s"); 
    // $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $agent   = $request->getUserAgent();
    $userAgent = $agent->getAgentString();
    $ua = strtolower($agent->getAgentString());
    // Default
    $device = 'Desktop';
    $mobileName= null;

    // Tablet detection (common keywords)
    if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false || strpos($ua, 'kindle') !== false) {
        $device = 'Tablet';
    } elseif ($agent->isMobile()) {
        $device = 'Mobile';
        $mobileName = $agent->getMobile(); // Mobile brand/model
    }

    $browser = $agent->getBrowser();
    $platform = $agent->getPlatform();

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $details = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
    $location = $details->city . ", " . $details->country;


    $builder = $db->table('user_logins');
    $data = [
        'user_id'       => $userId,
        'email'         => $email,
        'ip_address'    => $ip,
        // 'user_agent'    => $userAgent,
        'device'        => $mobileName.' '.$device.', '.$browser.', '.$platform,
        'location'      => $location,
        'login_time'    => $loginTime,
    ];
    // pre($data);

    $builder->insert($data);

    return $db->insertID(); // Return the log ID
}
