<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Kernel\Route;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Route\RouteAccessSpec;

class RouteAccessSpecTest extends BaseTestCase
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
			array('true'),
            array(new StdClass())
		);
	}

	/**
	 * @return	RouteAccess
	 */
	public function createRouteAccessSpec(array $spec)
	{
		return new RouteAccessSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec = array();

		$access = $this->createRouteAccessSpec($spec);
		$this->assertFalse($access->isPublicAccess());
		$this->assertFalse($access->isInternalOnlyAccess());
		$this->assertFalse($access->isAclAccessIgnored());
		$this->assertFalse($access->isAclForeachMethod());		

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
		$access = $this->createRouteAccessSpec($spec);
		$this->assertTrue($access->isPublicAccess());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function publicFalseValues($bool)
	{
		$spec['is-public'] = $bool;
		$access = $this->createRouteAccessSpec($spec);
		$this->assertFalse($access->isPublicAccess());
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function internalOnlyAccess($spec)
	{
		$spec['is-internal'] = true;
		$access = $this->createRouteAccessSpec($spec);
		$this->assertTrue($access->isInternalOnlyAccess());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function internalOnlyAccessFalseValues($bool)
	{
		$spec['is-internal'] = $bool;
		$access = $this->createRouteAccessSpec($spec);
		$this->assertFalse($access->isInternalOnlyAccess());
	}

	/**
	 * @test
	 * @depends		defaultSettings
	 * @return		null
	 */
	public function ignoreAcl($spec)
	{
		$spec['is-ignore-acl'] = true;
		$access = $this->createRouteAccessSpec($spec);
		$this->assertTrue($access->isAclAccessIgnored());
	}

	/**
	 * @test
	 * @dataProvider	provideFalseBoolValues
	 * @return			null
	 */
	public function ignoreAclFalseValues($bool)
	{
		$spec['is-ignore-acl'] = $bool;
		$access = $this->createRouteAccessSpec($spec);
		$this->assertFalse($access->isAclAccessIgnored());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isAclAccessNotMappedAllowed()
	{
		$spec = array('acl' => array('admin', 'producer', 'guest'));
		$access = $this->createRouteAccessSpec($spec);
		$this->assertTrue($access->isAccessAllowed('admin'));
		$this->assertTrue($access->isAccessAllowed('producer'));
		$this->assertTrue($access->isAccessAllowed('guest'));
		$this->assertFalse($access->isAccessAllowed('manager'));
	
		$this->assertFalse($access->isAccessAllowed('manager', 'get'));
		
		/* method is ignored when acls are not mapped to a method */	
		$this->assertTrue($access->isAccessAllowed('admin', 'post'));
		$this->assertTrue($access->isAccessAllowed('admin', 'put'));
		$this->assertTrue($access->isAccessAllowed('admin', 'delete'));
		$this->assertTrue($access->isAccessAllowed('admin', ''));
		$this->assertTrue($access->isAccessAllowed('admin', array('1', 1,2)));
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function isAclAccessNotMapedNotString($code)
	{
		$spec = array('acl' => array('admin', 'producer', 'guest'));
		$access = $this->createRouteAccessSpec($spec);
		$this->assertFalse($access->isAccessAllowed($code));
	}

	/**
	 * @test
	 * @return	null
	 */
	public function isAclAccessMappedAllowed()
	{
		$spec = array(
			'acl' => array(
				'get'	 => array('admin', 'producer', 'guest'),
				'put'	 => array('admin', 'producer'),
				'post'	 => array('admin', 'producer'),
				'delete' => array('admin')
			),
		);
		$access = $this->createRouteAccessSpec($spec);
		$this->assertTrue($access->isAccessAllowed('admin', 'get'));
		$this->assertTrue($access->isAccessAllowed('admin', 'put'));
		$this->assertTrue($access->isAccessAllowed('admin', 'post'));
		$this->assertTrue($access->isAccessAllowed('admin', 'delete'));

		$this->assertTrue($access->isAccessAllowed('producer',  'get'));
		$this->assertTrue($access->isAccessAllowed('producer',  'put'));
		$this->assertTrue($access->isAccessAllowed('producer',  'post'));
		$this->assertFalse($access->isAccessAllowed('producer', 'delete'));
		
		$this->assertTrue($access->isAccessAllowed('guest',  'get'));
		$this->assertFalse($access->isAccessAllowed('guest', 'put'));
		$this->assertFalse($access->isAccessAllowed('guest', 'post'));
		$this->assertFalse($access->isAccessAllowed('guest', 'delete'));


		$this->assertFalse($access->isAccessAllowed('manager', 'get'));
		$this->assertFalse($access->isAccessAllowed('guest', ''));
		$this->assertFalse($access->isAccessAllowed('guest', array(1,2,3)));

	
		return $spec;
	}

	/**
	 * @test
	 * @depends	isAclAccessMappedAllowed
	 * @return	null
	 */
	public function isAclAccessMappedAllowedManyCodes(array $spec)
	{
		$access = $this->createRouteAccessSpec($spec);

		$codes = array('my-code', 'your-code', 'guest');
		$this->assertTrue($access->isAccessAllowed($codes, 'get'));
		
		array_pop($codes);
		$this->assertFalse($access->isAccessAllowed($codes, 'get'));

		array_unshift($codes, 'admin');
		$this->assertTrue($access->isAccessAllowed($codes, 'put'));
		$this->assertTrue($access->isAccessAllowed($codes, 'post'));
		$this->assertTrue($access->isAccessAllowed($codes, 'delete'));
		$this->assertFalse($access->isAccessAllowed($codes, 'not-there'));
	
		array_shift($codes);
		$this->assertFalse($access->isAccessAllowed($codes, 'put'));
		$this->assertFalse($access->isAccessAllowed($codes, 'post'));
		$this->assertFalse($access->isAccessAllowed($codes, 'delete'));
		$this->assertFalse($access->isAccessAllowed($codes, 'not-there'));
			
		return $spec;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return			null
	 */
	public function aclNotMappedInvalidAclCode($code)
	{
		$msg = 'all acl codes must be non empty strings';
		$this->setExpectedException('DomainException', $msg);
		$spec = array('acl' => array('admin', $code, 'guest'));
		$access = $this->createRouteAccessSpec($spec);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidMethod 
	 * @return			null
	 */
	public function acltMappedInvalidMethodAclCode($method)
	{
		$this->setExpectedException('DomainException');
		$spec = array(
			'acl' => array(
				$method => array('admin','somecode', 'guest')
			)
		);

		$access = $this->createRouteAccessSpec($spec);
	}

	/**
	 * @test
	 * @return			null
	 */
	public function acltMappedInvaliddAclCodeNotAnArray()
	{
		$msg = 'list of codes for -(put) must be an array';
		$this->setExpectedException('DomainException', $msg);

		$spec = array(
			'acl' => array(
				'put' => 'guest'
			)
		);

		$access = $this->createRouteAccessSpec($spec);
	}




}
