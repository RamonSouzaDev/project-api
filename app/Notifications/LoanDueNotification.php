<?php

namespace App\Notifications;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanDueNotification extends Notification
{
    use Queueable;

    public $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $daysUntilDue = Carbon::now()->startOfDay()->diffInDays($this->loan->due_date->startOfDay(), false);

        return (new MailMessage)
            ->subject('Lembrete de Devolução de Livro')
            ->line('Olá '.$notifiable->name.',')
            ->line("Este é um lembrete de que o livro \"{$this->loan->book->title}\" vence em {$daysUntilDue} dias.")
            ->line('Data de devolução: '.$this->loan->due_date->format('d/m/Y'))
            ->action('Ver Detalhes do Empréstimo', url('/loans/'.$this->loan->id))
            ->line('Por favor, devolva o livro à biblioteca antes da data de vencimento para evitar multas.');
    }
}
