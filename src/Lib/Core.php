<?php

namespace Rkladko\Anyart\Lib;

use Rkladko\Anyart\Repositories\DatabaseStorage;


final class Core
{
    private string $apiController = 'PropertyController';
    private string $apiMethod = 'index';

    public function __construct(array $db_credo)
    {
        $this->processRequest($db_credo);
    }

    private function processRequest(array $db_credo): void
    {
        $url = $this->getUrl();
        $params = $url ? array_values($url) : [];
        if ($this->controllerLoaded($this->apiController)) {
            $storage = new DatabaseStorage($db_credo);
            call_user_func_array(
                [new $this->apiController($storage), $this->apiMethod], $params
            );
        }
    }

    private function controllerLoaded(string $name): bool
    {
        $path = dirname(__DIR__) . '/Controller/' . $name . '.php';
        try {
            require_once $path;
            return true;
        } catch (Throwable $e) {
            syslog(LOG_ERR, $e->getMessage());
            return false;
        }
    }

    private function getUrl(): array
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);

        return explode('/', $url);
    }
}