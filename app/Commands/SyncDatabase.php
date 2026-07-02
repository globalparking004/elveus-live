<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
// 0 0 * * * cd /var/www/html/booking && php spark export:database >> writable/logs/cron.log 2>&1

class SyncDatabase extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'sync:db';
    protected $description = 'Sync data from live DB to backup DB';

    public function run()
    {
        $liveDB   = \Config\Database::connect('default');
        $backupDB = \Config\Database::connect('backup');

        $table = 'tbl_booking';
        $today = date('Y-m-d');

        // Step 1: Fetch today's new rows from live DB
        $liveData = $liveDB->table($table)
            ->where('DATE(booked_at)', $today)
            ->get()
            ->getResultArray();

        $synced = 0;

        // Step 2: Insert only if not already in backup DB
        foreach ($liveData as $row) {
            // Check if already exists (based on unique id)
            $exists = $backupDB->table($table)
                ->where('id', $row['id'])
                ->countAllResults();

            if ($exists == 0) {
                $backupDB->table($table)->insert($row);
                $synced++;
            }
        }

        CLI::write("Synced {$synced} new rows from today's data.", 'green');
    }
}

// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/bristol@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/bristol@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/brs@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/brs@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/sou@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/sou@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/agents@birminghamairportparkingservices.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/agents@birminghamairportparkingservices.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/ltn@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/ltn@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/bhx@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/bhx@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/stn@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/stn@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/leeds@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/leeds@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/dub@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/dub@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/dubmg@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/dubmg@ukmails.co.uk/log.txt
// */8 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/csvdubmg@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/csvdubmg@ukmails.co.uk/log.txt
// 5 * * * * /usr/bin/php /var/www/html/booking/emailparsing/parkviaapiparsing/park_via_api.php >> /var/www/html/booking/emailparsing/parkviaapiparsing/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/mgman@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/mgman@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/man@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/man@ukmails.co.uk/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/umairfreetomove/parsing.py >> /var/www/html/booking/emailparsing/umairfreetomove/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/bristol/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/bristol/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/leeds/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/leeds/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/luton/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/luton/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/stn/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/stn/log.txt
// #*/5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/man/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/man/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/dub/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/dub/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/sop/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/sop/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/sou/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/sou/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/freetomoveparsingimp/lcy/parsing.py >> /var/www/html/booking/emailparsing/freetomoveparsingimp/lcy/log.txt

// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/dubparkfly/parsing.py >> /var/www/html/booking/emailparsing/dubparkfly/log.txt
// */5 * * * * /usr/bin/python3 /var/www/html/booking/emailparsing/lcy@ukmails.co.uk/email_parsing.py >> /var/www/html/booking/emailparsing/lcy@ukmails.co.uk/log.txt
// 0 0 * * * cd /var/www/html/booking && php spark export:database >> writable/logs/cron.log 2>&1

// Hostinger Cronjob
// domains/airportparkingsolutions.co.uk/public_html/cron-test.php
// domains/airportparkingsolutions.co.uk/public_html/php spark sync:db >> /home/u539404567/domains/airportparkingsolutions.co.uk/public_html/writable/logs/cron.log 2>&1
// domains/airportparkingsolutions.co.uk/public_html/php spark sync:db >> writable/logs/cron.log 2>&1