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
	Appfuel\Kernel\Route\RouteAction;

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
	public function createRouteAction(array $spec)
	{
		return new RouteAction($spec);
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function routeActionInterface()
	{
		$spec = array('action-name' => 'MyAction');
		$action = $this->createRouteAction($spec);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Route\RouteActionInterface',
			$action
		);
	}

	/**
	 * @test
	 * @return	null
	 */
	public function findActionActionName()
	{
		$spec = array('action-name' => 'MyAction');
		$action = $this->createRouteAction($spec);

		$this->assertEquals('MyAction', $action->findAction());
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
		
		$spec = array('action-name' => $badName);
		$action = $this->createRouteAction($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function actionMap()
	{
		$map = array(
			'get'	 => 'MyGet',
			'post'	 => 'MyPost',
			'put'	 => 'MyPut',
			'delete' => 'MyDelete'
		);
		$spec = array('map' => $map);
		$action = $this->createRouteAction($spec);
		$this->assertEquals('MyGet', $action->findAction('get'));
		$this->assertFalse($action->findAction('GET'));
		$this->assertEquals('MyPost', $action->findAction('post'));
		$this->assertEquals('MyPut', $action->findAction('put'));
		$this->assertEquals('MyDelete', $action->findAction('delete'));
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
		$spec = array('map' => $map);
		$action = $this->createRouteAction($spec);
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
		$spec = array('map' => $map);
		$action = $this->createRouteAction($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function findActionEmptyMap()
	{
		$name = 'MyController';
		$spec = array('action-name' => $name);
		$action = $this->createRouteAction($spec);

		/* method is ignored */
		$this->assertEquals($name, $action->findAction('put'));
		$this->assertEquals($name, $action->findAction(12345));
		$this->assertEquals($name, $action->findAction());
	}
}
