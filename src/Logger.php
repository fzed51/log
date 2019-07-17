<?php

declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 07/05/2019
 * Time: 14:21
 */

namespace Log;


use Exception;
use InvalidArgumentException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use RuntimeException;

class Logger extends AbstractLogger
{
    /**
     * identifiant d'instance unique à l'execution du script
     * @var string
     */
    protected static $instanceId;

    private static $dico = [
        LogLevel::EMERGENCY => 'emergency',
        LogLevel::ALERT => 'alert',
        LogLevel::CRITICAL => 'critical',
        LogLevel::ERROR => 'error',
        LogLevel::WARNING => 'warning',
        LogLevel::NOTICE => 'notice',
        LogLevel::INFO => 'info',
        LogLevel::DEBUG => 'debug',
    ];
    private static $level = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    protected $levelMax;

    /**
     * Logger constructor.
     * @param int|string|null $levelMax
     * @throws \Exception
     */
    public function __construct($levelMax = null)
    {
        if (self::$instanceId === null) {
            self::$instanceId = self::generateUuid(12);
            $logFolder = '.' . DIRECTORY_SEPARATOR . 'log';
            self::controlOrCreateDirectory($logFolder);
            $logFile = $logFolder . DIRECTORY_SEPARATOR . 'log_' . date('Ymd') . '.log';
            ini_set('error_log', $logFile);
            ini_set('log_errors', '1');
        }

        if (is_string($levelMax)) {
            $levelMax = $this->levelToInt($levelMax);
        }
        if (is_int($levelMax)) {
            $this->levelMax = $levelMax;
        } else {
            $this->levelMax = count(self::$dico);
        }
    }

    /**
     * genere un id unique
     * @param int $length
     * @return string
     * @throws \Exception
     */
    private static function generateUuid(
        int $length
    ): string {
        $buff = random_bytes($length);
        return bin2hex($buff);
    }

    private static function controlOrCreateDirectory($directory)
    {
        if (!is_dir($directory) && !mkdir($directory) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Le dossier "%s" ne peut pas etre cree', $directory));
        }
    }

    /**
     * Logs with an arbitrary level.
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->testLevel($level);

        if ($this->levelToInt($level) > $this->levelMax) {
            return;
        }
        $messageConstruit = empty($context)
            ? $message
            : static::interpolate($message, $context);
        $log = sprintf(
            '[%s] [%s] [%s] > %s',
            $this->getRemote(),
            $this->getInstanceId(),
            $this->levelToStr($level),
            str_replace(["\r\n", PHP_EOL, "\n", "\r"], ' § ', $messageConstruit)
        );
        error_log($log);
    }

    /**
     * retourne l'adresse IP de l'emetteur de la requete
     * si il n'y a pas d'adresse disponnible retourne le
     * script php à l'origine de l'exécution;
     * @return string
     */
    protected function getRemote(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? $_SERVER['PHP_SELF'] ?? '';
    }

    /**
     * retourne l'identifiant unique du processus
     * @return string
     */
    protected function getInstanceId(): string
    {
        return self::$instanceId;
    }

    /**
     * @param mixed $level
     * @return string
     */
    private function levelToStr($level): string
    {
        return self::$dico[$level];
    }

    /**
     * @param mixed $level
     * @return void;
     */
    private function testLevel($level)
    {
        if (!isset(self::$dico[$level])) {
            throw new InvalidArgumentException("'$level' n'est pas un niveau de log valide.");
        }
    }

    /**
     * @param mixed $level
     * @return int
     */
    private function levelToInt($level): int
    {
        return self::$level[$level] ?: count(self::$level);
    }

    protected static function interpolate($message, array $context = [])
    {
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            } elseif (is_a($val, Exception::class)) {
                /* @var  \Exception $val */
                $replace['{' . $key . '}'] = $val->getMessage();
            }
        }
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
