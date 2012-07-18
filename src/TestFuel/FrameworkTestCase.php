<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Testfuel;

use PHPUnit_Framework_TestCase;

class FrameworkTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Calculates the absolute path to the Fixtures directory in the whatever
     * concrete class's directory that calls this method.
     *
     * @return  string
     */
    protected function getFixturePath()
    {
        /* absolute path to TestFuel directory */
        $testPath = dirname(__DIR__);

        /* relative path to the concrete classes directory */
        $path = str_replace('\\', '/', get_class($this));
        $relativePath = dirname($path);

        return "$testPath/$relativePath/Fixtures"; 
    }
}
