<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BooksControllerValidationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testItValidatesRequiredFieldsWhenCreatingANewBook()
    {
        $this->post('/books', [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true );

        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('author', $body);

        $this->assertEquals(["The title field is required."], $body['title']);
        $this->assertEquals(["Please provide a description."], $body['description']);
        $this->assertEquals(["The author field is required."], $body['author']);
    }

    /** @test */
    public function testItValidatesRequiredFieldsWhenUpgradingABook()
    {
        $book = factory(\App\Book::class)->create();

        $this->put("/books/{$book->id}", [], ['Accept' => 'application/json']);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->response->getStatusCode());

        $body = json_decode($this->response->getContent(), true );

        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('author', $body);

        $this->assertEquals(["The title field is required."], $body['title']);
        $this->assertEquals(["Please provide a description."], $body['description']);
        $this->assertEquals(["The author field is required."], $body['author']);
    }

    /** @test */
    public function testTitleFailsCreateValidationWhenJustTooLong()
    {
        $book = factory(\App\Book::class)->make(); // invece di usare create(), perché permette di avere un modello non salvato in maniera per sistente nel DB
        $book->title = str_repeat('a', 256);

        $this->post("/books", [
           'title' => $book->title,
           'description' => $book->description,
           'author' => $book->author
        ], ['Accept' => 'application/json']);

        $this->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJson(['title' => ["The title may not be greater than 255 characters."]])
            ->notSeeInDatabase('books', ['title' => $book->title]);
    }

    public function testTitleFailsUpdatingValidationWhenJustTooLong()
    {
        $book = factory(\App\Book::class)->create();
        $book->title = str_repeat('a', 256);

        $this->put("/books/{$book->id}", [
            'title' => $book->title,
            'description' => $book->description,
            'author' => $book->author
        ], ['Accept' => 'application/json']);

        $this->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJson(['title' => ["The title may not be greater than 255 characters."]])
            ->notSeeInDatabase('books', ['title' => $book->title]);
    }

    public function testTitlePassingCreateValidationWhenExactlyMax()
    {
        $book = factory(\App\Book::class)->make();
        $book->title = str_repeat('a', 255);

        $this->post("/books", [
            'title' => $book->title,
            'description' => $book->description,
            'author' => $book->author
        ], ['Accept' => 'application/json']);

        $this->seeStatusCode(Response::HTTP_CREATED)
            ->seeInDatabase('books', ['title' => $book->title]);
    }

    public function testTitlePassingUpdatingValidationWhenExactlyMax()
    {
        $book = factory(\App\Book::class)->create();
        $book->title = str_repeat('a', 255);

        $this->put("/books/{$book->id}", [
            'title' => $book->title,
            'description' => $book->description,
            'author' => $book->author
        ], ['Accept' => 'application/json']);

        $this->seeStatusCode(Response::HTTP_OK)
            ->seeInDatabase('books', ['title' => $book->title]);
    }
}
