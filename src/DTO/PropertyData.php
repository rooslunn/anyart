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
                'image_full' => $record->image_full,
                'image_thumbnail' => $record->image_thumbnail,
                'latitude' => $record->latitude,
                'longitude' => $record->longitude,
                'num_bedrooms' => $record->num_bedrooms,
                'num_bathrooms' => $record->num_bathrooms,
                'price' => $record->price,
                'property_type_title' => $record->property_type->title,
                'property_type_description' => $record->property_type->description,
                'sale_or_rent' => $record->type,
            ];
        }

        return $result;
    }

    public static function fromJsonStdClass(stdClass $json): PropertyData
    {
        return new self($json);
    }
}