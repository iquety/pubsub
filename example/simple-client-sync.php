<?php

declare(strict_types=1);

use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$microtime = microtime(true);

$client = new Client();

$response1 = $client->get('http://localhost:8080/simple-server.php');
$response2 = $client->get('http://localhost:8081/simple-server.php');

$totaltime = microtime(true) - $microtime;

echo 'Promise 1: ' . $response1->getBody()->getContents();
echo PHP_EOL;
echo 'Promise 2: ' . $response2->getBody()->getContents();
echo PHP_EOL;
echo 'Total: ' . number_format($totaltime, 1) . ' segundos';
echo PHP_EOL;
