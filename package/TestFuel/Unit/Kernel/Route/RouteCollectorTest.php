<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Kernel\Route;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Route\RouteCollector;

class RouteCollectorTest extends BaseTestCase
{
	/**
	 * @return	RouteCollector
	 */
	public function createRouteCollector($filename = null)
	{
		return new RouteCollector($filename);
	}

	/**
	 * @test
	 * @return	RouteCollector
	 */
	public function collectorInterface()
	{
        /*
         * code path is derived from this constant which must be set 
         */
        $this->assertTrue(defined('AF_CODE_PATH'));
        
        $collector = $this->createRouteCollector();
        $interface = 'Appfuel\Kernel\Route\RouteCollectorInterface';
        $this->assertInstanceOf($interface, $collector);

        return $collector;
	}

    /**
     * @test
     * @depends collectorInterface
     * @param   RouteCollector  $collector
     * @return  RouteCollector
     */
    public function filename(RouteCollector $collector)
    {
        $this->assertEquals('route-details.php', $collector->getFilename());

        $newName = "my-routes.php";
        $this->assertSame($collector, $collector->setFilename($newName));
        $this->assertEquals($newName, $collector->getFilename());

        /* restore default setting */
        $collector->setFilename('route-details.php');
        return $collector;
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function filenameFailure($name)
    {
        $msg = 'filename must be a non empty string';
        $this->setExpectedException('DomainException', $msg);
        $collector = $this->createRouteCollector();
        $collector->setFilename($name);
    }

    /**
     * @test
     * @return  RouteCollector
     */
    public function constructorFilename()
    {
        $file = 'my-routes-file.php';
        $collector = $this->createRouteCollector($file);
        $this->assertEquals($file, $collector->getFilename());

        return $collector;
    }

    /**
     * @test
     * @depends collectorInterface
     * @param   RouteCollector  $collector
     * @return  RouteCollector
     */
    public function collect(RouteCollector $collector)
    {
        $path = AF_CODE_PATH . '/Testfuel/Functional/Action';
        $result = $collector->collect(array($path));
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('welcome', $result);

        $welcome = $result['welcome'];
        $this->assertInternalType('array', $welcome);

        $this->assertArrayHasKey('my-action', $result);

        $myAction = $result['my-action'];
        $this->assertInternalType('array', $myAction);

    }
}
