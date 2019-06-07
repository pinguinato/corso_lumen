<?php

namespace Tests\App\Exceptions;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HandlerTest extends TestCase
{
    /** @test */
    public function testItRespondWithHtmlWhenJsonIsNotAccepted()
    {
        $subject = m::mock(Handler::class)->makePartial(); // voglio moccare solo il metodo isDebugMode
        $subject->shouldNotReceive('isDebugMode');

        // mock interaction with the Request
        $request = m::mock(Request::class);
        $request->shouldReceive('wantsJson')->andReturn(false);

        //mock the interaction with the Exception
        $exception = m::mock(\Exception::class, ['Error!']);
        $exception->shouldNotReceive('getStatusCode');
        $exception->shouldNotReceive('getTrace');
        $exception->shouldNotReceive('getMessage');

        // call the method under test, non è un mock
        $result = $subject->render($request, $exception);

        //asserisco che il metodo render non ritorna JSON
        $this->assertNotInstanceOf(JsonResponse::class, $result);
    }
}
