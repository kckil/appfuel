<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Kernel\Route;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Route\RoutePatternSpec;

class RoutePatternTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	MvcRoute
	 */
	public function createRoutePattern(array $data)
	{
		return new RoutePatternSpec($data);
	}

	/**
	 * @return string
	 */
	public function getRoutePatternSpecInterface()
	{
		return 'Appfuel\Kernel\Route\RoutePatternSpecInterface';
	}

	/**
	 * @test
	 * @return null
	 */
	public function createPatternWithIndexArray()
	{
		$data = array(
            'route-key'=> 'my-route',
            'pattern'  => '/^users$/', 
            'pattern-group' => 'my-group'
        );
		$pattern = $this->createRoutePattern($data);
		$interface = $this->getRoutePatternSpecInterface();
		$this->assertInstanceOf($interface, $pattern);
		$this->assertEquals($data['pattern'], $pattern->getPattern());
		$this->assertEquals($data['route-key'], $pattern->getRouteKey());
		$this->assertEquals($data['pattern-group'], $pattern->getGroup());

		$data = array(
            'pattern'   => '/^users$/', 
            'route-key' => 'my-route'
        );
		$pattern = $this->createRoutePattern($data);
		$this->assertInstanceOf($interface, $pattern);
		$this->assertEquals($data['pattern'],   $pattern->getPattern());
		$this->assertEquals($data['route-key'],   $pattern->getRouteKey());
		$this->assertEquals('no-group', $pattern->getGroup());
	}

	/**
     * @test
	 * @return	null
	 */
	public function createPatternEmptyArray()
	{
		$msg = '-(route-key) route key is expected but not given';
		$this->setExpectedException('OutOfBoundsException', $msg);
		$pattern = $this->createRoutePattern(array());
	}

	/**
     * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function createPatternInvalidRouteKey($key)
	{
		$msg = 'route key must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		
		$data = array('pattern' => '/^somepatter$/', 'route-key' => $key);
		$pattern = $this->createRoutePattern($data);
	}
}
