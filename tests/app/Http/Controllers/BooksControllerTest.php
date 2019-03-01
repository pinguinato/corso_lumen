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
        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test */
    public function testShowFailBookNotExists()
    {
        $this->get('/books/99999')
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'message' => 'Book not found'
                ]
            ]);
    }

    /** @test */
    public function testUrlNotFound()
    {
        $this->markTestIncomplete('Pending Test');
    }

    /** @test */
    public function testStoreANewBook()
    {
        $this->markTestSkipped('skip the test!!');
        $this->post('/books', [
            'title' => 'The invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);

        $this->seeJson(['created' => true])
            ->seeInDatabase('books', ['title' => 'The invisible Man']);
    }

    public function testStore201AndLocationHeaderSuccess()
    {
        $this->markTestSkipped('skip the test!!');
        $this->post('/books', [
            'title' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sin amet',
            'author' => 'Lorem Ipsum'
        ]);

        $this->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/books/[\d]+$#');
    }
}