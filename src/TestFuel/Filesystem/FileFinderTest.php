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
    Appfuel\Filesystem\FileFinder;

class FileFinderTest extends FrameworkTestCase 
{

    /**
     * @param   array   $spec
     * @return  FileFinder
     */
    public function createFileFinder($path = null)
    {
        return new FileFinder($path);
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
     * Used to test getPathBase
     * param 1) path
     * param 2) optional suffix
     * param 3) expected result
     *
     * @return  array
     */
    public function providePathBaseData()
    {
        return array(
            array('', null, ''),
            array('.', null, '.'),
            array('/etc/', null, 'etc'),
            array('/etc/passwd', null, 'passwd'),
            array('/ets/sudoers.d', '.d', 'sudoers')
        );
    }

    /**
     * @test
     * @return  FileFinder
     */
    public function creatingFinderWithNoRoot()
    {
        $finder = $this->createFileFinder();

        $interface = 'Appfuel\\Filesystem\\FileFinderInterface';
        $this->assertInstanceOf($interface, $finder);

        $this->assertFalse($finder->isRoot());
        $this->assertNull($finder->getRoot());
        $this->assertFalse($finder->isRootAbsolute());

        return $finder;
    }

    /**
     * @test
     * @return  FileFinderInterface
     */
    public function creatingFinderWithAbsoluteRootPath()
    {
        $root = '/path/to/my/app';
        $finder = $this->createFileFinder($root);
        
        $this->assertTrue($finder->isRoot());
        $this->assertEquals($root, $finder->getRoot());
        $this->assertTrue($finder->isRootAbsolute());
    
        return $finder;    
    }

    /**
     * @test
     * @return  FileFinderInterface
     */
    public function creatingFinderWithRelativeRootPath()
    {
        $root = 'your/app/path/to/my/app';
        $finder = $this->createFileFinder($root);
        
        $this->assertTrue($finder->isRoot());
        $this->assertEquals($root, $finder->getRoot());
        $this->assertFalse($finder->isRootAbsolute());
    
        return $finder;  
    }

    /**
     * @test
     * @depends  creatingFinderWithNoRoot
     * @return   FileFinder
     */
    public function finderRoot(FileFinder $finder)
    {
        $root = '/my/root/path';
        $this->assertSame($finder, $finder->setRoot($root));
        $this->assertTrue($finder->isRoot());
        $this->assertTrue($finder->isRootAbsolute());
        $this->assertEquals($root, $finder->getRoot());

        $this->assertSame($finder, $finder->clearRoot());
        $this->assertFalse($finder->isRoot());
        $this->assertFalse($finder->isRootAbsolute());
        $this->assertNull($finder->getRoot());

        $root = 'my/root/path';
        $this->assertSame($finder, $finder->setRoot($root));
        $this->assertTrue($finder->isRoot());
        $this->assertFalse($finder->isRootAbsolute());
        $this->assertEquals($root, $finder->getRoot());

        $finder->clearRoot();

        return $finder;
    }

    /**
     * @test
     * @depends  finderRoot
     * @return   FileFinder
     */
    public function checkIfAnyPathIsAbsolute(FileFinder $finder)
    {
        $this->assertTrue($finder->isAbsolute('/'));

        $path = '/some/path/to/some/file';
        $this->assertTrue($finder->isAbsolute($path));

        $path = 'some/file';
        $this->assertFalse($finder->isAbsolute($path));
       
        $this->assertFalse($finder->isAbsolute(''));
        $this->assertFalse($finder->isAbsolute(12345));
        $this->assertFalse($finder->isAbsolute(true));
        $this->assertFalse($finder->isAbsolute(false));
        $this->assertFalse($finder->isAbsolute(new stdClass));
         
       return $finder; 
    }

    /**
     * @test
     * @depends finderRoot
     * @return  FileFinder
     */
    public function convertPath(FileFinder $finder)
    {
        $path = 'some/path';

        /* does nothing here already a string */
        $this->assertEquals($path, $finder->convertPath($path));

        $pathObj  = new SplFileInfo($path);
        $this->assertEquals($path, $finder->convertPath($pathObj));
    }

    /**
     * @test
     * @depends         finderRoot
     * @dataProvider    provideInvalidPaths
     * @param   mixed   $badPath
     */
    public function convertPathFailure($badPath)
    {
        $finder = $this->createFileFinder();
        
        $msg = "path must be a string or an object that implements __toString";
        $this->setExpectedException('InvalidArgumentException', $msg);
     
       $finder->convertPath($badPath);
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     */
    public function convertPathNullFailure(FileFinder $finder)
    {
        $msg = "path must be a string or an object that implements __toString";
        $this->setExpectedException('InvalidArgumentException', $msg);
        $finder->convertPath(null);
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */ 
    public function getPathWithRootNoTrailingSlash(FileFinder $finder)
    {
        $root = '/my/root';
        $finder->setRoot($root);
        
        $path = 'my/path';
        $expected = $root . '/' . $path;
        $this->assertEquals($expected, $finder->getPath($path));

        $root = 'my/root';
        $finder->setRoot($root);

        $expected = $root . '/' . $path;
        $this->assertEquals($expected, $finder->getPath($path));
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */ 
    public function getPathWithRootTrailingSlash(FileFinder $finder)
    {
        /* forward slash trailing the root path will be trimmed */
        $root = '/my/root/';
        $finder->setRoot($root);
        $this->assertEquals('/my/root', $finder->getPath());
 
        $path = 'my/path';
        $expected = "/my/root/my/path";
        $this->assertEquals($expected, $finder->getPath($path));

        $root = 'my/root/';
        $finder->setRoot($root);

        $expected = "my/root/my/path";
        $this->assertEquals($expected, $finder->getPath($path));

        $this->assertEquals('my/root', $finder->getPath());
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */ 
    public function getPathNoRoot(FileFinder $finder)
    {
        $finder->clearRoot();
 
        $path = '/my/path';
        $this->assertEquals($path, $finder->getPath($path));

        $path = "my/path";
        $this->assertEquals($path, $finder->getPath($path));
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */    
    public function getPathNoRootEmptyString(FileFinder $finder)
    {
        $this->assertEquals('', $finder->getPath(''));
        return $finder;
    }

    /**
     * @test
     * @depends finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */    
    public function getPathNoRootUsingNull(FileFinder $finder)
    {
        $finder->clearRoot();

        $msg  = 'nothing useful can happen when no path is given and no ';
        $msg .= 'root path is set';
        $this->setExpectedException('DomainException', $msg);
        $finder->getPath();
    }

    /**
     * @test
     * @depends finderRoot
     * @param   string  $badPath
     * @dataProvider    provideInvalidPaths
     * @return  FileFinder
     */    
    public function getPathBadPathFailure($badPath)
    {
        $finder = $this->createFileFinder();

        $msg = "path must be a string or an object that implements __toString";
        $this->setExpectedException('InvalidArgumentException', $msg);
    
        $finder->getPath($badPath);
    }

    /**
     * @test
     * @depends             finderRoot
     * @dataProvider        providePathBaseData
     * @param   FileFinder  $finder
     * @return  FileFinder
     */
    public function getPathBase($path, $suffix = null, $expected)
    {
        $finder = $this->createFileFinder();
        $this->assertEquals($expected, $finder->getPathBase($path, $suffix));
    }

    /**
     * @test
     * @depends             finderRoot
     * @param   FileFinder  $finder
     * @return  FileFinder
     */
    public function getDirPath(FileFinder $finder)
    {
        $finder = $this->createFileFinder();
        
        $path = "/etc/passwd";
        $this->assertEquals('/etc', $finder->getDirPath($path));

        $path = "/etc/";
        $this->assertEquals('/', $finder->getDirPath($path));
       
        $path = ".";
        $this->assertEquals('.', $finder->getDirPath($path));
    }
}
