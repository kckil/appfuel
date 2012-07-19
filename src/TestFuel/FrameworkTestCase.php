<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Testfuel;

use stdClass,
    PHPUnit_Framework_TestCase;

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

    /** 
     * @return  array 
     */ 
    public function provideInvalidStrings() 
    { 
        return array( 
            array(array(1,3,4)), 
            array(12345), 
            array(new stdClass), 
            array(true), 
            array(false) 
        ); 
    }
                                                                            
    /**
     * @return  array
     */
    public function provideInvalidStringsIncludeEmpty()                          
    {
        $args = $this->provideInvalidStrings();
        array_unshift($args, array(''));
                                                                                 
        return $args; 
    }

    /**
     * @return  array
     */
    public function provideInvalidScalars()
    {
        return array(
            array(new stdClass),
            array(array(1,2,3)),
        );
    }

    /**
     * @return  array
     */
    public function provideInvalidScalarsIncludeNull()
    {
        $args = $this->provideInvalidScalars();
        array_unshift($args, array(''));
     
        return $args;
    }
}
