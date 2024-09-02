<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_book()
    {
        $user = User::factory()->create();
        $authors = Author::factory()->count(2)->create();

        $bookData = [
            'title' => 'New Book',
            'description' => 'A great book',
            'publication_year' => 2023,
            'author_ids' => $authors->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $bookData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Book']);
    }
}
