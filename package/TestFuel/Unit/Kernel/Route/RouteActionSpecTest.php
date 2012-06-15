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
			'action-name' => 'MyAction'
		);
	}

	/**
	 * @test
	 * @return RouteIntercept
	 */
	public function routeActionInterface()
	{
		$spec   = $this->getDefaultSpecData();
		$action = $this->createRouteActionSpec($spec);
		$this->assertInstanceOf(
			'Appfuel\Kernel\Route\RouteActionSpecInterface',
			$action
		);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec = array(
			'namespace'   => 'MyNamespace',
			'action-name' => 'MyAction',
		);

		$action = $this->createRouteActionSpec($spec);
		$this->assertFalse($action->isPublicAccess());
		$this->assertFalse($action->isInternalOnlyAccess());
		$this->assertFalse($action->isAclAccessIgnored());
		$this->assertFalse($action->isAclForeachMethod());		

		return $spec;
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function publicAccess($spec)
	{
		$spec['is-public'] = true;
		$action = $this->createRouteActionSpec($spec);
		$this->assertTrue($action->isPublicAccess());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function publicFalseValues($bool)
	{
		$spec = $this->getDefaultSpecData();
		$spec['is-public'] = $bool;
		$action = $this->createRouteActionSpec($spec);
		$this->assertFalse($action->isPublicAccess());
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function internalOnlyAccess($spec)
	{
		$spec['is-internal'] = true;
		$action = $this->createRouteActionSpec($spec);
		$this->assertTrue($action->isInternalOnlyAccess());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function internalOnlyAccessFalseValues($bool)
	{
		$spec = $this->getDefaultSpecData();
		$spec['is-internal'] = $bool;
		$action = $this->createRouteActionSpec($spec);
		$this->assertFalse($action->isInternalOnlyAccess());
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function ignoreAcl($spec)
	{
		$spec['is-ignore-acl'] = true;
		$action = $this->createRouteActionSpec($spec);
		$this->assertTrue($action->isAclAccessIgnored());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function ignoreAclFalseValues($bool)
	{
		$spec = $this->getDefaultSpecData();
		$spec['is-ignore-acl'] = $bool;
		$action = $this->createRouteActionSpec($spec);
		$this->assertFalse($action->isAclAccessIgnored());
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
