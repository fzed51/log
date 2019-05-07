<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 15:29
 */

require __DIR__ . './../vendor/autoload.php';

chdir(__DIR__);

$logger = new \Log\Logger(Psr\Log\LogLevel::ERROR);
$logger->info('ce log ne doit pas etre ecrit');
$logger->error('ce log doit etre ecrit');

