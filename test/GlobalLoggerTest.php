<?php

namespace Test;

use Log\GlobalLogger;

class GlobalLoggerTest extends TestCase
{
    public function testGlobalLogger()
    {
        $filename = $this->getFilename();
        $this->deleteFileIfExits($filename);

        GlobalLogger::info('log info de base utilisant GlobalLogger');
        GlobalLogger::notice('2eme log, cette fois notice, utilisant GlobalLogger');

        self::assertFileExists($filename);
        $content = file($filename);
        self::assertContentString('log info de base utilisant GlobalLogger', $content[0]);
        self::assertContentString('2eme log, cette fois notice, utilisant GlobalLogger', $content[1]);

    }
}
