<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Filesystem;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Filesystem\FileManager;

class FileManagerTest extends BaseTestCase
{
	/**
	 * @return	FileManager
	 */
	public function createFileManager()
	{
		return new FileManager();
	}

	/**
	 * @test
	 * @return  FileManager
	 */
	public function defaultFileManager()
	{
        $manager = $this->createFileManager();
        $interface = 'Appfuel\Filesystem\FileManagerInterface';
        $this->assertInstanceOf($interface, $manager);

        return $manager;
	}

    /**
     * @test
     * @depends defaultFileManager
     * @param   FileManager $manager
     * @return  FileManager
     */
    public function createDefaultFileFinder(FileManager $manager)
    {
        $finder = $manager->createFileFinder();
        $class  = 'Appfuel\Filesystem\FileFinder';
        $this->assertInstanceOf($class, $finder);

        $this->assertTrue(defined('AF_BASE_PATH'));
        $this->assertTrue($finder->isBasePath());
        $this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
        $this->assertEquals('', $finder->getRootPath());
        $this->assertEquals(AF_BASE_PATH, $finder->getPath());
       
        return $manager; 
    }

    /**
     * @test
     * @depends createDefaultFileFinder 
     * @param   FileManager $manager
     * @return  FileManager
     */
    public function createRootPathFileFinder(FileManager $manager)
    {
        $path = 'my/relative/root';
        $finder = $manager->createFileFinder($path);
        $class  = 'Appfuel\Filesystem\FileFinder';
        $this->assertInstanceOf($class, $finder);

        $this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
        $this->assertEquals($path, $finder->getRootPath());
        $this->assertTrue($finder->isBasePath());

        $root = AF_BASE_PATH . DIRECTORY_SEPARATOR . $path;
        $this->assertEquals($root, $finder->getPath());
 
        return $manager; 
    }

    /**
     * @test
     * @depends createDefaultFileFinder 
     * @param   FileManager $manager
     * @return  FileManager
     */
    public function createNoBasePathFileFinder(FileManager $manager)
    {
        $isBase = false;
        $finder = $manager->createFileFinder(null, $isBase);
        $class  = 'Appfuel\Filesystem\FileFinder';
        $this->assertInstanceOf($class, $finder);

        $this->assertEquals('', $finder->getBasePath());
        $this->assertEquals('', $finder->getRootPath());
        $this->assertFalse($finder->isBasePath());
        $this->assertEquals('', $finder->getPath());
        return $manager; 
    }

    /**
     * @test
     * @depends createNoBasePathFileFinder
     * @param   FileManager $manager
     * @return  FileManager
     */
    public function createRootWithNoBasePathFileFinder(FileManager $manager)
    {
        $isBase = false;
        $path   = '/some/path';
        $finder = $manager->createFileFinder($path, $isBase);
        $class  = 'Appfuel\Filesystem\FileFinder';
        $this->assertInstanceOf($class, $finder);

        $this->assertFalse($finder->isBasePath());
        $this->assertEquals('', $finder->getBasePath());
        $this->assertEquals($path, $finder->getRootPath());
        $this->assertEquals($path, $finder->getPath());

        return $manager; 
    }

}
