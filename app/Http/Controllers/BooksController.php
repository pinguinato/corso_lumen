<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
// integrazione di fractal response nuovo servizio
use App\Transformer\BookTransformer;
use Illuminate\Http\Response;

class BooksController extends Controller
{
    /**
     * GET /books
     * @return Book[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        // ecco come agganciare la giusta risposta con il servizio
        return $this->collection(Book::all(), new BookTransformer());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->item(Book::findOrFail($id), new BookTransformer());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        /**
         * Validazione nel controller uso del metodo validate()
         */
        $this->validate($request, [
           'title' => 'required|max:255',
           'description' => 'required',
           'author_id' => 'required|exists:authors,id'
        ], [
            'description.required' => 'Please provide a :attribute.'
        ]);

        $book = Book::create($request->all());

        $data = $this->item($book, new BookTransformer());

        return response()->json($data, 201, [
           'Location' => route('books.show', ['id' => $book->id])
        ]);
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

        /**
         * Validazione nel controller uso del metodo validate()
         */
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'author_id' => 'required|exists:authors,id'
        ], [
            'description.required' => 'Please provide a :attribute.'
        ]);

        $book->fill($request->all());
        $book->save();

        // refactoring per agganciare il nuovo servizio
        return $this->item($book, new BookTransformer());
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