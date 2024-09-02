<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'loan_date' => Carbon::now()->subDays($this->faker->numberBetween(1, 30)),
            'due_date' => Carbon::now()->addDays($this->faker->numberBetween(1, 30)),
            'return_date' => $this->faker->optional()->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}
