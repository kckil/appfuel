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
    Appfuel\Kernel\Route\RouteStartupSpec;

class RouteStartupSpecTest extends BaseTestCase
{
    /**
     * @return RouteStartup
     */
    public function createRouteStartupSpec(array $spec)
    {
        return new RouteStartupSpec($spec);
    }

    /**
     * @test
     * @return    null
     */
    public function emptySpec()
    {
        $startup = $this->createRouteStartupSpec(array());
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
     * @return    null
     */
    public function prependStartupTasks()
    {
        $spec = array('prepend-startup-tasks' => true);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertTrue($startup->isPrependStartupTasks());

        $spec = array('prepend-startup-tasks' => false);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isPrependStartupTasks());
    
        $spec = array('prepend-startup-tasks' => 1);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isPrependStartupTasks());
    
        $spec = array('prepend-startup-tasks' => 'on');
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isPrependStartupTasks());
    }

    /**
     * only a strict bool true will toggle this setting
     *
     * @test
     * @return    null
     */
    public function ignoreConfigStartupTasks()
    {
        $spec = array('only-route-startup-tasks' => true);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertTrue($startup->isIgnoreConfigStartupTasks());

        $spec = array('only-route-startup-tasks' => false);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isIgnoreConfigStartupTasks());
    
        $spec = array('only-route-startup-tasks' => 1);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isIgnoreConfigStartupTasks());
    
        $spec = array('only-route-startup-tasks' => 'on');
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isIgnoreConfigStartupTasks());
    }

    /**
     * only a strict bool true will toggle this setting
     *
     * @test
     * @return    null
     */
    public function disableStartupTasks()
    {
        $spec = array('disable-startup' => true);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertTrue($startup->isStartupDisabled());

        $spec = array('disable-startup' => false);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isStartupDisabled());
    
        $spec = array('disable-startup' => 1);
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isStartupDisabled());
    
        $spec = array('disable-startup' => 'on');
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertFalse($startup->isStartupDisabled());
    }

    /**
     * @test
     * @return    null
     */
    public function startupTasks()
    {
        $tasks = array('MyTask', 'YourTask');
        $spec  = array('startup-tasks' => $tasks);
        $startup = $this->createRouteStartupSpec($spec);
        
        $this->assertEquals($tasks, $startup->getStartupTasks());
        $this->assertTrue($startup->isStartupTasks());

        $spec  = array('startup-tasks' => array());
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertEquals(array(), $startup->getStartupTasks());
        $this->assertFalse($startup->isStartupTasks());
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return    null
     */
    public function startupTasksNotAStringFailure($task)
    {
        $msg = 'startup tasks must be non empty strings';
        $this->setExpectedException('DomainException', $msg);
        $tasks = array('MyTask', 'YourTask', $task);
        $spec  = array('startup-tasks' => $tasks);
        $startup = $this->createRouteStartupSpec($spec);
    }

    /**
     * @test
     * @return    RouteStartup
     */
    public function excludedStartupTasks()
    {
        $tasks = array('MyTask', 'YourTask');
        $spec  = array('excluded-startup-tasks' => $tasks);
        $startup = $this->createRouteStartupSpec($spec);

        $this->assertEquals($tasks, $startup->getExcludedStartupTasks());
        $this->assertTrue($startup->isExcludedStartupTasks());

        $spec    = array('excluded-startup-tasks' => array());
        $startup = $this->createRouteStartupSpec($spec);
        $this->assertEquals(array(), $startup->getExcludedStartupTasks());
        $this->assertFalse($startup->isExcludedStartupTasks());
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return    null
     */
    public function excludedStartupNotStrFailure($task)
    {
        $msg = 'startup tasks must be non empty strings';
        $this->setExpectedException('DomainException', $msg);
        $tasks = array('MyTask', 'YourTask', $task);
        $spec  = array('excluded-startup-tasks' => $tasks);
        $startup = $this->createRouteStartupSpec($spec);
    }
}
