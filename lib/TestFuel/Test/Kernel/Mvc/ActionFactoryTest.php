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
namespace TestFuel\Test\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcActionFactory;

/**
 * The action factory is reponsible for creating any of the objects in the
 * action controller namespace
 */
class ActionFactoryTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var AppContext
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->factory = new MvcActionFactory();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->factory = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionFactoryInterface',
			$this->factory
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetActionClass()
	{
		$this->assertEquals(
			'ActionController', 
			$this->factory->getActionClass()
		);
		
		$name = 'MyName';
		$this->assertSame(
			$this->factory,
			$this->factory->setActionClass($name)
		);
		$this->assertEquals($name, $this->factory->getActionClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testSetActionClassName_FailureInvalidString($name)
	{
		$this->factory->setActionClass($name);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testConstructor_FailureInvalidString($name)
	{
		$factory = new MvcActionFactory($name);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateMvcAction()
	{
		$namespace = 'TestFuel\Fake\Action\User\Create';
		$action    = $this->factory->createMvcAction($namespace);
		$this->assertInstanceOf(
			"$namespace\\ActionController",
			$action
		);
		$this->assertInstanceof(
			"Appfuel\Kernel\Mvc\MvcAction",
			$action
		);
		$this->assertInstanceof(
			"Appfuel\Kernel\Mvc\MvcActionInterface",
			$action
		);

		$namespace = 'TestFuel\Fake\Action\User\Delete';
		$action    = $this->factory->createMvcAction($namespace);
		$this->assertInstanceOf(
			"$namespace\\ActionController",
			$action
		);
		$this->assertInstanceof(
			"Appfuel\Kernel\Mvc\MvcAction",
			$action
		);
		$this->assertInstanceof(
			"Appfuel\Kernel\Mvc\MvcActionInterface",
			$action
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateConsoleViewNoNamespace()
	{
		$view = $this->factory->createConsoleView();
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);

	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateConsoleViewNamespace()
	{
		$ns   = 'TestFuel\Fake\Action\User\Create';
		$view = $this->factory->createConsoleView($ns);
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);
	}

	/**
	 * When no view namespace exists the factory iterprets that to mean you
	 * will you the default appfuel console view
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateConsoleViewNamespaceDoesNotExist()
	{
		$ns   = 'TestFuel\Fake\Action\User\Create\DoesNotExist';
		$view = $this->factory->createConsoleView($ns);
		$this->assertInstanceOf(
			'Appfuel\Console\ConsoleViewTemplate',
			$view
		);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testCreateConsoleViewInvalidInterface()
	{
		$ns   = 'TestFuel\Fake\Action\User\BadViews';
		$view = $this->factory->createConsoleView($ns);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateAjaxViewNoNamespace()
	{
		$view = $this->factory->createAjaxView();
		$this->assertInstanceOf(
			'Appfuel\View\JsonTemplate',
			$view
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateAjaxViewNamespace()
	{
		$ns   = 'TestFuel\Fake\Action\User\Create';
		$view = $this->factory->createAjaxView($ns);
		$this->assertInstanceOf("$ns\AjaxView",$view);
	}

	/**
	 * When no view namespace exists the factory iterprets that to mean you
	 * will you the default appfuel json view
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testCreateAjaxViewNamespaceDoesNotExist()
	{
		$ns   = 'TestFuel\Fake\Action\User\Create\DoesNotExist';
		$view = $this->factory->createAjaxView($ns);
		$this->assertInstanceOf(
			'Appfuel\View\JsonTemplate',
			$view
		);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testCreateAjaxViewInvalidInterface()
	{
		$ns   = 'TestFuel\Fake\Action\User\BadViews';
		$view = $this->factory->createAjaxView($ns);
	}
}
