<?php

namespace Tests\Unit;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_author()
    {
        $authorData = [
            'name' => 'John Doe',
            'bio' => 'A famous author',
        ];

        $author = Author::create($authorData);

        $this->assertInstanceOf(Author::class, $author);
        $this->assertEquals($authorData['name'], $author->name);
        $this->assertEquals($authorData['bio'], $author->bio);
    }
}
