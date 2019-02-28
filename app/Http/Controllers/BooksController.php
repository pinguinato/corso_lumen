<?php

namespace App\Http\Controllers;

class BooksController extends Controller
{
    /**
     * @return []
     */
    public function index()
    {
        return [
            ['title' => 'La guerra dei mondi'],
            ['title' => 'Il vecchio e il mare']
        ];
    }
}