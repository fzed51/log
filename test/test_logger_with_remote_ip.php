<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 15:29
 */

require __DIR__ . './../vendor/autoload.php';

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

chdir(__DIR__);

$logger = new \Log\Logger();
$logger->info('log info avec remote ip');

