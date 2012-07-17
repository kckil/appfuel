<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Component\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Component\Kernel\CodeCacheArgs;

class CodeCacheArgsTest extends FrameworkTestCase 
{

    /**
     * @param   array   $spec
     * @return  CodeCacheArgs
     */
    public function createArgs(array $spec)
    {
        return new CodeCacheArgs($spec);
    }

    /**
     * @return  array
     */
    public function provideRequiredKeys()
    {
        return array(
            array('classes'),
            array('cache-dir'),
            array('cache-key'),
        );
    }

    /**
     * @return  array
     */
    public function provideInvalidStringsIncludeEmpty()
    {
        return array(
            array(''),
            array(array(1,3,4)),
            array(12345),
            array(new StdClass),
            array(true),
            array(false)
        );
    }

    /**
     * List of required arguments with valid values
     *
     * @return  array
     */
    public function getRequiredArguments()
    {
        return array(
            'classes'   => array('MyClass', 'YourClass'),
            'cache-dir' => 'path/to/cache',
            'cache-key' => 'name-of-file',
        );
    }

    /**
     * @test
     * @return null
     */
    public function requiredArguments()
    {
        $spec = $this->getRequiredArguments();
        $args = $this->createArgs($spec);

        $interface = 'Appfuel\\Component\\Kernel\\CodeCacheArgsInterface';
        $this->assertInstanceOf($interface, $args);
        $this->assertEquals($spec['classes'], $args->getClasses());
        $this->assertEquals($spec['cache-dir'], $args->getCacheDir());
        $this->assertEquals($spec['cache-key'], $args->getCacheKey());
        $this->assertFalse($args->isAutoReload());

        $expected = 'path/to/cache/name-of-file.php';
        $this->assertEquals($expected, $args->getCacheFilePath());

        $expected = "$expected.meta";
        $this->assertEquals($expected, $args->getCacheMetaFilePath());
       
        return $spec; 
    }

    /**
     * @test
     * @depends         requiredArguments
     * @dataProvider    provideRequiredKeys
     */
    public function requiredArgsNotSetFailure($key)
    {
        $spec = $this->getRequiredArguments();
        unset($spec[$key]);
        
        $msg = "-($key) arg is missing";
        $this->setExpectedException('OutOfBoundsException', $msg);
        
        $args = $this->createArgs($spec);
    }

    /**
     * @test
     * @depends         requiredArguments
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */ 
    public function invalidCacheDir($dir)
    {
        $spec = $this->getRequiredArguments();
        $spec['cache-dir'] = $dir;

        $msg = 'cache directory must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $args = $this->createArgs($spec);
    }

    /**
     * @test
     * @depends         requiredArguments
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function invalidCacheKeyFailure($key)
    {
        $spec = $this->getRequiredArguments();
        $spec['cache-key'] = $key;

        $msg = 'cache key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $args = $this->createArgs($spec);
    }
}
