<?php

require_once "./vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Rkladko\Anyart\Contract\PropertyStorage;
use Rkladko\Anyart\DTO\PropertyData;
use Rkladko\Anyart\Repositories\DatabaseStorage;

const BASE_URL = 'https://trial.craig.mtcserver15.com/api/properties';
const API_KEY = '2S7rhsaq9X1cnfkMCPHX64YsWYyfe1he';
const PER_PAGE = 50;

/**
 * Helpers
 */
function query_page(int $page): string {
    return '?' . http_build_query([
        'page[number]' => $page,
        'page[size]' => PER_PAGE,
        'api_key' => API_KEY,
    ]);
}

function process_body(string $body, PropertyStorage $storage): int {
    $total = 0;
    try {
        $json = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        $total = $json->last_page;
        $property = PropertyData::fromJsonStdClass($json)->exportForDb();
        $storage->upload($property);
    } catch (JsonException $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    return $total;
}

function info(string $info): void
{
    echo sprintf("[%s] %s\n", date("H:i:s"), $info);
}

/**
 * first request to take control over (get total and check db)
 */

$db_credo = require 'config/db.php';
$db_storage = new DatabaseStorage($db_credo);

info('Empty storage...');
$db_storage->truncate();

$client = new Client();

try {
    info("Getting total and first " . PER_PAGE . ' records...');
    $response = $client->get(BASE_URL . query_page(1));
    $total_pages = process_body($response->getBody(), $db_storage);
    info(sprintf("Total: %s", $total_pages));
} catch (GuzzleException $e) {
    info($e->getMessage());
    exit(-1);
}


/**
 * Main
 */

--$total_pages;

$requests = static function ($total) {
    for ($i = 2; $i < $total; $i++) {
        $uri = BASE_URL . query_page($i);
        yield new Request('GET', $uri);
    }
};

$pool = new Pool($client, $requests($total_pages), [
    'concurrency' => 1,
    'fulfilled' => static function (Response $response, $index) use ($db_storage) {
        info('Processing page: ' . $index);
        process_body($response->getBody(), $db_storage);
        info('Done');
    },
    'rejected' => static function (RequestException $reason, $index) {
        info('Error while processing page: ' . $index);
        info($reason->getMessage());
    },
]);

$promise = $pool->promise();

$promise->wait();