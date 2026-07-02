<?php
function id_en($id)
{
   $id=base64_encode(\Config\Services::encrypter()->encrypt($id));
   return $id;
}

function id_de($id)
{
	$id = base64_decode($id);        
   return \Config\Services::encrypter()->decrypt($id);
}

function pre($params) {
    echo'<pre>';print_r($params);die;
}

function get_menu($node1,$node2)
{  

   $AUTH=session()->get('AUTH');
   if($AUTH['role_id']=="3" || $AUTH['role_name'] == 'Driver')
   {      
        $has_booking_sub[]=['href'=>"view","feather"=>"circle","text"=>"Bookings"];
        $menu[]=['href'=>"bookings","feather"=>"bookmark","text"=>"Bookings","has_sub"=>$has_booking_sub];
   }elseif($AUTH['role_name']=="DRT")
   {      
        $menu[]=['href'=>"drivers","feather"=>"users","text"=>"Drivers","has_sub"=>[]];
        $has_booking_sub[]=['href'=>"driver/view","feather"=>"circle","text"=>"Departure Return"];
        // $has_booking_sub[]=['href'=>"driver/viewb","feather"=>"circle","text"=>"Beta DepartReturn"];
        $has_booking_sub[]=['href'=>"view","feather"=>"circle","text"=>"Bookings"];
        $menu[]=['href'=>"bookings","feather"=>"bookmark","text"=>"Bookings","has_sub"=>$has_booking_sub];

        $has_new_sub[]=['href'=>"passenger","feather"=>"circle","text"=>"Passenger"];
        $has_new_sub[]=['href'=>"bookings_count","feather"=>"circle","text"=>"Bookings"];
        $menu[]=['href'=>"reports","feather"=>"file-text","text"=>"Reports","has_sub"=>$has_new_sub];

        // $has_inte_sub[]=['href'=>"clicksend","feather"=>"circle","text"=>"ClickSend"];
        // $has_new_sub[]=['href'=>"bookings_count","feather"=>"circle","text"=>"Email"];
        // $menu[]=['href'=>"integration","feather"=>"link","text"=>"Integration","has_sub"=>$has_inte_sub];
        
   }elseif($AUTH['role_name']=="CSR")
   {
        $menu[]=['href'=>"dashboard","feather"=>"home","text"=>"Home","has_sub"=>[]];

        $has_booking_sub[]=['href'=>"add","feather"=>"circle","text"=>"Add Booking"];
        $has_booking_sub[]=['href'=>"view","feather"=>"circle","text"=>"Bookings"];

        $menu[]=['href'=>"bookings","feather"=>"bookmark","text"=>"Bookings","has_sub"=>$has_booking_sub];

        $menu[]=['href'=>"promotion/view","feather"=>"dollar-sign","text"=>"Promotions","has_sub"=>[]];
        $menu[]=['href'=>"products","feather"=>"shopping-bag","text"=>"Products","has_sub"=>[]];

        $has_sub[]=['href'=>"bookings/capacity","feather"=>"circle","text"=>"Bookings Capacity"];
        $has_sub[]=['href'=>"all_bookings","feather"=>"circle","text"=>"All Bookings"];
        $has_sub[]=['href'=>"performance","feather"=>"circle","text"=>"Performance"];
        $has_sub[]=['href'=>"aff-performance","feather"=>"circle","text"=>"Aff. Performance"];
        $has_sub[]=['href'=>"departure_return","feather"=>"circle","text"=>"Departure Return"];
        $menu[]=['href'=>"reports","feather"=>"file-text","text"=>"Reports","has_sub"=>$has_sub]; 

        $menu[]=['href'=>"domains","feather"=>"globe","text"=>"Domains","has_sub"=>[]];
        $menu[]=['href'=>"prices","feather"=>"dollar-sign","text"=>"Prices","has_sub"=>[]];
        $menu[]=['href'=>"reviews","feather"=>"file-text","text"=>"Reviews","has_sub"=>[]];

   }elseif($AUTH['role_name']=="Pricing")
   {
        $menu[]=['href'=>"products","feather"=>"shopping-bag","text"=>"Products","has_sub"=>[]];

        $has_sub[]=['href'=>"bookings/capacity","feather"=>"circle","text"=>"Bookings Capacity"];
        $has_sub[]=['href'=>"all_bookings","feather"=>"circle","text"=>"All Bookings"];
        $has_sub[]=['href'=>"performance","feather"=>"circle","text"=>"Performance"];
        $has_sub[]=['href'=>"aff-performance","feather"=>"circle","text"=>"Aff. Performance"];
        $has_sub[]=['href'=>"departure_return","feather"=>"circle","text"=>"Departure Return"];
        $menu[]=['href'=>"reports","feather"=>"file-text","text"=>"Reports","has_sub"=>$has_sub]; 

        $menu[]=['href'=>"prices","feather"=>"dollar-sign","text"=>"Prices","has_sub"=>[]];

   }elseif($AUTH['role_name']=="Operator")
   {
        $has_booking_sub[]=['href'=>"view","feather"=>"circle","text"=>"Bookings"];
        $menu[]=['href'=>"bookings","feather"=>"bookmark","text"=>"Bookings","has_sub"=>$has_booking_sub];

   }else{
        $menu[]=['href'=>"dashboard","feather"=>"home","text"=>"Home","has_sub"=>[]];
        $menu[]=['href'=>"users","feather"=>"users","text"=>"Users","has_sub"=>[]];
        $has_booking_sub[]=['href'=>"add","feather"=>"circle","text"=>"Add Booking"];
        $has_booking_sub[]=['href'=>"view","feather"=>"circle","text"=>"Bookings"];
        $has_booking_sub[]=['href'=>"supplier/view","feather"=>"circle","text"=>"supplier Bookings"];

        $menu[]=['href'=>"bookings","feather"=>"bookmark","text"=>"Bookings","has_sub"=>$has_booking_sub];


        $has_promotion_sub[]=['href'=>"agent","feather"=>"circle","text"=>"Add Agent"];
        $has_promotion_sub[]=['href'=>"add","feather"=>"circle","text"=>"Add Promotion"];
        $has_promotion_sub[]=['href'=>"view","feather"=>"circle","text"=>"View Promotion"];
        $has_promotion_sub[]=['href'=>"report","feather"=>"circle","text"=>"Promotion Report"];
        $menu[]=['href'=>"promotion","feather"=>"dollar-sign","text"=>"Promotions","has_sub"=>$has_promotion_sub];

        // $menu[]=['href'=>"agent","feather"=>"user","text"=>"Add Agent","has_sub"=>[]];
        $menu[]=['href'=>"operators","feather"=>"user","text"=>"Operators","has_sub"=>[]];
        $menu[]=['href'=>"supplier","feather"=>"user","text"=>"Supplier","has_sub"=>[]];

        $menu[]=['href'=>"products","feather"=>"shopping-bag","text"=>"Products","has_sub"=>[]];

        $has_invoice_sub[]=['href'=>"admin","feather"=>"circle","text"=>"Admin Invoice"];
        $has_invoice_sub[]=['href'=>"operator","feather"=>"circle","text"=>"Operator Invoice"];
        $has_invoice_sub[]=['href'=>"apply_gcost","feather"=>"circle","text"=>"Apply Google Cost"];
        // $has_invoice_sub[]=['href'=>"account","feather"=>"circle","text"=>"Account Manage"];
        // $has_invoice_sub[]=['href'=>"paid","feather"=>"circle","text"=>"Paid Invoice"];
        $menu[]=['href'=>"invoices","feather"=>"file-text","text"=>"Invoices","has_sub"=>$has_invoice_sub];

        $has_sub[]=['href'=>"bookings/capacity","feather"=>"circle","text"=>"Bookings Capacity"];
        $has_sub[]=['href'=>"all_bookings","feather"=>"circle","text"=>"All Bookings"];
        //$has_sub[]=['href'=>"#","feather"=>"circle","text"=>"Cancelled Bookings"];
        //$has_sub[]=['href'=>"#","feather"=>"circle","text"=>"Summary"];
        $has_sub[]=['href'=>"performance","feather"=>"circle","text"=>"Performance"];
        $has_sub[]=['href'=>"aff-performance","feather"=>"circle","text"=>"Aff. Performance"];
        $has_sub[]=['href'=>"departure_return","feather"=>"circle","text"=>"Departure Return"];
        $has_sub[]=['href'=>"refunds","feather"=>"circle","text"=>"Refunds"];
        // $has_sub[]=['href'=>"#","feather"=>"circle","text"=>"Operator Bookings"];
        $has_sub[]=['href'=>"exports","feather"=>"circle","text"=>"Exports"];
        $menu[]=['href'=>"reports","feather"=>"file-text","text"=>"Reports","has_sub"=>$has_sub]; 

        $menu[]=['href'=>"domains","feather"=>"globe","text"=>"Domains","has_sub"=>[]];
        $menu[]=['href'=>"settings","feather"=>"settings","text"=>"Settings","has_sub"=>[]];

        $has_accsub[]=['href'=>"website","feather"=>"circle","text"=>"Website"];
        $has_accsub[]=['href'=>"supplier","feather"=>"circle","text"=>"Supplier"];
        $menu[]=['href'=>"account","feather"=>"file-text","text"=>"Account Manage","has_sub"=>$has_accsub]; 

        // $menu[]=['href'=>"account","feather"=>"file-text","text"=>"Account Manage","has_sub"=>[]];
        $menu[]=['href'=>"integration/clicksend","feather"=>"message-circle","text"=>"SMS","has_sub"=>[]];
        $menu[]=['href'=>"prices","feather"=>"dollar-sign","text"=>"Prices","has_sub"=>[]];
        $menu[]=['href'=>"reviews","feather"=>"file-text","text"=>"Reviews","has_sub"=>[]];
    }

   
   $html="";
   for ($i=0;$i<sizeof($menu); $i++) 
   { 
         extract($menu[$i]);
         $active="";
         if($node1==$href)
         {
             $active="active";
         }
         if(sizeof($has_sub)==0)
         {
            $html.="<li class=\"$active nav-item\">
                       <a class=\"d-flex align-items-center\" href=".base_url($href).">
                           <i data-feather='$feather'></i>
                           <span class=\"menu-title text-truncate\" data-i18n='$feather'>$text</span>
                       </a>
                   </li>";
       }else{
            $open="";
            $node1_href=$href;
            if($node1==$href)
            {
                $open="open";
            }
            $main_href=$href;
            $html.="<li class=\"nav-item has-sub $open\">
                 <a class=\"d-flex align-items-center\" href=\"javascript:void(0);\">
                        <i data-feather='$feather'></i>
                        <span class=\"menu-title text-truncate\" data-i18n='$feather'>$text</span>
                 </a>";

                 for ($j=0; $j<sizeof($has_sub); $j++) 
                 { 
                   extract($has_sub[$j]);
                   $active="";
                  if($node1==$node1_href && $node2==$href)
                  {
                      $active="active";
                  }
                   $html.="<ul class=\"menu-content\">
                        <li class='$active'>
                           <a class=\"d-flex align-items-center\" href=".base_url($main_href."/".$href).">
                             <i data-feather='$feather'></i>
                             <span class=\"menu-item text-truncate\" data-i18n='$feather'>$text</span>
                           </a>
                         </li>
                    </ul>";
                 }
            $html.="</li>";
       }            
   }
   print_r($html);
       
}
function get_currency($code,$price)
{
    $currency = '€'.$price;
    if ($code == 'DUB') {
        $currency ='£'.$price;
    }
    return $currency;
}

function get_website_types()
{
   $options = [
       // '' => '-- All --',
       'GL' => 'Gobal Parking',
       '2' => 'Supplier',
   ];
   $html="";
   foreach ($options as $key => $op) 
   {
       $html .='<option value="'.$key.'">'.$op.'</option>';
   }

   return $html;
}

function get_agents()
{
   $options = [
       '' => '-- Select Agent --',
       '1' => 'Airport Parking Global Services Ltd',
       'CPD' => 'Compare Parking Deals',
       'FreeToMove' => 'Free2Move',
       'CTAP' => 'CTAP',
       'APU' => 'APU',
       'Holiday Extras' => 'Holiday Extras',
       'P4U' => 'Parking4You',
       'ParkVia' => 'ParkVia',
       'SkyParking Services' => 'SkyParking Services',
       'Park&Fly' => 'Park & Fly',
       'JBF' => 'JBF',
       'YTE' => 'Your Travel Extras',
       'HCP' => 'Holidays CarParking',
       'CYP' => 'Compare Your Parking',
       'https://longtermparking.ie/' => 'Long Term Parking',
       'Cash Booking' => 'Cash Booking',
       'Unknown' => 'Unknown',
       // '18' => 'Cheap Deal Center Ltd',
       // '14' => 'Global - Old System Import',
       // '16' => 'Global Comparison Site',
       // '3' => 'Looking4Parking',
       // '17' => 'Meet and Greet Reservations Ltd',
       // '2' => 'Opitech Ltd',
       // '12' => 'Park & Go',
       // '10' => 'Parking Zone',
       // '13' => 'Skypark Ltd',
       // '7' => 'Travel Airport Plus',
       // '9' => 'ZMD Travel',
   ];   return $options;
}

function get_traffic_sources()
{
   $options = [
       '' => '-- Select Traffic Source --',
       'aw' => 'AWIN',
       'facebook' => 'Facebook',
   ];
   return $options;
}

function get_shift_time()
{
   $options = array(
    '0247' => '24/7',
    '0000' => '0:00 am',
    '0030' => '0:30 am',
    '0100' => '1:00 am',
    '0130' => '1:30 am',
    '0200' => '2:00 am',
    '0230' => '2:30 am',
    '0300' => '3:00 am',
    '0330' => '3:30 am',
    '0400' => '4:00 am',
    '0430' => '4:30 am',
    '0500' => '5:00 am',
    '0530' => '5:30 am',
    '0600' => '6:00 am',
    '0630' => '6:30 am',
    '0700' => '7:00 am',
    '0730' => '7:30 am',
    '0800' => '8:00 am',
    '0830' => '8:30 am',
    '0900' => '9:00 am',
    '0930' => '9:30 am',
    '1000' => '10:00 am',
    '1030' => '10:30 am',
    '1100' => '11:00 am',
    '1130' => '11:30 am',
    '1200' => '12:00 pm',
    '1230' => '12:30 pm',
    '1300' => '1:00 pm',
    '1330' => '1:30 pm',
    '1400' => '2:00 pm',
    '1430' => '2:30 pm',
    '1500' => '3:00 pm',
    '1530' => '3:30 pm',
    '1600' => '4:00 pm',
    '1630' => '4:30 pm',
    '1700' => '5:00 pm',
    '1730' => '5:30 pm',
    '1800' => '6:00 pm',
    '1830' => '6:30 pm',
    '1900' => '7:00 pm',
    '1930' => '7:30 pm',
    '2000' => '8:00 pm',
    '2030' => '8:30 pm',
    '2100' => '9:00 pm',
    '2130' => '9:30 pm',
    '2200' => '10:00 pm',
    '2230' => '10:30 pm',
    '2300' => '11:00 pm',
    '2330' => '11:30 pm'
    );
   return $options;
}

function get_booking_shift_time()
{
   $options = array(
    '0000' => '0:00 am',
    '0030' => '0:30 am',
    '0100' => '1:00 am',
    '0130' => '1:30 am',
    '0200' => '2:00 am',
    '0230' => '2:30 am',
    '0300' => '3:00 am',
    '0330' => '3:30 am',
    '0400' => '4:00 am',
    '0430' => '4:30 am',
    '0500' => '5:00 am',
    '0530' => '5:30 am',
    '0600' => '6:00 am',
    '0630' => '6:30 am',
    '0700' => '7:00 am',
    '0730' => '7:30 am',
    '0800' => '8:00 am',
    '0830' => '8:30 am',
    '0900' => '9:00 am',
    '0930' => '9:30 am',
    '1000' => '10:00 am',
    '1030' => '10:30 am',
    '1100' => '11:00 am',
    '1130' => '11:30 am',
    '1200' => '12:00 pm',
    '1230' => '12:30 pm',
    '1300' => '1:00 pm',
    '1330' => '1:30 pm',
    '1400' => '2:00 pm',
    '1430' => '2:30 pm',
    '1500' => '3:00 pm',
    '1530' => '3:30 pm',
    '1600' => '4:00 pm',
    '1630' => '4:30 pm',
    '1700' => '5:00 pm',
    '1730' => '5:30 pm',
    '1800' => '6:00 pm',
    '1830' => '6:30 pm',
    '1900' => '7:00 pm',
    '1930' => '7:30 pm',
    '2000' => '8:00 pm',
    '2030' => '8:30 pm',
    '2100' => '9:00 pm',
    '2130' => '9:30 pm',
    '2200' => '10:00 pm',
    '2230' => '10:30 pm',
    '2300' => '11:00 pm',
    '2330' => '11:30 pm'
    );
   return $options;
}

function get_capacity_by()
{
    $options = array(
        array(
            'value' => '0',
            'label' => 'Do Not Adjust Prices'
        ),
        array(
            'value' => '1',
            'label' => 'Adjust By Product Capacity'
        ),
        array(
            'value' => '2',
            'label' => 'Adjust By Operator Capacity'
        )
    );

    return $options;
} 

function get_weekdays()
{
    $options=array
    (
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
    );

    return $options;
}

function get_invoice_years()
{
    $html='';
    for ($i=date('Y'); $i >= 2000; $i--) {
        $html.='<option value="'.$i.'">'.$i.'</option>';
    }

    return $html;
}


function get_invoice_month()
{
    $options=array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    );
    $html='';
    foreach ($options as $key => $v) {
        $html.='<option value="'.$key.'">'.$v.'</option>';
    }

    return $html;
}

function get_invoice_days()
{
    $options=array(
        7=> 'Weekly',
        15=> '15 Days',
        30=>'Monthly',
    );
    $html='';
    foreach ($options as $key => $v) {
        $html.='<option value="'.$key.'">'.$v.'</option>';
    }

    return $html;
}

function generateRandomColor() 
{
    $r = mt_rand(0, 255);
    $g = mt_rand(0, 255);
    $b = mt_rand(0, 255);
    return "rgb($r, $g, $b)";
    // return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

function get_limiter_time()
{
   $options = array(
    '00:01' => '24/7',
    '00:00' => '0:00 am',
    '00:30' => '0:30 am',
    '01:00' => '1:00 am',
    '01:30' => '1:30 am',
    '02:00' => '2:00 am',
    '02:30' => '2:30 am',
    '03:00' => '3:00 am',
    '03:30' => '3:30 am',
    '04:00' => '4:00 am',
    '04:30' => '4:30 am',
    '05:00' => '5:00 am',
    '05:30' => '5:30 am',
    '06:00' => '6:00 am',
    '06:30' => '6:30 am',
    '07:00' => '7:00 am',
    '07:30' => '7:30 am',
    '08:00' => '8:00 am',
    '08:30' => '8:30 am',
    '09:00' => '9:00 am',
    '09:30' => '9:30 am',
    '10:00' => '10:00 am',
    '10:30' => '10:30 am',
    '11:00' => '11:00 am',
    '11:30' => '11:30 am',
    '12:00' => '12:00 pm',
    '12:30' => '12:30 pm',
    '13:00' => '1:00 pm',
    '13:30' => '1:30 pm',
    '14:00' => '2:00 pm',
    '1430' => '2:30 pm',
    '15:00' => '3:00 pm',
    '15:30' => '3:30 pm',
    '16:00' => '4:00 pm',
    '16:30' => '4:30 pm',
    '17:00' => '5:00 pm',
    '1730' => '5:30 pm',
    '18:00' => '6:00 pm',
    '18:30' => '6:30 pm',
    '19:00' => '7:00 pm',
    '19:30' => '7:30 pm',
    '20:00' => '8:00 pm',
    '20:30' => '8:30 pm',
    '21:00' => '9:00 pm',
    '21:30' => '9:30 pm',
    '22:00' => '10:00 pm',
    '22:30' => '10:30 pm',
    '23:00' => '11:00 pm',
    '23:30' => '11:30 pm',
    '23:59' => '11:59 pm'

);
   return $options;
}

function getDaysList($startDate, $endDate) 
{
    $daysList = [];

    // Create DateTime objects for start and end dates
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->modify('+1 day'); // Include the end date in the period

    // Define the interval (1 day)
    $interval = new DateInterval('P1D');

    // Create DatePeriod object
    $datePeriod = new DatePeriod($start, $interval, $end);

    // Iterate through each day in the period and add to the list
    foreach ($datePeriod as $date) {
        $daysList[] = $date->format('Y-m-d');
    }

    return $daysList;
}

function calculateDaysPast($returnAt)
{
    $now = new DateTime(); // Get the current datetime
    $returnDate = new DateTime($returnAt); // Parse the return_at datetime

    // Check if the current date is after the return date
    if ($now > $returnDate) {
        $interval = $returnDate->diff($now); // Calculate the difference
        $daysPast = $interval->days; // Get the difference in days
        return $daysPast;
    } else {
        return false;
    }
}
function getDaysBetweenDates($startDate, $endDate) {
    // Create DateTime objects for the start and end dates
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    // Calculate the difference between the two dates
    $interval = $start->diff($end);

    // Return the difference in days
    return $interval->days;
}

function getDepartRowClass($depart_at)
{
    $compareDate =$depart_at;
    $rowClass='';
    $currentDate= date('Y-m-d H:i:s');
    $pastTime = date('Y-m-d H:i:s',strtotime($currentDate.' -1 hour'));
    $futureTime = date('Y-m-d H:i:s',strtotime($currentDate.' +30 minutes'));

    if ($compareDate < $pastTime)
    {
        // Departure date has passed - add a class
        $rowClass = 'red-mark';
    } elseif ($compareDate >= $currentDate && $compareDate <= $futureTime) {
        // Departure date is within the next 30 minutes - add a class
        $rowClass = 'green-mark';
    }
    return $rowClass;
}

function getReturnRowClass($return_at)
{
    $rowClass='';
    $late_charges=0;

    $compareDate =$return_at;
    $currentDate= date('Y-m-d H:i:s');
    $pastTime = date('Y-m-d H:i:s',strtotime($currentDate.' -1 hour'));
    $futureTime = date('Y-m-d H:i:s',strtotime($currentDate.' +30 minutes'));
    $returnAtTime = date('Y-m-d H:i:s',strtotime($return_at.' +4 hour'));

    if ($compareDate < $pastTime)
    {
         // Departure date has passed - add a class
        if ($currentDate > $returnAtTime) {
            $rowClass = 'late-mark';
            $days = calculateDaysPast($return_at);
            $late_charges = ($days > 0)? $days*20+20: 20;
        }else{
            $rowClass = 'red-mark'; 
        }
    } elseif ($compareDate >= $currentDate && $compareDate <= $futureTime) {
        // Departure date is within the next 30 minutes - add a class
        $rowClass = 'green-mark';
    }

    return [$rowClass, $late_charges];
}

function generateTimeIntervals($currentDate)
{
    // $currentDate = date('Y-m-d'); // Current date
    $startTime = strtotime("$currentDate 00:00:00"); // Midnight
    $endTime = strtotime("$currentDate 23:59:59"); // End of the day

    $intervals = [];

    while ($startTime <= $endTime) {
        $intervals[] = date('Y-m-d H:i:s', $startTime);
        $startTime = strtotime('+30 minutes', $startTime);
    }

    return $intervals;
}

function check_departReturn_status($checkDate)
{
    $departure = strtotime($checkDate);
    $today     = strtotime(date('Y-m-d'));
    $yesterday = strtotime('-1 day', $today);
    $tomorrow  = strtotime('+1 day', $today);

    if ($departure === $today) {
        return 'Today';
    } elseif ($departure === $yesterday) {
        return 'Yesterday';
    } elseif ($departure === $tomorrow) {
        return 'Tomorrow';
    } elseif ($departure < $today) {
        return 'Before '.getDaysBetweenDates(date('Y-m-d'), $checkDate).' days';
    } else {
        return 'After '.getDaysBetweenDates(date('Y-m-d'), $checkDate).' days';
    }
}

function get_cpd_p4u($arr)
{
    // Initialize
    $combined = [];
    $cpd_p4u = (object)[
        'source' => 'CPD-P4U',
        'qty' => 0,
        'totPrice' => 0,
        'collected' => 0,
        'collectedAmount' => 0,
        'noShow' => 0
    ];

    foreach ($arr as $item) {
        if (in_array($item->source, ['P4U', 'CPD'])) {
            $cpd_p4u->qty += $item->qty;
            $cpd_p4u->totPrice += $item->totPrice;
            $cpd_p4u->collected += $item->collected;
            $cpd_p4u->collectedAmount += $item->collectedAmount;
            $cpd_p4u->noShow += $item->noShow;
        } else {
            $combined[] = $item; // keep other sources as-is
        }
    }

    // Add the combined CPD_P4U record
    $combined[] = $cpd_p4u;
    return $combined;
}

function get_op_cpd_p4u($arr)
{
    // Initialize
    $combined = [];
    $cpd_p4u = (object)[
        'source' => 'CPD-P4U',
        'qty' => 0,
        'totPrice' => 0,
        'collected' => 0,
        'collectedAmount' => 0,
        'totRefund' => 0,
        'totGcost' => 0,
        'noShow' => 0,
    ];

    foreach ($arr as $item) {
        if (in_array($item->source, ['P4U', 'CPD'])) {
            $cpd_p4u->qty += $item->qty;
            $cpd_p4u->totPrice += $item->totPrice;
            $cpd_p4u->collected += $item->collected;
            $cpd_p4u->collectedAmount += $item->collectedAmount;
            $cpd_p4u->totRefund += $item->totRefund;
            $cpd_p4u->totGcost += $item->totGcost;
            $cpd_p4u->noShow += $item->noShow;
        } else {
            $combined[] = $item; // keep other sources as-is
        }
    }

    // Add the combined CPD_P4U record
    $combined[] = $cpd_p4u;
    return $combined;
}

function get_review_star($rating)
{
    switch ($rating) {
        case 5:
            echo'<span class="text-warning">★★★★★</span>';
            break;
        case 4:
            echo'<span class="text-warning">★★★★☆</span>';
            break;
        case 3:
            echo'<span class="text-warning">★★★☆☆</span>';
            break;
        case 2:
            echo'<span class="text-warning">★★☆☆☆</span>';
            break;
        
        default:
            echo'<span class="text-warning">★☆☆☆☆</span>';
            break;
    }
}

function mergeAirportData($array1, $array2, $array3) {
    $merged = [];

    // Index array1 by airport
    foreach ($array3 as $item) {
        $merged[$item->airport] = [
            'airport' => $item->airport,
            'totalQTY' => 0,
            'totAmount' => 0,
            'totalQTY2' => 0,
            'totAmount2' => 0,
        ];
    }
    // Index array1 by airport
    foreach ($array1 as $item) {
        $merged[$item->airport] = [
            'airport' => $item->airport,
            'totalQTY' => $item->totalQTY,
            'totAmount' => $item->totAmount,
            'totalQTY2' => 0,
            'totAmount2' => 0,
        ];
    }

    // Merge array2 data
    foreach ($array2 as $item) {
        if (isset($merged[$item->airport])) {
            $merged[$item->airport]['totalQTY2'] = $item->totalQTY2;
            $merged[$item->airport]['totAmount2'] = $item->totAmount2;
        } else {
            $merged[$item->airport] = [
                'airport' => $item->airport,
                'totalQTY' => 0,
                'totAmount' => 0,
                'totalQTY2' => $item->totalQTY2,
                'totAmount2' => $item->totAmount2,
            ];
        }
    }

    

    // Convert back to stdClass objects
    return array_map(function ($item) {
        return (object)$item;
    }, array_values($merged));
}

function get_review_stars($avgRating)
{
    if ($avgRating == 5) {
        echo'★★★★★';
    }elseif ($avgRating < 5 && $avgRating >=4) {
        echo'★★★★☆';
    }elseif ($avgRating < 4 && $avgRating >=3) {
        echo'★★★☆☆';
    }elseif ($avgRating < 3 && $avgRating >=2) {
        echo'★★☆☆☆';
    }elseif ($avgRating < 2) {
        echo'★☆☆☆☆';
    }
}

function get_missing_rnages($ranges)
{
    // $ranges = [
    //     ['band_name' => 'A', 'from_date' => '2025-01-01', 'to_date' => '2025-06-30'],
    //     ['band_name' => 'B', 'from_date' => '2025-08-01', 'to_date' => '2025-12-31'],
    //     ['band_name' => 'C', 'from_date' => '2026-02-01', 'to_date' => '2026-06-30'],
    // ];

    $start = new DateTime(date('Y-m-d')); // Start of current year
    $end = (clone $start)->modify('+2 years')->modify('last day of December');
    // $end = new DateTime('2027-12-31');

    // Step 1: Sort by from_date
    usort($ranges, function ($a, $b) {
        return strcmp($a->dfrom, $b->dfrom);
    });

    $missing = [];
    $current = clone $start;

    foreach ($ranges as $range) 
    {
        $rangeStart = new DateTime($range->dfrom);
        $rangeEnd = new DateTime($range->dto);

        // Step 2: Check for gap
        if ($current < $rangeStart) {
            $gapStart = clone $current;
            $gapEnd = (clone $rangeStart)->modify('-1 day');
            $missing[] = ['dfrom' => $gapStart->format('Y-m-d'), 'dto' => $gapEnd->format('Y-m-d')];
        }

        // Step 3: Move current forward
        if ($current <= $rangeEnd) {
            $current = (clone $rangeEnd)->modify('+1 day');
        }
    }

    // Step 4: Final gap
    if ($current <= $end && $current != $start) {
        $missing[] = ['dfrom' => $current->format('Y-m-d'), 'dto' => $end->format('Y-m-d')];
    }
    $missing = array_map(function($item) {
        return $item['dfrom'] . ' to ' . $item['dto'];
    }, $missing);
    return $missing;
    // pre($missing);
    // Output missing ranges
    // foreach ($missing as $gap) {
    //     echo "Missing: {$gap['from']} to {$gap['to']}\n";
    // }
}

?>