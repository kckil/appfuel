<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Kernel\PathCollection;

class PathCollectionTest extends FrameworkTestCase 
{

    /**
     * @return  string
     */
    public function getRootPath()
    {
        return "/my/app";
    }

    /**
     * @return  array
     */
    public function getCustomPaths()
    {
        $vendor = 'vendor/appfuel/appfuel';
        return array(
            'appfuel'     => $vendor,
            'appfuel-src' => "$vendor/src",
            'appfuel-bin' => "$vendor/bin",
            'appfuel-makefile' => "$vendor/Makefile",
        );
    }

    /**
     * @return  array
     */
    public function provideDefaultPaths()
    {
        $root = $this->getRootPath();
        return array(
            array('www',    'www',  "$root/www"),
            array('bin',    'bin',  "$root/bin"),
            array('test',   'test', "$root/test"),
            array('src',    'src',  "$root/src"),
            array('app',    'app',  "$root/app"),
            array('cache',  'app/cache',  "$root/app/cache"),
            array('config',  'app/config',  "$root/app/config"),
            array('vendor',  'vendor',  "$root/vendor"),
        );
    }

    /**
     * @param   string  $root   app root dir
     * @param   string  $paths  list of paths under the root
     * @return  PathCollection
     */
    public function createPathCollection($root, array $paths = array())
    {
        return new PathCollection($root, $paths);
    }
    
    /**
     * @test
     * @return  PathCollection
     */
    public function creatingPathCollectionRootWithNoPaths()
    {
        $root = $this->getRootPath(); 
        $paths = $this->createPathCollection($root);
        $interface = 'Appfuel\\Kernel\\PathCollectionInterface';
        $this->assertInstanceOf($interface, $paths);

        $this->assertEquals($root, $paths->getRootPath());
        
        return $paths;
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingPathCollectionRootInvalidTypeFailure($root)
    {
        $msg = 'root path must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $paths = $this->createPathCollection($root);
    }

    /**
     * @test
     */
    public function creatingPathCollectionRootNotAbsoluteFailure()
    {
        $msg = 'root path must be an absolute path';
        $this->setExpectedException('DomainException', $msg);
        
        $paths = $this->createPathCollection('my/path');
    }

    /**
     * @test
     * @dataProvider    provideDefaultPaths
     * @depends         creatingPathCollectionRootWithNoPaths
     * @return          PathCollection
     */
    public function defaultPathCollection($name, $relative, $absolute)
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);
        $this->assertTrue($paths->isPath($name));
        $this->assertEquals($relative, $paths->getRelativePath($name));
        $this->assertEquals($absolute, $paths->getPath($name));

        return $paths;
    }

    /**
     * @test
     * @return  PathCollection
     */
    public function creatingPathCollectionWithCustomPaths()
    {
        $root = $this->getRootPath();
        $list = $this->getCustomPaths();
        $paths = $this->createPathCollection($root, $list);

        foreach ($list as $name => $path) {
            $this->assertTrue($paths->isPath($name));
            $this->assertEquals($path, $paths->getRelativePath($name));
            
            $absolute = "$root/$path";
            $this->assertEquals($absolute, $paths->getPath($name));
        }

        return $paths;
    }

    /**
     * @test
     * @return  PathCollection
     */
    public function addingPaths()
    {
        $root = $this->getRootPath();
        $list = $this->getCustomPaths();
        $paths = $this->createPathCollection($root);

        foreach ($list as $name => $path) {
            $this->assertFalse($paths->isPath($name));
            $this->assertSame($paths, $paths->addPath($name, $path));
            $this->assertTrue($paths->isPath($name));
            $this->assertEquals($path, $paths->getRelativePath($name));

            $absolute = "$root/$path";
            $this->assertEquals($absolute, $paths->getPath($name));
        }
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function addingPathNameInvalidTypeFailure($badName)
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);

        $msg = 'path name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $paths->addPath($badName, 'some/path');
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function addingPathInvalidPathTypeFailure($badPath)
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);

        $msg = 'path for -(my-bad-path) must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $paths->addPath('my-bad-path', $badPath);
    }

    /**
     * @test
     */
    public function addingPathInvalidAbsolutePathFailure()
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);

        $msg  = 'path for -(my-bad-path) must not be  absolute, since all ';
        $msg .= 'paths are to be under the root path -(/my/app)';
        $this->setExpectedException('DomainException', $msg);

        $paths->addPath('my-bad-path', '/mypath');
    }
}
