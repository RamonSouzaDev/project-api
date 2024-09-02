<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run()
    {
        // Certifique-se de que temos usuários e livros
        $user = User::factory()->create();
        $books = Book::factory()->count(5)->create();

        // Criar empréstimos com diferentes status
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $books[0]->id,
            'loan_date' => Carbon::now()->subDays(10),
            'due_date' => Carbon::now()->addDay(), // Vence amanhã
            'return_date' => null,
        ]);

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $books[1]->id,
            'loan_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(5), // Vence em 5 dias
            'return_date' => null,
        ]);

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $books[2]->id,
            'loan_date' => Carbon::now()->subDays(15),
            'due_date' => Carbon::now()->subDay(), // Venceu ontem
            'return_date' => null,
        ]);

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $books[3]->id,
            'loan_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(5),
            'return_date' => Carbon::now(), // Já devolvido
        ]);

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $books[4]->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14), // Vence em 14 dias
            'return_date' => null,
        ]);
    }
}
