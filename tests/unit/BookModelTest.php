<?php

use App\Book;

class BookModelTest extends \PHPUnit\Framework\TestCase
{
    function testBookModelFields()
    {
        $book = new Book([
            'title' => '20000 leghe sotto i mari',
            'description' => 'Un libro di Giulio Verme',
            'author' => 'Giulio Verme'
        ]);

        $this->assertEquals('20000 leghe sotto i mari', $book->title);
        $this->assertEquals('Un libro di Giulio Verme', $book->description);
        $this->assertEquals('Giulio Verme', $book->author);
    }
}
