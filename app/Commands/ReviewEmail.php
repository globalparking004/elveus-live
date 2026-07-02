<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
// 0 */4 * * * cd /var/www/html/booking && php spark review:email >> writable/logs/cron.log 2>&1

class ReviewEmail extends BaseCommand
{
    protected $group       = 'Custom'; 
    protected $name        = 'review:email';
    protected $description = 'Send email for feedback or reminder review';

    public function run(array $params)
    {
        helper('query');
        $type = $params[0] ?? 'initial';

        if ($type === 'reminder') {
            reminder_review_mail_send();
            CLI::write('Reminder review email sent successfully.');
        } else {
            review_mail_send();
            CLI::write('Initial review email sent successfully.');
        }
        
    }
}
