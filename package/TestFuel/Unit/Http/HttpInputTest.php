<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Http;

use StdClass,
	Appfuel\Http\HttpInput,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\DataStructure\ArrayData;

/**
 * The request object was designed to service web,api and cli request
 */
class HttpInputTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideMostUsedMethods()
	{
		return array(
			array('get'),
			array('post'),
			array('put'),
			array('delete'),
			array('cli')
		);
	}

    /**
     * @return  string
     */
    public function getHttpInputInterface()
    {
        return 'Appfuel\Http\HttpInputInterface';
    }

	/**
	 * @param	string $method
	 * @param	array	$params
	 * @return	AppInput
	 */
	public function createAppInput($method, array $params = array())
	{
		return new HttpInput($method, $params);
	}

	/**
	 * @test
	 * @return  null	
	 */
	public function defaultGetConstructor()
	{
		$method = 'GET';
		$input = $this->createAppInput($method);
		$this->assertInstanceOf('Appfuel\Http\HttpInputInterface', $input);
		$this->assertTrue($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isDelete());
		$this->assertEquals('get', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());

		/*
		 * params are categorized by method, however its the builders
		 * responsibility to ensure param categories are available
		 */
		$this->assertFalse($input->getAll('get'));
		$this->assertFalse($input->getAll('post'));
	}

	/**
     * @test
	 * @return	null	
	 */
	public function defaultPostConstructor()
	{
		$method = 'Post';
		$input = $this->createAppInput($method);
		$this->assertTrue($input->isPost());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPut());
		$this->assertFalse($input->isDelete());
		$this->assertEquals('post', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('post'));
	}

	/**
     * @test
	 * @return  null	
	 */
	public function defaultPutConstructor()
	{
		$method = 'put';
		$input = $this->createAppInput($method);
		$this->assertTrue($input->isPut());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isDelete());
		$this->assertEquals('put', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('put'));
	}

	/**
     * @test
	 * @return  null	
	 */
	public function defaultDeleteConstructor()
	{
		$method = 'delete';
		$input = $this->createAppInput($method);
		$this->assertTrue($input->isDelete());
		$this->assertFalse($input->isGet());
		$this->assertFalse($input->isPost());
		$this->assertFalse($input->isPut());
		$this->assertEquals('delete', $input->getMethod());

		$this->assertEquals(array(), $input->getAll());
		$this->assertFalse($input->getAll('delete'));
	}

	/**
     * @test
	 * @return	null
	 */
	public function constructorManyParams()
	{
		$method = 'get';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 1234,
				'param3' => new StdClass()
			),
			'post' => array(
				'param4' => 'value-4',
				'param5' => 'value-5',
			),
			'route' => array(
				'param6' => 'value-6'
			)
		);
		$input = $this->createAppInput($method, $params);
		$this->assertEquals($params, $input->getAll());
		$this->assertEquals($params['get'], $input->getAll('get'));
		$this->assertEquals($params['post'], $input->getAll('post'));
		$this->assertEquals($params['route'], $input->getAll('route'));
	}

	/**
     * @test
	 * @return	null
	 */
	public function retrieveWitGet()
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertEquals('value1', $input->get('get', 'param1'));
		$this->assertEquals('value1', $input->get('Get', 'param1'));
		$this->assertEquals(12345, $input->get('get', 'param2'));
		$this->assertEquals('value3', $input->get('post', 'param3'));
	}

	/**
     * @test
	 * @dataProvider	provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function getKeyNotValid($key)
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertNull($input->get('get', $key));
		$this->assertEquals('default', $input->get('get', $key, 'default'));
	}

	/**
     * @test
	 * @dataProvider    provideInvalidStringsIncludeEmpty
	 * @return	null
	 */
	public function getParamTypeInvalid($type)
	{
		$method = 'post';
		$params = array(
			'get' => array(
				'param1' => 'value1',
				'param2' => 12345,
			),
			'post' => array(
				'param3' => 'value3'
			),
		);
		$input = $this->createAppInput($method, $params);

		$this->assertNull($input->get($type, 'param1'));
		$this->assertEquals('default', $input->get($type, 'param1', 'default'));
	}

	/**
     * @test
	 * @return	null
	 */
	public function collect()
	{
		$method = 'post';
		$params = array(
			'post' => array(
				'param1' => 'value1',
				'param2' => 12345,
				'param3' => 'value3',
				'param4' => 'value4',
				'param5' => 'value5'
			),
		);
		$input = $this->createAppInput($method, $params);
		$keys  = array('param1', 'param2', 'param5');
	
		$result = $input->collect('post', $keys);
		$class  = 'Appfuel\DataStructure\ArrayData';
		$this->assertInstanceOf($class, $result);
		$expected = array(
			'param1' => 'value1', 
			'param2' => 12345, 
			'param5' => 'value5'
		);
		$this->assertEquals($expected, $result->getAll());

		
		$result = $input->collect('post', $keys, true);
		$this->assertEquals($expected, $result);

		return $params;
	}

	/**
     * @test
	 * @depends	collect
	 * @return	null
	 */
	public function collectKeyNotFound(array $params)
	{
		$input = $this->createAppInput('post', $params);
		$keys  = array('param1', new StdClass(), true, 'paramXX', 'param5');
		$expected = array(
			'param1' => 'value1', 
			'param5' => 'value5'
		);
		$result = $input->collect('post', $keys, true);
		$this->assertEquals($expected, $result);
	}
}
