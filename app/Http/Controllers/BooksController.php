<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    /**
     * GET /books
     * @return Book[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Book::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            return Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $book = Book::create($request->all());

        return response()->json([
            'created' => true],
            201,
            ['Location' => route('books.show', ['id' => $book->id])]);
    }
}