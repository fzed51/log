<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 15:16
 */

namespace Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class GlobalLogger
{
    /**
     * logger
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger(): LoggerInterface
    {
        return self::$logger;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * System is unusable.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function emergency($message, array $context = [])
    {
        static::log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * send log to logger
     * @param $level
     * @param $message
     * @param array $context
     * @throws \Exception
     */
    protected static function log($level, $message, array $context = [])
    {
        if (static::$logger === null) {
            static::$logger = new Logger();
        }
        static::$logger->log($level, $message, $context);
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function alert($message, array $context = [])
    {
        static::log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function critical($message, array $context = [])
    {
        static::log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function error($message, array $context = [])
    {
        static::log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function warning($message, array $context = [])
    {
        static::log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function notice($message, array $context = [])
    {
        static::log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function info($message, array $context = [])
    {
        static::log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     * @param string $message
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public static function debug($message, array $context = [])
    {
        static::log(LogLevel::DEBUG, $message, $context);
    }
}
