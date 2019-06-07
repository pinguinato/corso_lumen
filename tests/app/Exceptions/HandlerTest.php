<?php

namespace Tests\App\Exceptions;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function testItRespondWithJsonForJsonConsumers()
    {
        $subject = m::mock(Handler::class)->makePartial(); // voglio moccare solo il metodo isDebugMode
        $subject->shouldNotReceive('isDebugMode')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('wantsJson')->andReturn(true);

        $exception = m::mock(\Exception::class, ['Doh!']);
        $exception->shouldReceive('getMessage')->andReturn('Doh!');

        /** @var JsonResponse $result */
        $result = $subject->render($request, $exception);
        $data = $result->getData();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertObjectHasAttribute('error', $data);
        $this->assertAttributeEquals('Doh!', 'message', $data->error);
        $this->assertAttributeEquals(400, 'status', $data->error);
    }

    public function testItProvideJsonResponseForHttpException()
    {
        $subject = m::mock(Handler::class)->makePartial(); // voglio moccare solo il metodo isDebugMode
        $subject->shouldNotReceive('isDebugMode')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('wantsJson')->andReturn(true);

        $examples = [
          [
              'mock' => NotFoundHttpException::class,
              'status' => 404,
              'message' => 'Not Found'
          ],
          [
              'mock' => AccessDeniedHttpException::class,
              'status' => 403,
              'message' => 'Forbidden'
          ],
          [
              'mock' => ModelNotFoundException::class,
              'status' => 404,
              'message' => 'Not Found'
          ]
        ];

        foreach ($examples as $example){
            $exception = m::mock($example['mock']);
            $exception->shouldReceive('getMessage')->andReturn(null);
            $exception->shouldReceive('getStatusCode')->andReturn($example['status']);

            /** @var JsonResponse $result */
            $result = $subject->render($request, $exception);
            $data = $result->getData();

            $this->assertEquals($example['status'], $result->getStatusCode());
            $this->assertEquals($example['message'], $data->error->message);
            $this->assertEquals($example['status'], $data->error->status);
        }
    }
}
