<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 16/05/2019
 * Time: 15:20
 */

require __DIR__ . './../vendor/autoload.php';

chdir(__DIR__);

$logger = new \Log\Logger();
$logger->info('1ere ligne de log' . PHP_EOL . '2eme ligne de logue');