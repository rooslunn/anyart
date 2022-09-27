<?php

namespace Rkladko\Anyart\DTO;

use stdClass;

final class PropertyData
{
    private readonly stdClass $json;

    private function __construct(stdClass $json)
    {
        $this->json = $json;
    }

    public function exportForDb(): array
    {
        $result = [];

        foreach ($this->json->data as $record) {
            $result[] = [
                'county' => $record->county,
                'country' => $record->country,
                'town' => $record->town,
                'description' => $record->description,
                'address' => $record->address,
                'price' => $record->price,
            ];
        }

        return $result;
    }

    public static function fromJsonStdClass(stdClass $json): PropertyData
    {
        return new self($json);
    }
}