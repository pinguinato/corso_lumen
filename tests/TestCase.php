<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
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
}
