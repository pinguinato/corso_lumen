<?php

namespace Tests\App\Http\Controllers;

use App\Book;
use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BooksControllerTest extends TestCase
{
    use DatabaseMigrations;


    public function testStoreAnewBookIntoTheDatabase()
    {
        $this->markTestSkipped();
        $this->post('/books', [
           'title' => 'The Invisible Man',
           'description' => 'An invisible man is trapped in the terror of his own
creation',
           'author' => 'H. G. Wells'
        ]);

        $body = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('data', $body);

        $data = $body['data'];

        $this->assertEquals('The Invisible Man', $data['title']);
        $this->assertEquals('An invisible man is trapped in the terror of his own
creation', $data['description']);
        $this->assertEquals('H. G. Wells', $data['author']);
        $this->assertTrue($data['id'] > 0);
        $this->seeInDatabase('books', ['title' => 'The Invisible Man']);
    }


    /** @test */
    public function testShowReturnAValidBook()
    {
        $this->markTestSkipped();
        $book = factory('App\Book')->create();

        $this->get("/books/{$book->id}")
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->author
           ]);

        $data = json_decode($this->response->getContent(), true);

        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test */
    public function testShowFailBookNotExists()
    {
        $this->get('/books/99999', ['Accept' => 'application/json'])
            ->seeStatusCode(404)
            ->seeJson([
                    'message' => 'Not Found',
                    'status' => 404
            ]);
    }

    /** @test */
    public function testStoreANewBook()
    {
        $this->post('/books', [
            'title' => 'The invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);

        $this->seeJsonStructure([
            'data' => [
                'title',
                'description',
                'author',
                'updated_at',
                'created_at',
                'id',
            ]
        ]);

        $this->seeInDatabase('books', ['title' => 'The invisible Man']);
    }

    /** @test */
    public function testStore201AndLocationHeaderSuccess()
    {
        $this->post('/books', [
            'title' => 'Lorem ipsum',
            'description' => 'Lorem ipsum dolor sin amet',
            'author' => 'Lorem Ipsum'
        ]);

        $this->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/books/[\d]+$#');
    }

    /** @test */
    public function testUpdateShouldOnlyChangeFillableFields()
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

    /** #@test */
    public function testRefactoringUpdateShouldOnlyChangeFillableFields()
    {
        $book = factory('App\Book')->create([
           'title' =>  'The War of the Worlds',
           'description' => 'The book is way better than the movie',
           'author' => 'Wells, H.G.'
        ]);

        $this->seeInDatabase('books', [
           'title' =>  'The War of the Worlds',
            'description' => 'The book is way better than the movie',
            'author' => 'Wells, H.G.'
        ]);

        $this->put("/books/{$book->id}", [
           'id' => 5,
           'title' => 'Book updated',
           'description' => 'The book is way better than the movie',
           'author' => 'Roberto Gianotto'
        ]);

        $this->seeStatusCode(200)
            ->seeJson([
               'id' => 1,
               'title' => 'Book updated',
               'description' =>  'The book is way better than the movie',
               'author' => 'Roberto Gianotto'
            ])
           ->seeInDatabase('books', [
              'title' => 'Book updated'
           ]);
    }

    /** #@test */
    public function testRefactorRemoveAValidBook()
    {
        $book = factory('App\Book')->create();

        $this->delete("/books/{$book->id}")->seeStatusCode(204)->isEmpty();

        $this->notSeeInDatabase('books', ['id' => $book->id]);
    }

    /** @test */
    public function testShouldFailWithAnInvalidId()
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
    public function testUpdateShouldNoMatchAnInvalidRoute()
    {
        $this->put('/books/this-is-invalid')
            ->seeStatusCode(404);
    }

    /** #@test */
    public function testRemoveAValidBook()
    {
        $this->markTestSkipped();
        $this->delete('/books/13')
            ->seeStatusCode(204)
            ->isEmpty();

        $this->notSeeInDatabase('books', ['id' => 13]);
    }

    /** #@test */
    public function testRemoveReturnA404WithAnInvalidId()
    {
        $this->delete('/books/999999999999')
            ->seeStatusCode(404)
            ->seeJsonEquals([
               'error' => [
                   'message' => 'Book not found'
               ]
            ]);
    }

    /** #@test */
    public function testRemoveShouldNotMathAValidRoute()
    {
        $this->delete('/books/this-is-invalid')
            ->seeStatusCode(404);
    }

    /** #@test */
    public function indexShouldReturnACollectionOfRecords()
    {
        $books = factory('App\Book', 2)->create();

        $this->get('/books');

        // nuova modifica con Fractal
        $expected = [
          'data' => $books->toArray()
        ];



        $this->seeJsonEquals($expected);
        // fine nuova versione con Fractal

//        foreach ($books as $book){
//            $this->seeJson([
//               'title' => $book->title
//            ]);
//        }
    }

    /** @test */
    public function testShouldReturnAValidBookNewVersion()
    {
        $book = factory('App\Book')->create();

        $expected = [
            'data' => $book->toArray()
        ];

        $this->get("/books/{$book->id}")->seeStatusCode(200)->seeJsonEquals($expected);
    }

    /** @test */
    public function testIndexStatusCodeIs200()
    {
        $this->get('/books')->seeStatusCode(200)->seeJson([]);
    }
}