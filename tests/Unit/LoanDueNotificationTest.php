<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\LoanDueNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanDueNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testLoanDueNotificationContent()
    {
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 0, 0));

        $user = User::factory()->create();
        $book = Book::factory()->create();
        $dueDate = Carbon::now()->addDays(5)->startOfDay();
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => $dueDate,
        ]);

        $notification = new LoanDueNotification($loan);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('Lembrete de Devolução de Livro', $mailMessage->subject);
        $this->assertStringContainsString($user->name, $mailMessage->introLines[0]);
        $this->assertStringContainsString($book->title, $mailMessage->introLines[1]);
        $this->assertStringContainsString('5 dias', $mailMessage->introLines[1]);

        Carbon::setTestNow(); // Reset the mock time
    }
}
