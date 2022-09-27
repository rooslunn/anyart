<?php

use Rkladko\Anyart\Contract\PropertyStorage;

final class PropertyController
{
    public function __construct(
        private readonly PropertyStorage $storage
    )
    {

    }

    /**
     * @throws JsonException
     */
    public function index(): void
    {

        $data = $this->storage->paginate();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}