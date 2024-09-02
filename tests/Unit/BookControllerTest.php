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
            ->assertJsonCount(3);
    }

    public function test_can_create_book()
    {
        $user = User::factory()->create();

        // Criar autores válidos
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();

        $bookData = [
            'title' => 'New Book',
            'description' => 'A great book',
            'publication_year' => 2023,
            'author_ids' => [$author1->id, $author2->id],
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $bookData);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Book']);

        // Adicione esta linha para depuração
        if ($response->status() != 201) {
            dump($response->json());
        }
    }
}
