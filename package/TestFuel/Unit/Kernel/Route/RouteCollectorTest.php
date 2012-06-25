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
	public function createRouteCollector($filename = null, $reader = null)
	{
		return new RouteCollector($filename, $reader);
	}

	/**
	 * @test
	 * @return	RouteCollector
	 */
	public function collectorInterface()
	{
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
    public function defaultSettings(RouteCollector $collector)
    {
        $this->assertEquals('route-details.php', $collector->getFilename());

        $reader = $collector->getFileReader();
        $class  = 'Appfuel\Filesystem\FileReader';
        $this->assertInstanceOf($class, $reader);

        $finder = $reader->getFileFinder();
        $class = 'Appfuel\Filesystem\FileFinder';
        $this->assertInstanceOf($class, $finder);
           
        $this->assertEquals(AF_BASE_PATH, $finder->getBasePath()); 
    }
}
