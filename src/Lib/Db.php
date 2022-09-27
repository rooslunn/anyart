<?php

namespace Rkladko\Anyart\Lib;

use PDO;
use PDOStatement;
use Throwable;

final class Db
{
    private PDO $dbh;
    private PDOStatement $stmt;

    public function __construct(
        private readonly string $host,
        private readonly string $db_name,
        private readonly string $db_user,
        private readonly string $db_pass,
    )
    {
        $this->createPDO();
    }

    public function query(string $sql): Db
    {
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    public function bind(string $param, $value, int $type = null): Db
    {
        if (is_null($type)) {
            $type = $this->guessParamType($value);
        }
        $this->stmt->bindValue($param, $value, $type);

        return $this;
    }

    public function execute(): bool
    {
        try {
            return $this->stmt->execute();
        } catch (Throwable $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function fetchAll()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    public function fetchOne()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    private function guessParamType($value): int
    {
        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }

    private function createPDO(
        string $dsn = null,  array $options = null
    ): void
    {
        $dsn = $dsn ?? $this->buildDSN();
        $options = $options ?? $this->defaultOptions();
        try {
            $this->dbh = new PDO(
                $dsn, $this->db_user, $this->db_pass, $options
            );
        } catch (Throwable $th) {
            $error = $th->getMessage();
            echo $error;
        }
    }

    private function buildDSN(): string
    {
        return "mysql:host=$this->host;dbname=$this->db_name";
    }

    private function defaultOptions(): array
    {
        return [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];
    }
}
