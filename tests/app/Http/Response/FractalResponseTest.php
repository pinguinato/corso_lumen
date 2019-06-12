<?php

use App\Http\Response\FractalResponse;
use Mockery as m;
use \League\Fractal\Manager;
use League\Fractal\Serializer\SerializerAbstract;

class FractalResponseTest extends TestCase
{
    /** @test */
    public function testItCanBeInitialized()
    {
        $this->assertInstanceOf(FractalResponse::class, new FractalResponse(new Manager(), new League\Fractal\Serializer\ArraySerializer()));
    }

    /** @test */
    public function testItCanBeInitializedRefactor()
    {
        // dipendenze di Fractal
        $fractal_manager = m::mock(Manager::class);
        $fractal_serializer = m::mock(SerializerAbstract::class);

        $fractal_manager->shouldReceive('setSerializer')->with($fractal_serializer)->once()->andReturn($fractal_manager);

        $fractal = new FractalResponse($fractal_manager, $fractal_serializer);

        $this->assertInstanceOf(FractalResponse::class, $fractal);
    }

    /** @test */
    public function testItCanTransformAnItem()
    {
        //transformer
        $transformer = m::mock('League\Fractal\TransformerAbstract');

        // scope
        $scope = m::mock('League\Fractal\Scope');
        $scope->shouldReceive('toArray')->once()->andReturn(['foo' => 'bar']);

        // Serializer
        $serializer = m::mock('League\Fractal\Serializer\SerializerAbstract');
        $manager = m::mock('League\Fractal\Manager');
        $manager->shouldReceive('setSerializer')->with($serializer)->once();
        $manager->shouldReceive('createData')->once()->andReturn($scope);
        $subject = new FractalResponse($manager, $serializer);

        $this->assertInternalType('array', $subject->item(['foo' => 'bar'], $transformer));
    }

    public function testItCanTransformACollection()
    {
        $data = [
            [
                'foo' => 'bar'
            ],
            [
                'fizz' => 'buzz'
            ]
        ];

        //transformer
        $transformer = m::mock('League\Fractal\TransformerAbstract');

        // scope
        $scope = m::mock('League\Fractal\Scope');
        $scope->shouldReceive('toArray')->once()->andReturn($data);

        // Serializer
        $serializer = m::mock('League\Fractal\Serializer\SerializerAbstract');

        $manager = m::mock('League\Fractal\Manager');

        $manager->shouldReceive('setSerializer')->with($serializer)->once();

        $manager->shouldReceive('createData')->once()->andReturn($scope);

        $subject = new FractalResponse($manager, $serializer);

        $this->assertInternalType('array', $subject->collection($data, $transformer));
    }
}