<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 15:29
 */

require __DIR__ . './../vendor/autoload.php';

chdir(__DIR__);

session_start();

$logger = new \Log\Logger();
$logger->info('log info avec session demarre');

