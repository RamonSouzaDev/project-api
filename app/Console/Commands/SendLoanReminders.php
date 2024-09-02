<?php

namespace App\Console\Commands;

use App\Jobs\SendLoanReminderEmail;
use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';

    protected $description = 'Send reminders for overdue loans';

    public function handle()
    {
        $lockKey = 'loan_reminders_lock';

        // Tenta obter um lock para evitar execuções simultâneas
        if (! Redis::setnx($lockKey, true)) {
            $this->info('Another instance of this command is already running.');

            return;
        }

        Redis::expire($lockKey, 3600); // O lock expira após 1 hora

        try {
            $overdueLoans = Loan::where('due_date', '<', now())
                ->whereNull('return_date')
                ->cursor(); // Usar cursor para processar grandes conjuntos de dados

            $count = 0;
            foreach ($overdueLoans as $loan) {
                SendLoanReminderEmail::dispatch($loan)->onQueue('emails');
                $count++;

                if ($count % 100 == 0) {
                    $this->info("Processed $count loans...");
                }
            }

            $this->info("Loan reminders dispatched: $count");
        } finally {
            Redis::del($lockKey); // Libera o lock
        }
    }
}
