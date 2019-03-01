<?php

namespace App\Http\Controllers;

use App\Book;

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
        return Book::findOrFail($id);
    }
}