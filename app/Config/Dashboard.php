<?php
namespace App\Controllers;

use App\Libraries\PdfGenerator;

class Dashboard extends BaseController
{
    public function index()
    {

        // // Load the PdfGenerator library
        // $pdfGenerator = new PdfGenerator();

        // // HTML content to be converted to PDF
        // $htmlContent = '<html><body><h1>Hello, World!</h1></body></html>';

        // // Specify the output file path
        // $outputFilePath = WRITEPATH . 'pdf/output.pdf';

        // // Generate the PDF
        // $pdfGenerator->generatePdf($htmlContent, $outputFilePath);

        // // Provide a download link or redirect to the generated PDF
        // return redirect()->to(base_url('pdf/output.pdf'));

        // exit();
        $today = date("Y-m-d");
        $profit = 0;
        $total_bookings = 0;
        $completed_bookings = 0;
        $avg = 0;
        $sql = "SELECT sum(price) as profit,avg(price) as average FROM `tbl_booking` WHERE date(created_at)='$today' AND status='1'";
        $result = $this->db->query($sql)->getResult();
        if ($result) {
            $profit = $result[0]->profit;
            $avg = $result[0]->average;
        }

        $sql = "SELECT count(id) as total_bookings FROM `tbl_booking` WHERE date(created_at)='$today'";
        $result = $this->db->query($sql)->getResult();
        if ($result) {
            $total_bookings = $result[0]->total_bookings;
        }

        $sql = "SELECT count(id) as completed_bookings FROM `tbl_booking` WHERE date(created_at)='$today' AND status='1'";
        $result = $this->db->query($sql)->getResult();
        if ($result) {
            $completed_bookings = $result[0]->completed_bookings;
        }

        // echo $profit."<br>";
        // echo $total_bookings."<br>";
        // echo $completed_bookings."<br>";

        //$avg = strval($total_bookings)/strval($completed_bookings);
        $avg = number_format($avg, 2);
        $profit = number_format($profit, 2);

        //print_r($avg);
        //exit();
        /////////////////// capacity //////////////////////

        //echo $DateFrom = date('Y-m-d');

        $web_name = array();
        $booking_count = array();
        $airport_capacity = array();
        $sql = "SELECT short_code,web_name from tbl_websites ";
        $fetch_websites = $this->db->query($sql)->getResult();

        $i=0;
        foreach ($fetch_websites as $website) {


            $short_code = $website->short_code;

            $sql = "SELECT sum(capacity) as sum FROM `tbl_products` WHERE parent='$short_code' and adjust_prices_by_capacity=1";
            $result_product = $this->db->query($sql)->getResult();
            $airport_capacity[$i] = $result_product[0]->sum;

            $DateFrom = date('Y-m-d');

            if (isset($airport_capacity) and !empty($airport_capacity[$i]) and $airport_capacity[$i]!=0) {
                $sql_data = "SELECT count(*) as bookingCount FROM `tbl_booking` WHERE (`depart_at`<= '$DateFrom 00:00:00' AND return_at>'$DateFrom 00:00:00')  and status='completed' and airport='$short_code'";
                $result_product = $this->db->query($sql_data)->getRow();
                $web_name[$i] = $website->web_name;

                $booking_count[$i] = $result_product->bookingCount;
                $i++;
            }




        }

        // exit;



        /////////////////////////////////////////////////////



        $data = [
            "page_title" => "Dashboard",
            "breadcrumb" => [],
            "stats" => ['profit' => $profit, 'total_bookings' => $total_bookings, 'completed_bookings' => $completed_bookings, 'avg' => $avg],
            //"breadcrumb"=>[["href"=>"#","title"=>"home","status"=>"active","link"=>true],["href"=>"#","title"=>"view","status"=>"","link"=>false]]
            "web_name" => $web_name,
            "booking_count" => $booking_count,
            "airport_capacity" => $airport_capacity


        ];
        return view('home', $data);
    }

    public function export_bookings()
    {
        $sql2 = "SELECT firstName, surname, email, contactNumber, created_at, airport FROM `tbl_booking` WHERE airport IS NOT NULL";
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
        fputcsv($file, ['First Name', 'Last Name', 'Email', 'Contact No', 'Created At', 'Airport']);

        // Add rows to the CSV file
        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking->firstName,
                $booking->surname,
                $booking->email,
                $booking->contactNumber,
                $booking->created_at,
                $booking->airport
            ]);
        }

        fclose($file);
        // file_put_contents($filePath . $fileName, $file);
        // Return the CSV file as a download
        return $this->response->download($filePath. $fileName, null)->setFileName($fileName);
    }
}