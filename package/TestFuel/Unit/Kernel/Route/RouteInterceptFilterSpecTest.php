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
	Appfuel\Kernel\Route\RouteInterceptFilterSpec;

class RouteInterceptFilterSpecTest extends BaseTestCase
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
     * @return  array                                                            
     */                                                                          
    public function provideInvalidFilterName()                                   
    {                                                                            
        return array(                                                            
            array(12345),                                                        
            array(1.234),                                                        
            array(true),                                                         
            array(false),                                                        
            array(array(1,2,3)),                                                 
            array(new StdClass()),                                               
            array('')                                                            
        );                                                                       
    }

	/**
	 * @return	RouteAccess
	 */
	public function createRouteInterceptFilterSpec(array $spec)
	{
		return new RouteInterceptFilterSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec = array();

		$filter = $this->createRouteInterceptFilterSpec($spec);
        $this->assertInstanceOf(                                                 
            'Appfuel\Kernel\Route\RouteInterceptFilterSpecInterface',                        
            $filter                         
        ); 
		$this->assertTrue($filter->isPreFilteringEnabled());
		$this->assertEquals(array(), $filter->getPreFilters());
		$this->assertFalse($filter->isPreFilters());
		
		$this->assertEquals(array(), $filter->getExcludedPreFilters());
		$this->assertFalse($filter->isExcludedPreFilters());

		$this->assertTrue($filter->isPostFilteringEnabled());
		$this->assertEquals(array(), $filter->getPostFilters());
		$this->assertFalse($filter->isPostFilters());
	
		$this->assertEquals(array(), $filter->getExcludedPostFilters());
		$this->assertFalse($filter->isExcludedPostFilters());

		return $spec;
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function skipPreFilters(array $spec)
	{
		$spec['disable-pre-filters'] = true;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertFalse($filter->isPreFilteringEnabled());
	}

	/**
	 * Only a bool true will toggle the enabled pre filter flag to disabled
	 *
	 * @test
	 * @dataProvider	provideNonTrueValues	
	 * @return			array
	 */
	public function skipPreFiltersNoBoolTrue($bool)
	{
		$spec['disable-pre-filters'] = $bool;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isPreFilteringEnabled());
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function setPreFilters(array $spec)
	{
		$spec['pre-filters'] = array('FilterA', 'FilterB', 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isPreFilters());	
		$this->assertEquals($spec['pre-filters'], $filter->getPreFilters());

		return $spec;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	array
	 */
	public function setPreFilterFailure($filter)
	{
		$msg = 'pre intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);

		$spec['pre-filters'] = array('FilterA', $filter, 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function setExcludedPreFilters(array $spec)
	{
		$spec['exclude-pre-filters'] = array('FilterA', 'FilterB', 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isExcludedPreFilters());	
		$this->assertEquals(
			$spec['exclude-pre-filters'], 
			$filter->getExcludedPreFilters()
		);

		return $spec;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	array
	 */
	public function setPreExcludedFilterFailure($filter)
	{
		$msg = 'excluded pre intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);

		$spec['exclude-pre-filters'] = array('FilterA', $filter, 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function setExcludedPreAndPreFilters(array $spec)
	{
		$spec['pre-filters'] = array('FilterA', 'FilterB', 'FilterC');
		$spec['exclude-pre-filters'] = array('FilterD', 'FilterE', 'FilterF');
		$filter = $this->createRouteInterceptFilterSpec($spec);

		$this->assertTrue($filter->isPreFilters());	
		$this->assertEquals($spec['pre-filters'], $filter->getPreFilters());

		$this->assertTrue($filter->isExcludedPreFilters());	
		$this->assertEquals(
			$spec['exclude-pre-filters'], 
			$filter->getExcludedPreFilters()
		);

		return $spec;
	}

	/**
	 * @test
	 * @depends	setExcludedPreAndPreFilters
	 * @return	array
	 */
	public function setExcludedPreAndPreFiltersWhenSkip(array $spec)
	{
		$spec['disable-pre-filters'] = true;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertFalse($filter->isPreFilteringEnabled());

		$this->assertFalse($filter->isPreFilters());	
		$this->assertEquals(array(), $filter->getPreFilters());

		$this->assertFalse($filter->isExcludedPreFilters());	
		$this->assertEquals(array(), $filter->getExcludedPreFilters());

		return $spec;
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function skipPostFilters(array $spec)
	{
		$spec['disable-post-filters'] = true;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertFalse($filter->isPostFilteringEnabled());
	}

	/**
	 * Only a bool true will toggle the enabled post filter flag to disabled
	 *
	 * @test
	 * @dataProvider	provideNonTrueValues	
	 * @return			array
	 */
	public function skipPostFiltersNoBoolTrue($bool)
	{
		$spec['disable-post-filters'] = $bool;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isPostFilteringEnabled());
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function setPostFilters(array $spec)
	{
		$spec['post-filters'] = array('FilterA', 'FilterB', 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isPostFilters());	
		$this->assertEquals($spec['post-filters'], $filter->getPostFilters());

		return $spec;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	array
	 */
	public function setPostFilterFailure($filter)
	{
		$msg = 'post intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);

		$spec['post-filters'] = array('FilterA', $filter, 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	array
	 */
	public function setExcludedPostFilters(array $spec)
	{
		$spec['exclude-post-filters'] = array('FilterA', 'FilterB', 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertTrue($filter->isExcludedPostFilters());	
		$this->assertEquals(
			$spec['exclude-post-filters'], 
			$filter->getExcludedPostFilters()
		);

		return $spec;
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	array
	 */
	public function setPostExcludedFilterFailure($filter)
	{
		$msg = 'excluded post intercept filter must be a non empty string';
		$this->setExpectedException('DomainException', $msg);

		$spec['exclude-post-filters'] = array('FilterA', $filter, 'FilterC');
		$filter = $this->createRouteInterceptFilterSpec($spec);
	}

	/**
	 * @test
	 * @depends	setPostFilters
	 * @return	array
	 */
	public function setExcludedPostAndPostFilters(array $spec)
	{
		$spec['exclude-post-filters'] = array('FilterD', 'FilterE', 'FilterF');
		$filter = $this->createRouteInterceptFilterSpec($spec);

		$this->assertTrue($filter->isPostFilters());	
		$this->assertEquals($spec['post-filters'], $filter->getPostFilters());

		$this->assertTrue($filter->isExcludedPostFilters());	
		$this->assertEquals(
			$spec['exclude-post-filters'], 
			$filter->getExcludedPostFilters()
		);

		return $spec;
	}

	/**
	 * @test
	 * @depends	setExcludedPostAndPostFilters
	 * @return	array
	 */
	public function setExcludedPostAndPostFiltersWhenSkip(array $spec)
	{
		$spec['disable-post-filters'] = true;
		$filter = $this->createRouteInterceptFilterSpec($spec);
		$this->assertFalse($filter->isPostFilteringEnabled());

		$this->assertFalse($filter->isPostFilters());	
		$this->assertEquals(array(), $filter->getPostFilters());

		$this->assertFalse($filter->isExcludedPostFilters());	
		$this->assertEquals(array(), $filter->getExcludedPostFilters());

		return $spec;
	}

	/**
	 * @test
	 * @depends	setExcludedPostAndPostFilters
	 * @return	array
	 */
	public function setExcludedPrePostAndPrePostFilters(array $spec)
	{
		$spec['pre-filters'] = array('FilterU', 'FilterV', 'FilterW');
		$spec['exclude-pre-filters'] = array('FilterX', 'FilterY', 'FilterZ');
		$filter = $this->createRouteInterceptFilterSpec($spec);

		$this->assertTrue($filter->isPreFilters());	
		$this->assertEquals($spec['pre-filters'], $filter->getPreFilters());

		$this->assertTrue($filter->isExcludedPreFilters());	
		$this->assertEquals(
			$spec['exclude-pre-filters'], 
			$filter->getExcludedPreFilters()
		);

		$this->assertTrue($filter->isPostFilters());	
		$this->assertEquals($spec['post-filters'], $filter->getPostFilters());

		$this->assertTrue($filter->isExcludedPostFilters());	
		$this->assertEquals(
			$spec['exclude-post-filters'], 
			$filter->getExcludedPostFilters()
		);

		return $spec;
	}

	/**
	 * @test
	 * @depends	setExcludedPrePostAndPrePostFilters
	 * @return	array
	 */
	public function setExcludedBothAndBothFiltersWhenSkip(array $spec)
	{
		$spec['disable-post-filters'] = true;
		$spec['disable-pre-filters']  = true;
		$filter = $this->createRouteInterceptFilterSpec($spec);

		$this->assertFalse($filter->isPreFilteringEnabled());

		$this->assertFalse($filter->isPreFilters());	
		$this->assertEquals(array(), $filter->getPreFilters());

		$this->assertFalse($filter->isExcludedPreFilters());	
		$this->assertEquals(array(), $filter->getExcludedPreFilters());


		$this->assertFalse($filter->isPostFilteringEnabled());

		$this->assertFalse($filter->isPostFilters());	
		$this->assertEquals(array(), $filter->getPostFilters());

		$this->assertFalse($filter->isExcludedPostFilters());	
		$this->assertEquals(array(), $filter->getExcludedPostFilters());

		return $spec;
	}
}
