<?php

namespace Test;
/**
 * test de base TestCase
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * donne le nom par defaut du fichier de log
     * @return string
     */
    protected function getFilename(): string
    {
        return './log/log_' . ((new \DateTime())->format('Ymd')) . '.log';
    }

    /**
     * supprime un fichier si il existe
     * @param string $filename
     * @return bool
     */
    protected function deleteFileIfExits($filename = ""): bool
    {
        if (empty($filename)) {
            $filename = $this->getFilename();
        }
        if (is_file($filename)) {
            return unlink($filename);
        }
        return true;
    }

    protected static function assertContentString(string $needle, string $haystack, string $message = ""): void
    {
        $position = strpos($haystack, $needle);
        self::assertIsInt($position, $message);
    }

    protected static function assertContentStringNoCaseSensitive(
        string $needle,
        string $haystack,
        string $message = ""
    ): void {
        self::assertContentString(strtolower($needle), strtolower($haystack), $message);
    }

}