<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="List all books",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response="200", description="A list of books")
     * )
     */
    public function index()
    {
        return Book::with('authors')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="publication_year", type="integer"),
     *             @OA\Property(property="author_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *
     *     @OA\Response(response="201", description="Book created")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|integer|min:1000|max:'.(date('Y') + 1),
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
        ]);

        $book = Book::create($request->except('author_ids'));
        $book->authors()->attach($request->author_ids);

        return response()->json($book->load('authors'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get a book by ID",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response="200", description="Book details")
     * )
     */
    public function show(Book $book)
    {
        return $book->load('authors');
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     summary="Update a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="publication_year", type="integer"),
     *             @OA\Property(property="author_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="Book updated")
     * )
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|integer|min:1000|max:'.(date('Y') + 1),
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
        ]);

        $book->update($request->except('author_ids'));
        $book->authors()->sync($request->author_ids);

        return response()->json($book->load('authors'), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     summary="Delete a book",
     *     tags={"Books"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response="204", description="Book deleted")
     * )
     */
    public function destroy(Book $book)
    {
        $book->authors()->detach();
        $book->delete();

        return response()->noContent();
    }
}
