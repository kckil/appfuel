<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Framework\App;

use PHPUnit_Framework_TestCase		as ParentTestCase,
	Appfuel\Framework\App\Factory	as AppFactory;

/**
 * 
 */
class FactoryTest extends ParentTestCase
{
	/**
	 * System Under Test
	 * @var Appfuel\Framework\App\Factory
	 */
	protected $factory = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->basePath = AF_BASE_PATH;
		$this->factory  = new AppFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->factory);
	}

	/**
	 * @return NULL
	 */
	public function testCreateInitializer()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\App\InitializeInterface',
			$this->factory->createInitializer('/some/base/path')
		);
	}

	/**
	 * @return NULL
	 */
	public function testCreatePhpError()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\App\PHPErrorInterface',
			$this->factory->createPHPError()
		);
	}

	/**
	 * @return NULL
	 */
	public function testCreateAutoloader()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Autoload\AutoloadInterface',
			$this->factory->createAutoloader()
		);
	}
}

