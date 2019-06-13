<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use App\Http\Response\FractalResponse;
use League\Fractal\Serializer\DataArraySerializer;

class FractalServiceProvider extends ServiceProvider
{
    public function register()
    {
        // bind the data array serializer
        $this->app->bind(
          'League\Fractal\Serializer\SerializerAbstract',
          'League\Fractal\Serializer\DataArraySerializer'
        );

        $this->app->bind(FractalResponse::class, function($app){
           $manager = new Manager();
           $serializer = $app['League\Fractal\Serializer\SerializerAbstract'];

           return new FractalResponse($manager, $serializer);
        });

        $this->app->alias(FractalResponse::class, 'fractal');
    }

    public function boot()
    {
        // TODO: Implement boot() method.
    }
}