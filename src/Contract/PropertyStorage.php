<?php

namespace Rkladko\Anyart\Contract;

interface PropertyStorage
{
    public function truncate(): void;
    public function upload(array $propertyData): void;
    public function paginate(int $perPage = 20): array;
}