<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * For complete copywrite and license details see the LICENSE file distributed   
 * with this source code.                                                        
 */
namespace TestFuel\Unit\Kernel\Route;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Route\RouteStartup;

class RouteStartupTest extends BaseTestCase
{
	/**
	 * @return RouteStartup
	 */
	public function createRouteStartup(array $spec)
	{
		return new RouteStartup($spec);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function emptySpec()
	{
		$startup = $this->createRouteStartup(array());
		$this->assertFalse($startup->isPrependStartupTasks());
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());
		$this->assertFalse($startup->isStartupDisabled());
		$this->assertFalse($startup->isStartupTasks());
		$this->assertEquals(array(), $startup->getStartupTasks());
		$this->assertFalse($startup->isExcludedStartupTasks());
		$this->assertEquals(array(), $startup->getExcludedStartupTasks());
	}

	/**
	 * only a strict bool true will toggle this setting
	 *
	 * @test
	 * @return	null
	 */
	public function prependStartupTasks()
	{
		$spec = array('is-prepended' => true);
		$startup = $this->createRouteStartup($spec);
		$this->assertTrue($startup->isPrependStartupTasks());

		$spec = array('is-prepended' => false);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isPrependStartupTasks());
	
		$spec = array('is-prepended' => 1);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isPrependStartupTasks());
	
		$spec = array('is-prepended' => 'on');
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isPrependStartupTasks());
	}

	/**
	 * only a strict bool true will toggle this setting
	 *
	 * @test
	 * @return	null
	 */
	public function ignoreConfigStartupTasks()
	{
		$spec = array('is-config-ignored' => true);
		$startup = $this->createRouteStartup($spec);
		$this->assertTrue($startup->isIgnoreConfigStartupTasks());

		$spec = array('is-config-ignored' => false);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());
	
		$spec = array('is-config-ignored' => 1);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());
	
		$spec = array('is-config-ignored' => 'on');
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isIgnoreConfigStartupTasks());
	}

	/**
	 * only a strict bool true will toggle this setting
	 *
	 * @test
	 * @return	null
	 */
	public function disableStartupTasks()
	{
		$spec = array('is-disabled' => true);
		$startup = $this->createRouteStartup($spec);
		$this->assertTrue($startup->isStartupDisabled());

		$spec = array('is-disabled' => false);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isStartupDisabled());
	
		$spec = array('is-disabled' => 1);
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isStartupDisabled());
	
		$spec = array('is-disabled' => 'on');
		$startup = $this->createRouteStartup($spec);
		$this->assertFalse($startup->isStartupDisabled());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function startupTasks()
	{
		$tasks = array('MyTask', 'YourTask');
		$spec  = array('tasks' => $tasks);
		$startup = $this->createRouteStartup($spec);
		
		$this->assertEquals($tasks, $startup->getStartupTasks());
		$this->assertTrue($startup->isStartupTasks());

		$spec  = array('tasks' => array());
		$startup = $this->createRouteStartup($spec);
		$this->assertEquals(array(), $startup->getStartupTasks());
		$this->assertFalse($startup->isStartupTasks());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function startupTasksNotAStringFailure($task)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', $task);
		$spec  = array('tasks' => $tasks);
		$startup = $this->createRouteStartup($spec);
	}

	/**
	 * @test
	 * @return	RouteStartup
	 */
	public function excludedStartupTasks()
	{
		$tasks = array('MyTask', 'YourTask');
		$spec  = array('excluded-tasks' => $tasks);
		$startup = $this->createRouteStartup($spec);

		$this->assertEquals($tasks, $startup->getExcludedStartupTasks());
		$this->assertTrue($startup->isExcludedStartupTasks());

		$spec    = array('excluded-tasks' => array());
		$startup = $this->createRouteStartup($spec);
		$this->assertEquals(array(), $startup->getExcludedStartupTasks());
		$this->assertFalse($startup->isExcludedStartupTasks());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function excludedStartupNotStrFailure($task)
	{
		$msg = 'startup tasks must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$tasks = array('MyTask', 'YourTask', $task);
		$spec  = array('excluded-tasks' => $tasks);
		$startup = $this->createRouteStartup($spec);
	}
}
