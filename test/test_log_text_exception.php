<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 12/06/2019
 * Time: 10:10
 */

require __DIR__ . './../vendor/autoload.php';

chdir(__DIR__);

function throwException() {
    throw new \Exception("Exception levée!");
}

try {
    throwException();
} catch (\exception $ex) {
    $logger = new \Log\Logger();
    $exceptionName = get_class($ex);
    $exceptionCode = $ex->getCode();
    $exceptionStr = $ex;
    $logger->error("exception -> $exceptionName($exceptionCode)");
    $logger->debug($exceptionStr);
}