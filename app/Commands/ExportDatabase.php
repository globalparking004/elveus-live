<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

// cd /var/www/html/booking
// https://drive.google.com/drive/folders/1ZOkG98YCi_hIP954hduS_dm59qK7nTyV?usp=drive_link
class ExportDatabase extends BaseCommand
{
    protected $group       = 'Custom'; 
    protected $name        = 'export:database';
    protected $description = 'Export the database to SQL and upload to Google Drive';

    public function run(array $params)
    {
        // Call your export logic here (move logic from Dashboard::export_database)
        // Example:
        $dashboard = new \App\Controllers\Dashboard();
        $dashboard->export_database(); // reuse your existing controller logic
        
        CLI::write('Database exported and uploaded successfully.');
    }
}
