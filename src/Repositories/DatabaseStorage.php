<?php

namespace Rkladko\Anyart\Repositories;

use Rkladko\Anyart\Contract\PropertyStorage;
use Rkladko\Anyart\Lib\Db;

final class DatabaseStorage implements PropertyStorage
{
    const PROPERTY_TABLE = 'properties';

    private Db $db;

    public function __construct(array $credo)
    {
        $this->db = new Db(
            $credo['db_host'],
            $credo['db_name'],
            $credo['db_user'],
            $credo['db_password']
        );
    }

    public function upload(array $propertyData): void
    {
        $sql = $this->composeBulkInsertSql($propertyData);
        $this->db->query($sql)->execute();
    }

    private function composeBulkInsertSql(array $propertyData): string
    {
        $cols = implode(', ', array_keys($propertyData[0]));

        $values = [];
        foreach ($propertyData as $data) {
            $valuesStr = implode("', '", $data);
            $values[] = sprintf("('%s')", $valuesStr);
        }

        $values = implode(', ', $values);

        return sprintf("insert into %s (%s) values %s", self::PROPERTY_TABLE, $cols, $values);
    }

    public function truncate(): void
    {
        $sql = sprintf('truncate %s', self::PROPERTY_TABLE);
        $this->db->query($sql)->execute();
    }
}