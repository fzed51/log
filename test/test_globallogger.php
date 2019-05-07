<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 15:29
 */

use Log\GlobalLogger;

require __DIR__ . './../vendor/autoload.php';

chdir(__DIR__);

GlobalLogger::info('log info de base utilisant GlobalLogger');
GlobalLogger::notice('2eme log, cette fois notice, utilisant GlobalLogger');

