<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    /**
     * Get the user that owns the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book associated with the loan.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scope a query to only include overdue loans.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('return_date')
            ->where('due_date', '<', now());
    }

    /**
     * Check if the loan is overdue.
     *
     * @return bool
     */
    public function isOverdue()
    {
        return ! $this->return_date && $this->due_date < now();
    }

    /**
     * Return the book.
     *
     * @return $this
     */
    public function returnBook()
    {
        $this->return_date = now();
        $this->save();

        return $this;
    }
}
