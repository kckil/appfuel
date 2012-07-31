<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Filesystem\PathCollection;

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
        $interface = 'Appfuel\\Filesystem\\PathCollectionInterface';
        $this->assertInstanceOf($interface, $paths);

        $this->assertEquals($root, $paths->getRoot());
        
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
     * @depends         creatingPathCollectionRootWithNoPaths
     * @return          PathCollection
     */
    public function defaultPathCollection()
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);
        $this->assertEquals(array(), $paths->getMap());

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
            $this->assertTrue($paths->exists($name));
            $this->assertEquals($path, $paths->getRelative($name));
            
            $absolute = "$root/$path";
            $this->assertEquals($absolute, $paths->get($name));
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
            $this->assertFalse($paths->exists($name));
            $this->assertSame($paths, $paths->add($name, $path));
            $this->assertTrue($paths->exists($name));
            $this->assertEquals($path, $paths->getRelative($name));

            $absolute = "$root/$path";
            $this->assertEquals($absolute, $paths->get($name));
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

        $paths->add($badName, 'some/path');
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

        $paths->add('my-bad-path', $badPath);
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

        $paths->add('my-bad-path', '/mypath');
    }

    /**
     * @test
     */
    public function loadingPathCollection()
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);
        $this->assertEquals(array(), $paths->getMap());

        $newPaths = array(
            'my-dir'  => 'path/to/some/dir',
            'my-file' => 'path/to/some/file.txt',
            'my-prg'  => 'path/to/some/executable'
        );

        $this->assertSame($paths, $paths->load($newPaths));
        $this->assertEquals($newPaths, $paths->getMap());
        
       
        $morePaths = array(
            'path-a' => 'path/to/path-a',
            'path-b' => 'path/to/path-b'
        );
        $this->assertSame($paths, $paths->load($morePaths));
       
        $expected = array_merge($newPaths, $morePaths);
        $this->assertEquals($expected, $paths->getMap()); 
        
        return $paths;
    }

    /**
     * @test
     * @depends loadingPathCollection
     */
    public function clearPaths(PathCollection $paths)
    {
        $this->assertNotEmpty($paths->getMap());
        $this->assertSame($paths, $paths->clear());
        $this->assertEmpty($paths->getMap());
    }

    /**
     * @test
     */
    public function settingPathCollection()
    {
        $root = $this->getRootPath();
        $paths = $this->createPathCollection($root);
        $this->assertEquals(array(), $paths->getMap());

        $newPaths = array(
            'my-dir'  => 'path/to/some/dir',
            'my-file' => 'path/to/some/file.txt',
            'my-prg'  => 'path/to/some/executable'
        );

        $this->assertSame($paths, $paths->set($newPaths));
        $this->assertEquals($newPaths, $paths->getMap());
        
       
        $morePaths = array(
            'path-a' => 'path/to/path-a',
            'path-b' => 'path/to/path-b'
        );
        $this->assertSame($paths, $paths->set($morePaths));
       
        $this->assertEquals($morePaths, $paths->getMap()); 
        
        return $paths;
    }
}
