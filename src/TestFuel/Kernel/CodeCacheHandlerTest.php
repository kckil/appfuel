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
    Appfuel\kernel\CodeCacheHandler,
    Appfuel\Filesystem\FileHandlerInterface;

require_once __DIR__ . '/Fixtures/ClassesWithParents/GInterface.php';
require_once __DIR__ . '/Fixtures/ClassesWithParents/CInterface.php';
require_once __DIR__ . '/Fixtures/ClassesWithParents/B.php';
require_once __DIR__ . '/Fixtures/ClassesWithParents/A.php';


class CodeCacheHandlerTest extends FrameworkTestCase 
{
    /**
     * @return null
     */
    public function setUp()
    {
        CodeCacheHandler::clearLoaded();
    }

    /**
     * @return  string
     */
    public function getFileHandlerInterface()
    {
        return 'Appfuel\\Filesystem\\FileHandlerInterface';
    }

    /**
     * @return  array
     */
    public function provideDifferentClassOrders()
    {
        return array(
            array(array(
                'ClassesWithParents\\A',
                'ClassesWithParents\\CInterface',
                'ClassesWithParents\\GInterface',
                'ClassesWithParents\\B',
            )),
            array(array(
                'ClassesWithParents\\B',
                'ClassesWithParents\\A',
                'ClassesWithParents\\CInterface',
            )),
            array(array(
                'ClassesWithParents\\CInterface',
                'ClassesWithParents\\B',
                'ClassesWithParents\\A',
            )),
            array(array(
                'ClassesWithParents\\A',
            )),
        );
    }

    /**
     * @return  array
     */
    public function provideDifferentClassOrdersForTraits()
    {
        return array(
            array(array(
                'ClassesWithParents\\E',
                'ClassesWithParents\\ATrait',
            )),
            array(array(
                'ClassesWithParents\\E',
            )),
        );
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
     * @test
     */
    public function creatingCacheArgs()
    {
        $spec = array(
            'cache-dir' => 'my/dir',
            'cache-key' => 'my-key',
            'classes'   => array('MyClass', 'YourClass')
        );

        $args = CodeCacheHandler::createArgs($spec);
        $this->assertInstanceOf('Appfuel\Kernel\CodeCacheArgs', $args);
    }

    /**
     * @test
     * @depends creatingCacheArgs
     */
    public function trackingTheLoadedProperty()
    {
        $this->assertEquals(array(), CodeCacheHandler::getLoaded());

        $key1 = "key-1";
        $expected = array($key1 => true);
        $this->assertFalse(CodeCacheHandler::isLoaded($key1));
        $this->assertTrue(CodeCacheHandler::markAsLoaded($key1));
        $this->assertTrue(CodeCacheHandler::isLoaded($key1));
        $this->assertEquals($expected, CodeCacheHandler::getLoaded());

        $key2 = "key-2";
        $expected[$key2] = true;
        $this->assertFalse(CodeCacheHandler::isLoaded($key2));
        $this->assertTrue(CodeCacheHandler::markAsLoaded($key2));
        $this->assertTrue(CodeCacheHandler::isLoaded($key2));
        $this->assertEquals($expected, CodeCacheHandler::getLoaded());
    }

    /**
     * @test
     */
    public function fixingNamespaceDeclarations()
    {
        $source  = "<?php ";
        $source .= "namespace Foo;class Foo{}";
        $source .= "namespace Bar;class Bar{}";
        $source .= "namespace Foo\\Bar;class Foo{}";
        $source .= "namespace Foo\\Bar\\Bar{class Foo{}}";
        $source .= "namespace{class Foo{}}";

        $expected  = "<?php ";
        $expected .= "namespace Foo\n{class Foo{}}\n";
        $expected .= "namespace Bar\n{class Bar{}}\n";
        $expected .= "namespace Foo\\Bar\n{class Foo{}}\n";
        $expected .= "namespace Foo\\Bar\\Bar{class Foo{}}";
        $expected .= "namespace{class Foo{}}";


        $result = CodeCacheHandler::fixNamespaceDeclarations($source);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function strippingComments()
    {
        $source = "<?php /* comment1 */\n //comment 2 \n\$a = 'b';";
        $expected = "<?php\n \$a = 'b';";
        $result = CodeCacheHandler::stripComments($source);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider    provideDifferentClassOrders
     */
    public function reorderingOfClasses(array $classes)
    {
        $expected = array(
            'ClassesWithParents\\GInterface',
            'ClassesWithParents\\CInterface',
            'ClassesWithParents\\B',
            'ClassesWithParents\\A',
        );

        $list = CodeCacheHandler::getOrderedClasses($classes);

        $result = array_map(function($class) {
            return $class->getName();
        }, $list);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider    provideDifferentClassOrdersForTraits
     */
    public function reorderingClassesWithTraits(array $classes)
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP > 5.4.0.');
            return;
        }

        require_once __DIR__.'/Fixtures/ClassesWithParents/ATrait.php';
        require_once __DIR__.'/Fixtures/ClassesWithParents/BTrait.php';
        require_once __DIR__.'/Fixtures/ClassesWithParents/CTrait.php';
        require_once __DIR__.'/Fixtures/ClassesWithParents/D.php';
        require_once __DIR__.'/Fixtures/ClassesWithParents/E.php';

        $expected = array(
            'ClassesWithParents\\GInterface',
            'ClassesWithParents\\CInterface',
            'ClassesWithParents\\CTrait',
            'ClassesWithParents\\ATrait',
            'ClassesWithParents\\BTrait',
            'ClassesWithParents\\B',
            'ClassesWithParents\\A',
            'ClassesWithParents\\D',
            'ClassesWithParents\\E',
        );

        $list = CodeCacheHandler::getOrderedClasses($classes);
        $result = array_map(function($class) {                                   
            return $class->getName();                                            
        }, $list);  

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider provideDifferentClassOrders
     */
    public function loadingTheCache(array $classes)
    {
        CodeCacheHandler::clearLoaded();

        $cacheDir = __DIR__ . '/Fixtures/cache-dir';
        system("rm -rf $cacheDir");
        
        $spec = array(
            'cache-dir'   => $cacheDir,
            'cache-key'   => 'test-cache',
            'auto-reload' => true,
            'classes'     => $classes
        );

        $args = CodeCacheHandler::createArgs($spec);

        $result = CodeCacheHandler::load($args);
        $cacheFile = "$cacheDir/test-cache.php";
        $metaFile = "$cacheDir/test-cache.php.meta";
        $this->assertTrue(file_exists($cacheFile));
        $this->assertTrue(file_exists($metaFile));
    }
}
