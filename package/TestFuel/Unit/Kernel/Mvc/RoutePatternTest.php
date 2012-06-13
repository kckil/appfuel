<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RoutePattern;

class RoutePatternTest extends BaseTestCase
{
	/**
	 * @param	array	$data
	 * @return	MvcRoute
	 */
	public function createRoutePattern(array $data)
	{
		return new RoutePattern($data);
	}

	/**
	 * @test
	 * @return null
	 */
	public function createPatternWithIndexArray()
	{
		$data = array('/^users$/', 'my-route', 'my-group');
		$pattern = $this->createRoutePattern($data);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RoutePatternInterface',
			$pattern
		);
		$this->assertEquals($data[0], $pattern->getRegEx());
		$this->assertEquals($data[1], $pattern->getRouteKey());
		$this->assertEquals($data[2], $pattern->getGroup());

		$data = array('/^users$/', 'my-route');
		$pattern = $this->createRoutePattern($data);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RoutePatternInterface',
			$pattern
		);
		$this->assertEquals($data[0],   $pattern->getRegEx());
		$this->assertEquals($data[1],   $pattern->getRouteKey());
		$this->assertEquals('no-group', $pattern->getGroup());
	}

	/**
	 * @test
	 * @return null
	 */
	public function createPatternAssociativeIndexArray()
	{
		$data = array(
			'pattern'	=> '/^users$/', 
			'route-key' => 'my-route', 
			'group'		=> 'my-group'
		);
		$pattern = $this->createRoutePattern($data);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RoutePatternInterface',
			$pattern
		);
		$this->assertEquals($data['pattern'],	$pattern->getRegEx());
		$this->assertEquals($data['route-key'], $pattern->getRouteKey());
		$this->assertEquals($data['group'],		$pattern->getGroup());

		$data = array(
			'pattern'		=> '/^users$/', 
			'route-key'     => 'my-route'
		);
		$pattern = $this->createRoutePattern($data);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\RoutePatternInterface',
			$pattern
		);
		$this->assertEquals($data['pattern'],	$pattern->getRegEx());
		$this->assertEquals($data['route-key'], $pattern->getRouteKey());
		$this->assertEquals('no-group',			$pattern->getGroup());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function createPatternEmptyArray()
	{
		$msg = 'route key must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
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
		
		$data = array('/^somepatter$/', $key);
		$pattern = $this->createRoutePattern($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function createPatternInvalidPattern($pattern)
	{
		$msg = 'pattern must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		
		$data = array($pattern, 'my-key');
		$pattern = $this->createRoutePattern($data);
	}

	/**
	 * @test
	 * @return			null
	 */
	public function createPatternInvalidGroup()
	{
		$msg = 'group must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		
		$data = array('/^somepattern$/', 'my-key', array(1,2,3));
		$pattern = $this->createRoutePattern($data);
	}
}
