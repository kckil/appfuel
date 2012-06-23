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
	Appfuel\Kernel\Route\RouteInputValidationSpec;

class RouteInputValidationTest extends BaseTestCase
{
	/**
	 * Used when testing that only a strict bool true is allowed
	 *
	 * @return array
	 */
	public function provideNonTrueValues()
	{
		return array(
			[false],
			array(0),
			array(1),
			array('on'),
			array('true')
		);
	}

	/**
	 * Used when testing that only a strict bool false is allowed
	 *
	 * @return array
	 */
	public function provideNonFalseValues()
	{
		return array(
			array(true),
			array(0),
			array(1),
			array('off'),
			array('false')
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidErrorCodes()
	{
		return	array(
			array(500),
			array(404),
			array('A100'),
			array('some text'),
			array(''),
			array(null),
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidErrorCodes()
	{
		return array(
			array(array(1,3,4)),
			array(new StdClass())
		);
	}


	/**
	 * @return	RouteInputValidationSpec
	 */
	public function createRouteInputValidationSpec(array $spec)
	{
		return new RouteInputValidationSpec($spec);
	}

	/**
	 * @test
	 * @return		null
	 */
	public function defaultSettings()
	{
		$spec = array();
		$input = $this->createRouteInputValidationSpec($spec);
        $this->assertInstanceOf(                                                 
            'Appfuel\Kernel\Route\RouteInputValidationSpecInterface',                        
            $input
        );
		$this->assertTrue($input->isInputValidation());
		$this->assertTrue($input->isThrowOnFailure());
		$this->assertEquals(500, $input->getErrorCode());
		$this->assertFalse($input->isSpecList());
		$this->assertEquals(array(), $input->getSpecList());	

		return $spec;
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function disableValidation(array $spec)
	{
		$spec['disable-validation'] = true;
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertFalse($input->isInputValidation());
	}

	/**
	 * Only a string bool true will disable input validation
	 *
	 * @test
	 * @dataProvider	provideNonTrueValues
	 * @return			null
	 */
	public function disableValidationNonTrueValues($value)
	{
		$spec['disable-validation'] = $value;
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertTrue($input->isInputValidation());
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function disableThrowOnFailure(array $spec)
	{
		$spec['disable-validation-failures'] = true;
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertFalse($input->isThrowOnFailure());
	}

	/**
	 * Only a string bool false will disable the isThrowOnFailure flag
	 *
	 * @test
	 * @dataProvider	provideNonTrueValues
	 * @return			null
	 */
	public function disableThrowOnFailureNonTrueValues($value)
	{
		$spec['disable-validation-failures'] = $value;
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertTrue($input->isThrowOnFailure());
	}

	/**
	 * @test
	 * @dataProvider	provideValidErrorCodes
	 * @return			null
	 */
	public function errorCode($code)
	{
		$spec['validation-error-code'] = $code;
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertEquals($code, $input->getErrorCode());
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidErrorCodes
	 * @return			null
	 */
	public function errorCodeInvalidFailure($code)
	{
		$msg = 'error code must be a scalar value or null';
		$this->setExpectedException('DomainException', $msg);

		$spec['validation-error-code'] = $code;
		$input = $this->createRouteInputValidationSpec($spec);
	}

	/**
	 * @test
	 * @depends	defaultSettings
	 * @return	null
	 */
	public function specList(array $spec)
	{
		$spec['validation-spec'] = array(
			array(
				'field' => 'id',
				'filters' => array(
					'int' => array(
						'options' => array('min' => 1),
						'error' => 'id must be an integer'
					)
				)
			)
		);
		$input = $this->createRouteInputValidationSpec($spec);
		$this->assertEquals($spec['validation-spec'], $input->getSpecList());
	}
}
