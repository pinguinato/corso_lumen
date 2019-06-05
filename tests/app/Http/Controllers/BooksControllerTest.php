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
                'title' => 'The War of the Worlds',
                'description' => 'The book is way better than the movie',
                'author' => 'Wells, H.G.'
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

    /** @test */
    public function testStore201AndLocationHeaderSuccess()
    {
        $this->markTestSkipped('Because this create a real book on the database');
        $this->post('/books', [
            'title' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sin amet',
            'author' => 'Lorem Ipsum'
        ]);

        $this->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/books/[\d]+$#');
    }

    /** @test */
    public function updateShouldOnlyChangeFillableFields()
    {
        $this->markTestSkipped('Because update a real book');

        $this->notSeeInDatabase('books',[
           'title' => 'The War of the Worlds'
        ]);

        $this->put('/books/1', [
           'id' => 5,
           'title' => 'The War of the Worlds',
           'description' => 'The book is way better than the movie',
           'author' => 'Wells, H.G.'
        ]);

        $this->seeStatusCode(200)->seeJson([
            'id' => 1,
            'title' => 'The War of the Worlds',
            'description' => 'The book is way better than the movie',
            'author' => 'Wells, H.G.'
        ])->seeInDatabase('books', [
            'title' => 'The War of the Worlds'
        ]);
    }

    /** @test */
    public function shouldFailWithAnInvalidId()
    {
        $this->put('/books/99999999999999')
            ->seeStatusCode(404)
            ->seeJsonEquals([
              'error' => [
                  'message' => 'Book not found'
              ]
            ]);
    }

    /** @test */
    public function updateShouldNoMatchAnInvalidRoute()
    {
        $this->put('/books/this-is-invalid')
            ->seeStatusCode(404);
    }

}