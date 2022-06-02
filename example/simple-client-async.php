<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;

require __DIR__ . '/../vendor/autoload.php';

$microtime = microtime(true);

$client = new Client();

$promise1 = $client->getAsync('http://localhost:8080/simple-server.php');
$promise2 = $client->getAsync('http://localhost:8081/simple-server.php');

/** @var array<ResponseInterface> $responses */
$responses = Utils::unwrap([
    $promise1,
    $promise2
]);

$totaltime = microtime(true) - $microtime;

echo 'Promise 1: ' . $responses[0]->getBody()->getContents();
echo PHP_EOL;
echo 'Promise 2: ' . $responses[1]->getBody()->getContents();
echo PHP_EOL;
echo 'Total: ' . number_format($totaltime, 1) . ' segundos';
echo PHP_EOL;
