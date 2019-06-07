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
//        try {
//            /** @return Book */
//            return Book::findOrFail($id);
//        } catch (ModelNotFoundException $e) {
//            return response()->json([
//                'error' => [
//                    'message' => 'Book not found'
//                ]
//            ], 404);
//        }

        return Book::findOrFail($id);
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

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        try{
            /** @var Book $book */
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e){
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        $book->fill($request->all());
        $book->save();

        return $book;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Laravel\Lumen\Http\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, $id)
    {
        try{
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $ecc) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        $book->delete();

        return response(null, 204);
    }
}