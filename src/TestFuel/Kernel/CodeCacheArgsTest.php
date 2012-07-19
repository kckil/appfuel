<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Component\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Kernel\CodeCacheArgs,
    Appfuel\Filesystem\FileHandlerInterface;

/**
 * Test the value object that holds parameters for the CodeCacheHandler
 */
class CodeCacheArgsTest extends FrameworkTestCase 
{

    /**
     * @return  string
     */
    public function getFileHandlerInterface()
    {
        return 'Appfuel\\Filesystem\\FileHandlerInterface';
    }

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

        $interface = 'Appfuel\\Kernel\\CodeCacheArgsInterface';
        $this->assertInstanceOf($interface, $args);
        $this->assertEquals($spec['classes'], $args->getClasses());
        $this->assertEquals($spec['cache-dir'], $args->getCacheDir());
        $this->assertEquals($spec['cache-key'], $args->getCacheKey());
        $this->assertFalse($args->isAutoReload());

        $expected = 'path/to/cache/name-of-file.php';
        $this->assertEquals($expected, $args->getCacheFilePath());

        $expected = "$expected.meta";
        $this->assertEquals($expected, $args->getCacheMetaFilePath());
    
        $fileHandler = $args->getFileHandler();
        $interface = $this->getFileHandlerInterface();
        $this->assertInstanceOf($interface, $fileHandler);
        return $spec; 
    }

    /**
     * @test
     * @param   array   $spec
     * @depends requiredArguments
     */
    public function optionalAutoReload(array $spec)
    {
        $spec['auto-reload'] = true;
        $args = $this->createArgs($spec);
        $this->assertTrue($args->isAutoReload());


        $spec['auto-reload'] = false;
        $args = $this->createArgs($spec);
        $this->assertFalse($args->isAutoReload());

        /* must be a strict true */
        $spec['auto-reload'] = 1;
        $args = $this->createArgs($spec);
        $this->assertFalse($args->isAutoReload()); 
    }

    /**
     * @test
     * @param   array   $spec
     * @depends requiredArguments
     */
    public function optionalFileExtension(array $spec)
    {
        $spec['ext'] = '.inc';
        $args = $this->createArgs($spec);

        $dir = $args->getCacheDir();
        $key = $args->getCacheKey();
        
        $expected = "{$dir}/{$key}.inc";
        $this->assertEquals($expected, $args->getCacheFilePath());
    
        $expected = "$expected.meta";
        $this->assertEquals($expected, $args->getCacheMetaFilePath());

        unset($spec['ext']);

        return $spec;
    }

    /**
     * @test
     * @param   array   $spec
     * @depends optionalFileExtension
     */
    public function optionalFileExtensionEmptyString(array $spec)
    {
        $spec['ext'] = '';
        $args = $this->createArgs($spec);

        $dir = $args->getCacheDir();
        $key = $args->getCacheKey();
        
        $expected = "{$dir}/{$key}";
        $this->assertEquals($expected, $args->getCacheFilePath());
    
        $expected = "$expected.meta";
        $this->assertEquals($expected, $args->getCacheMetaFilePath());
    }

    /**
     * @test
     * @param   array   $spec
     * @depends optionalFileExtension
     */
    public function overrideFileHander(array $spec)
    {
        $handler = $this->getMock($this->getFileHandlerInterface());
        $spec['file-handler'] = $handler;
        $args = $this->createArgs($spec);
        $this->assertSame($handler, $args->getFileHandler());

        unset($spec['file-handler']);

        return $spec;
    }

    /**
     * @test
     * @param   array   $spec
     * @depends overrideFileHander
     */
    public function overrideFileHandlerFailure(array $spec)
    {
        $interface = $this->getFileHandlerInterface();
        $msg = "file handler must implement -($interface)";
        $this->setExpectedException('OutOfBoundsException', $msg);

        $spec['file-handler'] = new stdClass;
        $args = $this->createArgs($spec);
    }

    /**
     * @test
     * @param           mixed   $ext
     * @depends         optionalFileExtension
     * @dataProvider    provideInvalidStrings
     */
    public function fileExtensionFailure($ext)
    {
        $spec = $this->getRequiredArguments();
        $spec['ext'] = $ext;
        
        $msg = "file extension must be a string";
        $this->setExpectedException('OutOfRangeException', $msg);
        
        $args = $this->createArgs($spec);
    }

    /**
     * @test
     * @param   array   $spec
     * @depends requiredArguments
     */
    public function filePathsWithAdaptiveTrue(array $spec)
    {
        $spec['adaptive'] = true;
        $spec['classes'] = array(
            'MyClass', 
            'YourClass', 
            'stdClass', 
            'Exception'
        );
        $args = $this->createArgs($spec);

        $dir  = $args->getCacheDir();
        $key  = $args->getCacheKey();
        $hash = substr(md5(implode('|', array('MyClass', 'YourClass'))), 0, 5);

        $expected = "$dir/$key-$hash.php";
        $this->assertEquals($expected, $args->getCacheFilePath());

        $expected = "$expected.meta";
        $this->assertEquals($expected, $args->getCacheMetaFilePath());
    }

    /**
     * @test
     * @param           mixed   $ext
     * @depends         optionalFileExtension
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function invalidClassItemFailure($class)
    {
        $spec = $this->getRequiredArguments();
        $spec['classes'] = array('MyClass', $class);
        
        $msg = "class entry in class list must be a non empty string";
        $this->setExpectedException('OutOfBoundsException', $msg);
        
        $args = $this->createArgs($spec);
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
