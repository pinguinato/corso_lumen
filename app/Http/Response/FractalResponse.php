<?php

namespace App\Http\Response;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;


class FractalResponse
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Manager $manager, SerializerAbstract $serializer)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->manager->setSerializer($serializer);
    }

    /**
     * @param $data
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @return array
     */
    public function item($data, TransformerAbstract $transformer, $resourceKey = null)
    {
//        $resource = new Item($data, $transformer, $resourceKey);

//        return $this->manager->createData($resource)->toArray();

        return $this->createDataArray(
          new Item($data, $transformer, $resourceKey)
        );
    }

    /**
     * @param $data
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @return array
     */
    public function collection($data, TransformerAbstract $transformer, $resourceKey = null)
    {
//        $resource = new Collection($data, $transformer, $resourceKey);

//        return $this->manager->createData($resource)->toArray();

        return $this->createDataArray(
            new Collection($data, $transformer, $resourceKey)
        );
    }

    /**
     * @param ResourceInterface $resource
     * @return array
     */
    public function createDataArray(ResourceInterface $resource)
    {
        return $this->manager->createData($resource)->toArray();
    }
}
