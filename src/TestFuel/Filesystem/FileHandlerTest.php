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
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\FileHandlerInterface,
    Appfuel\Filesystem\FileFinderInterface;

class FileHandlerTest extends FrameworkTestCase 
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
     * @return  FileHandler
     */
    public function createFileHandler(FileFinderInterface $finder)
    {
        return new FileHandler($finder);
    }

    /**
     * @test
     * @return  FileHandler
     */
    public function createFileHandlerMockFinder()
    {
        $finder = $this->createMockFinder();
        $handler = $this->createFileHandler($finder);
    
        $handlerInterface = "Appfuel\\Filesystem\\FileHandlerInterface";
        $this->assertInstanceOf($handlerInterface, $handler);
        $this->assertSame($finder, $handler->getFileFinder());

        return $handler;
    }
      
    /**
     * @test
     * @return  FileHandler
     */
    public function createFileHandlerUsedForFixtures()
    {
        $finder = new FileFinder($this->getFixturePath());
        $this->assertTrue($finder->isDir($finder->getPath()));

        return $this->createFileHandler($finder);
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @param   FileHandler
     * @return  FileHandler
     */
    public function settingAndGettingTheFinder(FileHandler $handler)
    {
        $oFinder = $handler->getFileFinder();
        $mFinder = $this->createMockFinder();
        $this->assertNotSame($oFinder, $mFinder);

        $this->assertSame($handler, $handler->setFileFinder($mFinder));
        $this->assertSame($mFinder, $handler->getFileFinder());

        $handler->setFileFinder($oFinder);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @param   FileHandler  $handler
     * @return  FileHandler
     */
    public function throwingExceptionFlag(FileHandler $handler)
    {
        $this->assertFalse($handler->isThrowOnFailure());
        $this->assertSame($handler, $handler->throwExceptionOnFailure());
        $this->assertTrue($handler->isThrowOnFailure());
        $this->assertSame($handler, $handler->disableExceptionsOnFailure());

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @param   FileHandler  $handler
     * @return  FileHandler
     */
    public function settingFailureMsg(FileHandler $handler)
    {
        $this->assertNull($handler->getFailureMsg());
        
        $msg = "this is my message";
        $this->assertSame($handler, $handler->setFailureMsg($msg));
        $this->assertEquals($msg, $handler->getFailureMsg());

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @dataProvider    provideInvalidStrings
     */
    public function settingFailureMsgFailure($badMsg)
    {
        $msg = 'failure msg must be a string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $handler = $this->createFileHandlerMockFinder();
        $handler->setFailureMsg($badMsg);
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @param   FileHandler  $handler
     * @return  FileHandler
     */
    public function settingFailureCode(FileHandler $handler)
    {
        $this->assertEquals(500, $handler->getFailureCode());
        
        $code = 404;
        $this->assertSame($handler, $handler->setFailureCode($code));
        $this->assertEquals($code, $handler->getFailureCode());

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerMockFinder
     * @dataProvider    provideInvalidScalars
     */
    public function settingFailureCodeFailure($badCode)
    {
        $msg = 'failure code must be a scalar';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $handler = $this->createFileHandlerMockFinder();
        $handler->setFailureCode($badCode);
    }

    /**
     * @test
     * @depends settingAndGettingTheFinder
     * @param   FileHandler  $handler
     * @return  FileHandler
     */
    public function readFailureToken(FileHandler $handler)
    {
        $token = FileHandlerInterface::READ_FAILURE;
        $this->assertEquals($token, '__AF_FILE_HANDLER_READ_FAILURE__');
        $this->assertTrue($handler->isReadFailureToken($token));
        $this->assertEquals($token, $handler->getReadFailureToken());
        $this->assertTrue(
            $handler->isReadFailureToken(
                $handler->getReadFailureToken()
            )
        );

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function importScriptsPHPClass(FileHandler $handler)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassA', $declared); 

        $file = 'reader/import/MyFixtureClassA.php';
        $result = $handler->importScript($file);
        $this->assertNotEquals($result, $handler->getReadFailureToken());
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassA', $declared); 

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function importScriptsOncePHPClass(FileHandler $handler)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassB', $declared); 

        $file = 'reader/import/MyFixtureClassB.php';
        $result = $handler->importScript($file, true);
        $failureToken = $handler->getReadFailureToken();
        $this->assertNotEquals($result, $failureToken);
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassB', $declared); 

        $result = $handler->importScript($file, true);
        $this->assertNotEquals($result, $failureToken);
 
        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
     */
    public function importScriptNotFound(FileHandler $handler)
    {
        $file = '__FileNotFoundNoPossibleWayThisExists__.php';
        $result = $handler->importScript($file);
        $this->assertEquals($result, $handler->getReadFailureToken());
        
        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function importScriptThatReturnsData(FileHandler $handler)
    {
        $file = 'reader/return-array.php';
        $result = $handler->importScript($file);
        $expected = array('a', 'b', 'c');
        $this->assertEquals($expected, $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function includeScriptsPHPClass(FileHandler $handler)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassC', $declared); 

        $file = 'reader/include/MyFixtureClassC.php';
        $result = $handler->includeScript($file);
        $this->assertNotEquals($result, $handler->getReadFailureToken());
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassC', $declared); 

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function includeScriptsOncePHPClass(FileHandler $handler)
    {
        $declared = get_declared_classes();
        $this->assertNotContains('MyFixtureClassD', $declared); 

        $file = 'reader/include/MyFixtureClassD.php';
        $result = $handler->includeScript($file, true);
        $failureToken = $handler->getReadFailureToken();
        $this->assertNotEquals($result, $failureToken);
        

        $declared = get_declared_classes();
        $this->assertContains('MyFixtureClassD', $declared); 

        $result = $handler->includeScript($file, true);
        $this->assertNotEquals($result, $failureToken);
 
        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function readValidJsonFile(FileHandler $handler)
    {
        $file = 'reader/json/basic.json';
        $result = $handler->readJson($file);
        $expected = array("a" => 1, "b" => 2, "c" => 3); 
        $this->assertEquals($expected, $result);

        $isAssoc = false;
        $expected = new stdClass();
        $expected->a = 1;
        $expected->b = 2;
        $expected->c = 3;
        $result = $handler->readJson($file, $isAssoc);
        $this->assertEquals($expected, $result);
        
        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function readdJsonNotFound(FileHandler $handler)
    {
        $file = 'file-does-not-exist.json';
        $result = $handler->readJson($file);
        $this->assertEquals($handler->getReadFailureToken(), $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function readValidFile(FileHandler $handler)
    {
        $file = 'reader/basic.txt';
        $result = $handler->read($file);
        $expected = "This is a basic file\n"; 
        $this->assertEquals($expected, $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function readdFileNotFound(FileHandler $handler)
    {
        $file = 'file-does-not-exist.txt';
        $result = $handler->read($file);
        $this->assertEquals($handler->getReadFailureToken(), $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
    */
    public function readSerializedFile(FileHandler $handler)
    {
        $file = 'reader/serialized.txt';
        $expected = array("a", "b", "c");
        
        $result = $handler->readSerialized($file);
        $this->assertEquals($expected, $result);
        
        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
     */
    public function readSerializeNotFound(FileHandler $handler)
    {
        $file = 'file-does-not-exist.txt';
        $result = $handler->readSerialized($file);
        $this->assertEquals($handler->getReadFailureToken(), $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
     */
    public function readLinesIntoArray(FileHandler $handler)
    {
        $file = 'reader/multiline.txt';
        $result = $handler->readLinesIntoArray($file);
        $expected = array(
            "this is line one.\n",
            "this is line two.\n",
            "this is line three.\n"
        );
        $this->assertEquals($expected, $result);

        return $handler;
    }

    /**
     * @test
     * @depends createFileHandlerUsedForFixtures
     * @return  FileHandler
     */
    public function readLinesIntoArrayNotFound(FileHandler $handler)
    {
        $file = 'file-does-not-exist.txt';
        $result = $handler->readLinesIntoArray($file);
        $this->assertEquals($handler->getReadFailureToken(), $result);

        return $handler;
    }
}
