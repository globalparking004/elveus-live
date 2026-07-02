<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ArchiveLogs extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'logs:archive';
    protected $description = 'Archive old logs to another table';

    public function run(array $params)
    {
        $db = Database::connect();

        $days = $params[0] ?? 10; // default keep 90 days
        $dateLimit = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $db->query("
            INSERT INTO user_login_archive
            SELECT * FROM user_logins WHERE created_at < ?
        ", [$dateLimit]);

        $db->query("DELETE FROM user_logins WHERE created_at < ?", [$dateLimit]);

        CLI::write("Logs older than {$days} days archived successfully.", 'green');
    }
}

// 0 /2 * * * cd /var/www/html/booking && php spark logs:archive 10 >> writable/logs/cron_archive.log 2>&1
