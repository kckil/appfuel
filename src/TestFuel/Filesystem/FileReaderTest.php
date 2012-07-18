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
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface,
    Appfuel\Filesystem\FileFinderInterface;

class FileReaderTest extends FrameworkTestCase 
{

    /**
     * @return  FileFinderInterface
     */
    public function createMockFinder()
    {
        return $this->getMock("Appfuel\\Filesystem\\FileFinderInterface");
    }

    /**
     * @param   array   $spec
     * @return  FileReader
     */
    public function createFileReader(FileFinderInterface $finder)
    {
        return new FileReader($finder);
    }

    /**
     * @test
     * @return  FileReader
     */
    public function createFileReaderMockFinder()
    {
        $finder = $this->createMockFinder();
        $reader = $this->createFileReader($finder);
    
        $readerInterface = "Appfuel\\Filesystem\\FileReaderInterface";
        $this->assertInstanceOf($readerInterface, $reader);
        $this->assertSame($finder, $reader->getFileFinder());

        return $reader;
    }
      
    /**
     * @test
     * @return  FileReader
     */
    public function createFileReaderUsedForFixtures()
    {
        $finder = new FileFinder($this->getFixturePath());
        $this->assertTrue($finder->isDir($finder->getPath()));

        return $this->createFileReader($finder);
    }

    /**
     * @test
     * @depends createFileReaderMockFinder
     * @return  FileReader
     */
    public function settingAndGettingTheFinder(FileReader $reader)
    {
        $oFinder = $reader->getFileFinder();
        $mFinder = $this->createMockFinder();
        $this->assertNotSame($oFinder, $mFinder);

        $this->assertSame($reader, $reader->setFileFinder($mFinder));
        $this->assertSame($mFinder, $reader->getFileFinder());

        $reader->setFileFinder($oFinder);

        return $reader;
    }

    /**
     * @test
     * @depends settingAndGettingTheFinder
     * @param   FileReader  $reader
     * @return  FileReader
     */
    public function failureToken(FileReader $reader)
    {
        $token = FileReaderInterface::READ_FAILURE;
        $this->assertEquals($token, '__AF_FILE_READER_FAILURE__');
        $this->assertTrue($reader->isFailureToken($token));
        $this->assertEquals($token, $reader->getFailureToken());
        $this->assertTrue($reader->isFailureToken($reader->getFailureToken()));
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function importScriptsPHPClass(FileReader $reader)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassA', $declared); 

        $file = 'reader/import/MyFixtureClassA.php';
        $result = $reader->importScript($file);
        $this->assertNotEquals($result, $reader->getFailureToken());
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassA', $declared); 

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function importScriptsOncePHPClass(FileReader $reader)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassB', $declared); 

        $file = 'reader/import/MyFixtureClassB.php';
        $result = $reader->importScript($file, true);
        $failureToken = $reader->getFailureToken();
        $this->assertNotEquals($result, $failureToken);
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassB', $declared); 

        $result = $reader->importScript($file, true);
        $this->assertNotEquals($result, $failureToken);
 
        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function importScriptNotFound(FileReader $reader)
    {
        $file = '__FileNotFoundNoPossibleWayThisExists__.php';
        $result = $reader->importScript($file);
        $this->assertEquals($result, $reader->getFailureToken());
        
        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function importScriptThatReturnsData(FileReader $reader)
    {
        $file = 'reader/return-array.php';
        $result = $reader->importScript($file);
        $expected = array('a', 'b', 'c');
        $this->assertEquals($expected, $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function includeScriptsPHPClass(FileReader $reader)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassC', $declared); 

        $file = 'reader/include/MyFixtureClassC.php';
        $result = $reader->includeScript($file);
        $this->assertNotEquals($result, $reader->getFailureToken());
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassC', $declared); 

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function includeScriptsOncePHPClass(FileReader $reader)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassD', $declared); 

        $file = 'reader/include/MyFixtureClassD.php';
        $result = $reader->includeScript($file, true);
        $failureToken = $reader->getFailureToken();
        $this->assertNotEquals($result, $failureToken);
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassD', $declared); 

        $result = $reader->includeScript($file, true);
        $this->assertNotEquals($result, $failureToken);
 
        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function readValidJsonFile(FileReader $reader)
    {
        $file = 'reader/json/basic.json';
        $result = $reader->readJson($file);
        $expected = array("a" => 1, "b" => 2, "c" => 3); 
        $this->assertEquals($expected, $result);

        $isAssoc = false;
        $expected = new stdClass();
        $expected->a = 1;
        $expected->b = 2;
        $expected->c = 3;
        $result = $reader->readJson($file, $isAssoc);
        $this->assertEquals($expected, $result);
        
        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function readdJsonNotFound(FileReader $reader)
    {
        $file = 'file-does-not-exist.json';
        $result = $reader->readJson($file);
        $this->assertEquals($reader->getFailureToken(), $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function readValidFile(FileReader $reader)
    {
        $file = 'reader/basic.txt';
        $result = $reader->read($file);
        $expected = "This is a basic file\n"; 
        $this->assertEquals($expected, $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function readdFileNotFound(FileReader $reader)
    {
        $file = 'file-does-not-exist.txt';
        $result = $reader->read($file);
        $this->assertEquals($reader->getFailureToken(), $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
    */
    public function readSerializedFile(FileReader $reader)
    {
        $file = 'reader/serialized.txt';
        $expected = array("a", "b", "c");
        
        $result = $reader->readSerialized($file);
        $this->assertEquals($expected, $result);
        
        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
     */
    public function readSerializeNotFound(FileReader $reader)
    {
        $file = 'file-does-not-exist.txt';
        $result = $reader->readSerialized($file);
        $this->assertEquals($reader->getFailureToken(), $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
     */
    public function readLinesIntoArray(FileReader $reader)
    {
        $file = 'reader/multiline.txt';
        $result = $reader->readLinesIntoArray($file);
        $expected = array(
            "this is line one.\n",
            "this is line two.\n",
            "this is line three.\n"
        );
        $this->assertEquals($expected, $result);

        return $reader;
    }

    /**
     * @test
     * @depends createFileReaderUsedForFixtures
     * @return  FileReader
     */
    public function readLinesIntoArrayNotFound(FileReader $reader)
    {
        $file = 'file-does-not-exist.txt';
        $result = $reader->readLinesIntoArray($file);
        $this->assertEquals($reader->getFailureToken(), $result);

        return $reader;
    }
}
