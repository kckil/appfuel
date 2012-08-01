<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Testfuel;

use StdClass,
    SplFileInfo,
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
     * @return  string
     */
    public function getPathCollectionInterface()
    {
        return 'Appfuel\\Filesystem\\PathCollectionInterface';
    }

    /**
     * @return  string
     */
    public function getFileHandlerInterface()
    {
        return 'Appfuel\\Filesystem\\FileHandlerInterface';
    }

    /**
     * @return  string
     */
    public function getArrayDataInterface()
    {
        return 'Appfuel\\DataStructure\\ArrayDataInterface';
    }

    /** 
     * @return  array 
     */ 
    public function provideInvalidStrings() 
    { 
        return array( 
            array(array(1,3,4)), 
            array(12345), 
            array(new StdClass), 
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
            array(new StdClass),
            array(array(1,2,3)),
            array(new SplFileInfo('some/file'))                                  
        );
    }

    /**
     * @return  array
     */
    public function provideInvalidScalarsIncludeNull()
    {
        $args = $this->provideInvalidScalars();
        array_unshift($args, array(null));
     
        return $args;
    }

    /**
     * @return  array
     */
    public function provideInvalidInts()
    {
        return array(
            array('abcde'),
            array(''),
            array(new StdClass),
            array(array(1,2,3)),
        );
    }
}
