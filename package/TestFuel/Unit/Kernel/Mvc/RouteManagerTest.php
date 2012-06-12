<?php
/**
 * Appfuel 
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 * 
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed 
 * with this source code.
 */
namespace Testfuel\Unit\Kernel\Mvc;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\RouteManager;

class RouteManagerTest extends BaseTestCase
{
	/**
	 * @var array
	 */
	protected $routesBk = array();

	/**
	 * @var array
	 */
	protected $patternsBk = array();

	/**
	 * @return	null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->routesBk   = RouteManager::getRoutes();
		$this->patternsBk = RouteManager::getPatternMap();

		RouteManager::enablePatternMatching();
		RouteManager::enableKeyLookup();
		RouteManager::useKeyLookupBeforePatternMatching();

		RouteManager::clearRoutes();
		RouteManager::clearPatternMap();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		parent::tearDown();
		RouteManager::setRoutes($this->routesBk);
		RouteManager::setPatternMap($this->patternsBk);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isPatternMatching()
	{
		$this->assertNull(RouteManager::disablePatternMatching());
		$this->assertFalse(RouteManager::isPatternMatching());
		
		$this->assertNull(RouteManager::enablePatternMatching());
		$this->assertTrue(RouteManager::isPatternMatching());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isKeylookup()
	{
		$this->assertNull(RouteManager::disableKeyLookup());
		$this->assertFalse(RouteManager::isKeyLookup());
		
		$this->assertNull(RouteManager::enableKeyLookup());
		$this->assertTrue(RouteManager::isKeyLookup());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isPatternMatchingBeforeKeyLookup()
	{
		$this->assertNull(RouteManager::usePatternMatchingBeforeKeyLookup());
		$this->assertTrue(RouteManager::isPatternMatchingBeforeKeyLookup());
	
		$this->assertNull(RouteManager::useKeyLookupBeforePatternMatching());
		$this->assertFalse(RouteManager::isPatternMatchingBeforeKeyLookup());
	}
}
