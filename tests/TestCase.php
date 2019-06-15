<?php

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * Print a response containing Content, StatusCode and Headers
     */
    public function printResponse()
    {
        $output = json_decode($this->response->getContent());
        if (is_null($output)) {
            dump($this->response);
        } else {
            dump($output);
        }

        dump($this->response->getStatusCode());
        dump($this->response->headers);
    }

    /**
     * @param $header
     * @return $this
     */
    public function seeHasHeader($header)
    {
        $this->assertTrue(
            $this->response->headers->has($header),
            "Response should has the header '{$header}', but does not"
        );

        return $this;
    }

    /**
     * @param $header
     * @param $regexp
     * @return $this
     */
    public function seeHeaderWithRegExp($header, $regexp)
    {
        $this->seeHasHeader($header)
            ->assertRegExp(
                $regexp,
                $this->response->headers->get($header)
            );

        return $this;
    }

    /**
     * Questo metodo crea un autore nel DB e poi popola un libro con il modello
     * dell'autore appena creato, il count verifica se c'è bisogno di creare
     * uno o più autori da associare ad 1 o più libri
     *
     * @param int $count
     * @return mixed
     */
    public function bookFactory($count = 1)
    {
        $author = factory(\App\Author::class)->create();

        $books = factory(\App\Book::class)->make();

        if ($count === 1){
            $books->author()->associate($author);
            $books->save();
        } else {
            $books->each(function ($book) use ($author) {
                $book->author()->associate($author);
                $book->save();
            });
        }

        return $books;
    }
}
