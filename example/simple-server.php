<?php

declare(strict_types=1);

$wait = rand(1, 3);
sleep($wait);

echo 'Resposta em ' . $wait . ' segundos';
