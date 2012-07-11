<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
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
	 * For any flag in this spec the only valid true value is a bool true,
	 * everything else is considered false
	 *
	 * @return	array
	 */
	public function provideFalseBoolValues()
	{
		return array(
			array(false),
			array('on'),
			array(1),
			array(0),
			array('true')
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
	 * The minimum required fields that will allow the spec to be created
	 * @return	array
	 */
	public function getDefaultSpecData()
	{
		return array(
			'namespace'   => 'MyName\Space',
			'action' => 'MyAction'
		);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec   = $this->getDefaultSpecData();
		$action = $this->createRouteActionSpec($spec);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Route\RouteActionSpecInterface',
			$action
		);
		return $spec;
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function noNamespaceSet($spec)
	{
		$msg = 'mvc action namespace -(namespace) is required but not set';
		$this->setExpectedException('DomainException', $msg);
		
		unset($spec['namespace']);
		$action = $this->createRouteActionSpec($spec);
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function noActionSet($spec)
	{
		$msg = 'the key -(action) must be set';
		$this->setExpectedException('DomainException', $msg);
		
		unset($spec['action']);
		$action = $this->createRouteActionSpec($spec);
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
			'action' => 'MyAction'
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
		$this->setExpectedException('DomainException');
	
		$spec = array(
			'namespace'   => 'MyName\Space',
			'action' => $badName
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
			'namespace' => $ns,
			'action'	=> $map
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
			'action'	=> $map
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
			'action'	=> $map
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
			'namespace' => $ns,
			'action'    => $name
		);
		$action = $this->createRouteActionSpec($spec);

		/* method is ignored */
		$this->assertEquals("$ns\\$name", $action->findAction('put'));
		$this->assertEquals("$ns\\$name", $action->findAction(12345));
		$this->assertEquals("$ns\\$name", $action->findAction());
	}
}
