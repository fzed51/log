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
            $levelMax = array_search($levelMax, self::$dico, true);
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
        $log = sprintf(
            '[%s] [%s] [%s] > %s',
            $this->getSessionId(),
            $this->getInstanceId(),
            $this->levelToStr($level),
            empty($context)
                ? $message
                : static::interpolate($message, $context)
        );
        error_log($log);
    }

    /**
     * retourne l'identifiant unique de la session
     * @return string
     */
    protected function getSessionId(): string
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return session_id();
        }
        return '';
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
        if (!isset(self::$dico[$level])) {
            throw new InvalidArgumentException("'$level' n'est pas un niveau de log valide.");
        }
        return self::$dico[$level];
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
        $log = strtr($message, $replace);

        return $log;
    }
}