<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Validate;

use StdClass,
	Appfuel\Validate\FilterSpec,
	Appfuel\DataStructure\Dictionary,
	Testfuel\TestCase\BaseTestCase;

class FilterSpecTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidStringsWithEmpty()
	{
		$result = $this->provideInvalidStrings();
		$result[] = array('');
		return $result;
	}

	/**
	 * @return	array	
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(12345),
			array(1.234),
			array(0),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function createFilterSpec(array $data)
	{
		return new FilterSpec($data);
	}

	/**
	 * @test
	 * @return	FieldSpec
	 */
	public function minimal()
	{
		$data = array(
			'name'  => 'id',
			
		);
		$spec = $this->createFilterSpec($data);
		$this->assertInstanceOf('Appfuel\Validate\FilterSpecInterface', $spec);
		$this->assertEquals($data['name'], $spec->getName());
		$this->assertNull($spec->getError());
		$this->assertInstanceOf(
			'Appfuel\DataStructure\DictionaryInterface',
			$spec->getParams()
		);
		return $data;
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function params(array $data)
	{
		$data['params'] = array(
			'param-a' => 'value-a',
			'param-b' => 'value-b',
			'param-c' => 'value-c' 
		);
		$spec = $this->createFilterSpec($data);

		$expected = new Dictionary($data['params']);
		$this->assertEquals($expected, $spec->getParams());
	}

	/**
	 * @test
	 * @depends	minimal
	 * @return	null
	 */
	public function error(array $data)
	{
		$data['error'] = 'some error message';
		$spec = $this->createFilterSpec($data);
		$this->assertEquals($data['error'], $spec->getError());
	}

	/**
	 * @test
	 * @return	null
	 */
	public function noNameFailure()
	{
		$data = array(
			'params' => array('a', 'b', 'c'),
			'error'  => 'my error'	
		);
		$msg = 'filter name must be defined with key -(name)';
		$this->setExpectedException('DomainException', $msg);
		$spec = $this->createFilterSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStringsWithEmpty
	 * @return	null
	 */
	public function invalidFieldFailure($field)
	{
		$data = array(
			'name'  => $field,
			'params' => array('a', 'b', 'c'),
		);
		$msg  = 'filter name must be a non empty string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFilterSpec($data);
	}

	/**
	 * @test
	 * @dataProvider	provideInvalidStrings
	 * @return	null
	 */
	public function invalidErrorFailure($error)
	{
		$data = array(
			'name'    => 'my-filter',
			'params' => array('a', 'b', 'c'),
			'error'  => $error
			
		);
		$msg  = 'error message must be a string';
		$this->setExpectedException('InvalidArgumentException', $msg);
		$spec = $this->createFilterSpec($data);
	}
}
