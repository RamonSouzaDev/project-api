<?php

namespace Tests\Unit;

use App\Jobs\SendLoanDueNotification;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\LoanDueNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendLoanDueNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testSendLoanDueNotification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $job = new SendLoanDueNotification($loan);
        $job->handle();

        Notification::assertSentTo(
            $user,
            LoanDueNotification::class,
            function ($notification, $channels) use ($book, $user) {
                $mailMessage = $notification->toMail($user);

                return strpos($mailMessage->introLines[1], $book->title) !== false;
            }
        );
    }
}
