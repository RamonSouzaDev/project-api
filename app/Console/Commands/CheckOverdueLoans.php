<?php

namespace App\Console\Commands;

use App\Jobs\SendLoanDueNotification;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CheckOverdueLoans extends Command
{
    protected $signature = 'loans:check-overdue';

    protected $description = 'Check for overdue loans and send notifications';

    public function handle()
    {
        $lockKey = 'check_overdue_loans_lock';

        if (! Redis::setnx($lockKey, true)) {
            $this->info('Another instance of this command is already running.');

            return;
        }

        Redis::expire($lockKey, 3600); // O lock expira após 1 hora

        try {
            $this->checkDueSoonLoans();
            $this->checkOverdueLoans();
        } finally {
            Redis::del($lockKey);
        }
    }

    private function checkDueSoonLoans()
    {
        $this->info('Checking for loans due in the next 3 days...');

        $dueSoonLoans = Loan::where('due_date', '>', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(3))
            ->whereNull('return_date')
            ->cursor();

        $count = 0;
        foreach ($dueSoonLoans as $loan) {
            SendLoanDueNotification::dispatch($loan)->onQueue('notifications');
            $count++;

            if ($count % 100 == 0) {
                $this->info("Processed $count due soon loans...");
            }
        }

        $this->info("$count loans found that are due soon.");
    }

    private function checkOverdueLoans()
    {
        $this->info('Checking for overdue loans...');

        $overdueLoans = Loan::where('due_date', '<', Carbon::now())
            ->whereNull('return_date')
            ->cursor();

        $count = 0;
        foreach ($overdueLoans as $loan) {
            SendLoanDueNotification::dispatch($loan)->onQueue('notifications');
            $count++;

            if ($count % 100 == 0) {
                $this->info("Processed $count overdue loans...");
            }
        }

        $this->info("$count overdue loans found.");
    }
}
