<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/loans",
     *     summary="List all loans",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response="200", description="A list of loans")
     * )
     */
    public function index()
    {
        return Loan::with(['user', 'book'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     summary="Create a new loan",
     *     tags={"Loans"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="book_id", type="integer"),
     *             @OA\Property(property="loan_date", type="string", format="date"),
     *             @OA\Property(property="due_date", type="string", format="date")
     *         )
     *     ),
     *
     *     @OA\Response(response="201", description="Loan created")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after:loan_date',
        ]);

        $loan = Loan::create([
            'user_id' => $request->user()->id,
            'book_id' => $request->book_id,
            'loan_date' => $request->loan_date,
            'due_date' => $request->due_date,
        ]);

        return response()->json($loan->load(['user', 'book']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/loans/{id}",
     *     summary="Get a loan by ID",
     *     tags={"Loans"},
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
     *     @OA\Response(response="200", description="Loan details")
     * )
     */
    public function show(Loan $loan)
    {
        return $loan->load(['user', 'book']);
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{id}",
     *     summary="Update a loan",
     *     tags={"Loans"},
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
     *             @OA\Property(property="return_date", type="string", format="date")
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="Loan updated")
     * )
     */
    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'return_date' => 'required|date|after_or_equal:loan_date',
        ]);

        $loan->update([
            'return_date' => $request->return_date,
        ]);

        return response()->json($loan->load(['user', 'book']), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/loans/{id}",
     *     summary="Delete a loan",
     *     tags={"Loans"},
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
     *     @OA\Response(response="204", description="Loan deleted")
     * )
     */
    public function destroy(Loan $loan)
    {
        $loan->delete();

        return response()->noContent();
    }
}
