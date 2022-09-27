<?php

namespace Rkladko\Anyart\Contract;

interface PropertyStorage
{
    public function truncate(): void;
    public function upload(array $propertyData): void;
}