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
	Appfuel\Kernel\Route\RouteActionSpec;

class RouteActionTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidMethod()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array('')		
		);
	}

	/**
	 * @return	RouteAction
	 */
	public function createRouteActionSpec(array $spec)
	{
		return new RouteActionSpec($spec);
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function routeActionInterface()
	{
		$spec = array(
			'namespace'   => 'MyName\Space',
			'action-name' => 'MyAction'
		);
		$action = $this->createRouteActionSpec($spec);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Route\RouteActionSpecInterface',
			$action
		);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function findActionActionName()
	{
		$ns = "MyNamespace";
		$spec = array(
			'namespace'   => $ns,
			'action-name' => 'MyAction'
		);
		$action = $this->createRouteActionSpec($spec);

		$this->assertEquals("$ns\MyAction", $action->findAction());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return			null
	 */
	public function actionNameFailure($badName)
	{
		$msg = 'action name must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
	
		$spec = array(
			'namespace'   => 'MyName\Space',
			'action-name' => $badName
		);	
		$action = $this->createRouteActionSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function actionMap()
	{
		$ns = 'MyName\Space';
		$map = array(
			'get'	 => 'MyGet',
			'post'	 => 'MyPost',
			'put'	 => 'MyPut',
			'delete' => 'MyDelete'
		);
		$spec = array(
			'namespace'   => $ns,
			'map'		  => $map
		);
		$action = $this->createRouteActionSpec($spec);
		$this->assertEquals("$ns\MyGet", $action->findAction('get'));
		$this->assertFalse($action->findAction('GET'));
		$this->assertEquals("$ns\MyPost", $action->findAction('post'));
		$this->assertEquals("$ns\MyPut", $action->findAction('put'));
		$this->assertEquals("$ns\MyDelete", $action->findAction('delete'));
		$this->assertFalse($action->findAction('not-found'));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidMethod
	 * @return			null
	 */
	public function setMapInvalidMethodFailure($badMethod)
	{
		$msg = 'action map method must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		
		$map = array(
			'get'		=> 'MyGet',
			'put'		=> 'MyPut',
			'delete'	=> 'MyDelete',
			$badMethod	=> 'MyBadMethod'
		);
		$spec = array(
			'namespace' => "MyNamespace",
			'map'		=> $map
		);
		$action = $this->createRouteActionSpec($spec);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function setMapInvalidActionNameFailure($badName)
	{
		$msg = 'action map action must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		$map = array(
			'get'		=> 'MyGet',
			'put'		=> 'MyPut',
			'delete'	=> 'MyDelete',
			'bad'		=> $badName
		);
		$spec = array(
			'namespace' => "MyNamespace",
			'map'		=> $map
		);
		$action = $this->createRouteActionSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function findActionEmptyMap()
	{
		$name = 'MyController';
		$ns   = "MyNamespace";
		$spec = array(
			'namespace'   => $ns,
			'action-name' => $name
		);
		$action = $this->createRouteActionSpec($spec);

		/* method is ignored */
		$this->assertEquals("$ns\\$name", $action->findAction('put'));
		$this->assertEquals("$ns\\$name", $action->findAction(12345));
		$this->assertEquals("$ns\\$name", $action->findAction());
	}
}
