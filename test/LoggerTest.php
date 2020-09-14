<?php

namespace Test;

use Exception;
use Psr\Log\LogLevel;

/**
 * @throws \Exception
 */
function throwException()
{
    throw new Exception("Exception levée!");
}

class LoggerTest extends TestCase
{
    public function testLogTextException(): void
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);
        try {
            throwException();
        } catch (exception $ex) {
            $logger = new \Log\Logger();
            $exceptionName = get_class($ex);
            $exceptionCode = $ex->getCode();
            $exceptionStr = $ex;
            $logger->error("exception -> $exceptionName($exceptionCode)");
            $logger->debug($exceptionStr);
        }

        self::assertFileExists($filename);
        $content = file($filename);
        self::assertCount(2, $content);
        self::assertContentString('exception -> Exception(0)', $content[0]);
        self::assertContentString('Exception: Exception levée! in ' . __FILE__, $content[1]);
    }

    public function testLogger()
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);
        $logger = new \Log\Logger();
        $logger->info('log info de base');
        self::assertFileExists($filename);
        $content = file($filename);
        self::assertCount(1, $content);
        // [2020-09-14T23:00:29+02:00] [D:/serveur/www/log/vendor/phpunit/phpunit/phpunit] [a0f907c7d6af2ed2ad6e2492] [info] > log info de base
        self::assertMatchesRegularExpression(
            '/^\[[0-9\-TZ\:\+]+\] \[[^\]]+\] \[[0-9a-f]{24}\] \[info\] > .+/',
            $content[0]
        );
        self::assertContentString('log info de base', $content[0]);
    }

    public function testLoggerWithLevelMax()
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);
        $logger = new \Log\Logger(LogLevel::ERROR);
        $logger->info('ce log ne doit pas etre ecrit');
        $logger->error('ce log doit etre ecrit');
        self::assertFileExists($filename);
        $content = file($filename);
        self::assertCount(1, $content);
        self::assertContentString('ce log doit etre ecrit', $content[0]);
    }

    public function testLoggerWithRemoteIp()
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $logger = new \Log\Logger();
        $logger->info('log info avec remote ip');
        self::assertFileExists($filename);
        $content = file($filename);
        self::assertCount(1, $content);
        self::assertContentString('] [127.0.0.1] [', $content[0]);
    }

    public function testLoggerMultiLine()
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);
        $logger = new \Log\Logger();
        $logger->info('1ere ligne de log' . PHP_EOL . '2eme ligne de logue');
        self::assertFileExists($filename);
        $content = file($filename);
        self::assertCount(1, $content);
        self::assertContentString('1ere ligne de log § 2eme ligne de logue', $content[0]);
    }
}
