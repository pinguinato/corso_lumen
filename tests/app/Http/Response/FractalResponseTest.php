<?php

use App\Http\Response\FractalResponse;

class FractalResponseTest extends TestCase
{
    /** @test */
    public function it_can_be_initialized()
    {
        $this->assertInstanceOf(FractalResponse::class, new FractalResponse());
    }
}