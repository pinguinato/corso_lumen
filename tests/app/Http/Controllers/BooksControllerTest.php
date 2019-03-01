<?php

class BooksControllerTest extends TestCase
{
    /** @test */
    public function testIndexStatusCodeIs200()
    {
        $this->get('/books')->seeStatusCode(200);
    }

    /** @test */
    public function testShowReturnAValidBook()
    {
        $this->get('/books/1')
            ->seeStatusCode(200)
            ->seeJson([
               'id' => 1,
               'title' => 'War of the Worlds',
               'description' => 'A science fiction masterpiece about Martians invading London',
               'author' => 'H. G. Wells'
            ]);
        $this->printResponse();
    }

    /** @test */
    public function testShowFailBookNotExists()
    {
        $this->markTestIncomplete('Pending Test');
    }

    public function testUrlNotFound()
    {
        $this->markTestIncomplete('Pending Test');
    }
}