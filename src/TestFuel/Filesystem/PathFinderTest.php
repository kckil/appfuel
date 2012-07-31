<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Filesystem;

use StdClass,
    SplFileInfo,
    Testfuel\FrameworkTestCase,
    Appfuel\Filesystem\PathFinder,
    Appfuel\Filesystem\PathCollection;

class PathFinderTest extends FrameworkTestCase 
{

    /**
     * @param   string  $root
     * @param   array   $paths
     * @return  PathCollection
     */
    public function createPathCollection($root, $paths = null)
    {
        return new PathCollection($root, $paths);
    }

    /**
     * @param   array   $spec
     * @return  FileFinder
     */
    public function createPathFinder($paths)
    {
        return new PathFinder($paths);
    }
    
    /**
     * @return  array
     */
    public function provideInvalidPaths()
    {
        return array(
            array(true),
            array(false),
            array(1234),
            array(1.23),
            array(array(1,2,3)),
            array(new stdClass),
        );
    }

    /**
     * @test
     * @return  FileFinderInterface
     */
    public function creatingPathFinder()
    {
        $root = $this->getFixturePath();
        $list = array(
            'file-a' => 'file-a.txt',
            'file-b' => 'file-b.txt'
        );
        $paths = $this->createPathCollection($root, $list);
        $finder = $this->createPathFinder($paths);

        $interface = 'Appfuel\\Filesystem\\PathFinderInterface';
        $this->assertInstanceOf($interface, $finder);
        $this->assertSame($paths, $finder->getPathCollection()); 
        $this->assertEquals($paths->getRoot(), $finder->getRoot());

        $expected = "$root/file-a.txt";
        $this->assertEquals($expected, $finder->getPath('file-a'));

        $expected = "$root/file-b.txt";
        $this->assertEquals($expected, $finder->getPath('file-b'));

        return $finder;    
    }

    /**
     * @test
     * @depends creatingPathFinder
     * @return  PathFinder
     */
    public function rootPath(PathFinder $finder)
    {
        /* will always be true */
        $this->assertTrue($finder->isRootAbsolute());

        $backup = $finder->getRoot();

        $paths = $finder->getPathCollection();
        $root = '/some/root/path';
        $this->assertSame($finder, $finder->setRoot($root));
        $this->assertEquals($root, $finder->getRoot());
        $this->assertEquals($root, $paths->getRoot());

        $finder->setRoot($backup);

        return $finder;
    }

    /**
     * @test
     * @depends rootPath
     * @return  null
     */
    public function rootPathCanNotBeCleared(PathFinder $finder)
    {
        $msg = 'path finders can not have their root path cleared';
        $this->setExpectedException('LogicException', $msg);

        $finder->clearRoot();
    }

    /**
     * @test
     * @depends creatingPathFinder
     * @return  PathFinder
     */
    public function pathCollections(PathFinder $finder)
    {
        $backup = $finder->getPathCollection();
        $paths = $this->getMock('Appfuel\\Filesystem\\PathCollectionInterface');
        $this->assertSame($finder, $finder->setPathCollection($paths));
        $this->assertSame($paths, $finder->getPathCollection());

        $finder->setPathCollection($backup);

        return $finder;
    }

}
