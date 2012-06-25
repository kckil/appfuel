<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Kernel\Route;

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Route\RouteViewSpec;

class RouteViewSpecTest extends BaseTestCase
{
	/**
	 * Used when testing that only a strict bool true is allowed
	 *
	 * @return array
	 */
	public function provideNonTrueValues()
	{
		return array(
			array(false),
			array(0),
			array(1),
			array('on'),
			array('true')
		);
	}

	/**
	 * @return	RouteAccess
	 */
	public function createRouteViewSpec(array $spec)
	{
		return new RouteViewSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec = array();
		$view = $this->createRouteViewSpec($spec);
        $this->assertInstanceOf(                                                 
            'Appfuel\Kernel\Route\RouteViewSpecInterface',                        
            $view
        );
	
		$this->assertEquals('html', $view->getDefaultFormat());	
		$this->assertFalse($view->isViewDisabled());
		$this->assertFalse($view->isManualView());
		$this->assertFalse($view->isViewPackage());
		$this->assertNull($view->getViewPackage());

		return $spec;
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function disableView(array $spec)
	{
		$spec['disable-view'] = true;
		$view = $this->createRouteViewSpec($spec);
		$this->assertTrue($view->isViewDisabled());
	}

	/**
	 * @test
	 * @dataProvider	provideNonTrueValues
	 * @return			null
	 */
	public function disableViewNonTrueValues($value)
	{
		$spec['disable-view'] = $value;
		$view = $this->createRouteViewSpec($spec);
		$this->assertFalse($view->isViewDisabled());
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function manualView(array $spec)
	{
		$spec['manual-view'] = true;
		$view = $this->createRouteViewSpec($spec);
		$this->assertTrue($view->isManualView());
	}

	/**
	 * @test
	 * @dataProvider	provideNonTrueValues
	 * @return			null
	 */
	public function manualViewNonTrueValues($value)
	{
		$spec['manual-view'] = $value;
		$view = $this->createRouteViewSpec($spec);
		$this->assertFalse($view->isManualView());
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function defaultFormat(array $spec)
	{
		$spec['default-format'] = 'json';
		$view = $this->createRouteViewSpec($spec);
		$this->assertEquals('json', $view->getDefaultFormat());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return null
	 */
	public function defaultFormatNotStringFailure($format)
	{
		$msg = 'route format must be a string';
		$this->setExpectedException('DomainException', $msg);
		$spec['default-format'] = $format;
		$view = $this->createRouteViewSpec($spec);
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function viewPackage(array $spec)
	{
		$spec['view-pkg'] = 'appfuel:page.my-page';
		$view = $this->createRouteViewSpec($spec);
		$this->assertEquals($spec['view-pkg'], $view->getViewPackage());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return null
	 */
	public function viewPacakgeNotValidFailure($pkg)
	{
		$msg = 'view package name must be a non empty string';
		$this->setExpectedException('DomainException', $msg);
		
		$spec['view-pkg'] = $pkg;
		$view = $this->createRouteViewSpec($spec);
	}


}
