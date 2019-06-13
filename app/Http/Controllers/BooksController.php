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
        //return Book::all();

        // modifica per Fractal
//        return ['data' => Book::all()->toArray()];

        // ecco come agganciare la giusta risposta con il servizio
        return $this->collection(Book::all(), new BookTransformer());
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

//        return Book::findOrFail($id);

//        $result = ['data' => Book::findOrFail($id)->toArray()];
//
//        return $result;

        // ecco come agganciare la giusta risposta con il servizio

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
           'title' => 'required',
           'description' => 'required',
           'author' => 'required'
        ]);

        $book = Book::create($request->all());

//        return response()->json([
//            'created' => true],
//            201,
//            ['Location' => route('books.show', ['id' => $book->id])]);

//        return response()->json(
//            ['data' => $book->toArray()],
//            201,
//            ['Location' => route('books.show',
//            ['id' => $book->id])]
//        );

        // refactor per agganciare la risposta del nuovo servizio

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
            'title' => 'required',
            'description' => 'required',
            'author' => 'required'
        ]);

        $book->fill($request->all());
        $book->save();

//        return $book;

//        return ['data' => $book->toArray()];

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