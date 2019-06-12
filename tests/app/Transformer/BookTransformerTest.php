<?php

namespace Tests\App\Transformer;

use TestCase;
use App\Transformer\BookTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class BookTransformerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_transform_a_book_model()
    {
        $book = factory('App\Book')->create();

        $subject = new BookTransformer();

        $this->assertInstanceOf(TransformerAbstract::class, $subject);

        $transform = $subject->transform($book);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('title', $transform);
        $this->assertArrayHasKey('description', $transform);
        $this->assertArrayHasKey('author', $transform);
        $this->assertArrayHasKey('created_at', $transform);
        $this->assertArrayHasKey('updated_at', $transform);
    }
}
