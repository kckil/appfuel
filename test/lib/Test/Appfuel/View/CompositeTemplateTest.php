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
namespace Test\Appfuel\View;

use Test\AfTestCase as ParentTestCase,
	Appfuel\View\CompositeTemplate,
	Appfuel\View\Template,
	StdClass,
	SplFileInfo;

/**
 * Callback function used for result filter test
 *
 * @param	string	$buildResult
 * @return  string
 */
function callbackFilter($buildResult)
{
	return 'filter data added ' . $buildResult;
}	


/**
 * Since a composit is a template that can hold other templates we will
 * be testing its ability to add remove get and build templates
 *
 */
class CompositeTemplateTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * Name of the template passed into the constructor
	 * @var string
	 */
	protected $templateName = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->templateName = 'my-template';
		$this->template = new CompositeTemplate($this->templateName);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->template);
	}

	/**
	 * @return null
	 */
	public function testGetAddRemoveExistsTemplate()
	{
		$key_1 = 'other-template';
		$this->assertFalse($this->template->templateExists($key_1));
		$this->assertFalse($this->template->getTemplate($key_1));

		$interface  = 'Appfuel\Framework\View\TemplateInterface';
		$template_1 = $this->getMockBuilder($interface)
						   ->disableOriginalConstructor()
						   ->getMock();

		$this->assertSame(
			$this->template,
			$this->template->addTemplate($key_1, $template_1),
			'must use a fluent interface'
		);

		$this->assertTrue($this->template->templateExists($key_1));
		$this->assertSame($template_1, $this->template->getTemplate($key_1));

		$key_2      = 'my-other-template';
		$template_2 = $this->getMockBuilder($interface)
						   ->disableOriginalConstructor()
						   ->getMock();

		$this->assertFalse($this->template->templateExists($key_2));
		$this->assertFalse($this->template->getTemplate($key_2));
		$this->template->addTemplate($key_2, $template_2);

		$this->assertTrue($this->template->templateExists($key_1));
		$this->assertTrue($this->template->templateExists($key_2));
		$this->assertSame($template_1, $this->template->getTemplate($key_1));
		$this->assertSame($template_2, $this->template->getTemplate($key_2));

		/* removing a template that does not exist ignores operation
		 * and acts as a fluent interface
		 */
		$this->assertFalse($this->template->templateExists('no-key'));
		$this->assertSame(
			$this->template,
			$this->template->removeTemplate('no-key')
		);

		/* removing a template that does exist also returns as a fluent
		 * interface
		 */
		$this->assertSame(
			$this->template,
			$this->template->removeTemplate($key_1)
		);
		$this->assertFalse($this->template->templateExists($key_1));
		$this->assertFalse($this->template->getTemplate($key_1));

		$this->assertSame(
			$this->template,
			$this->template->removeTemplate($key_2)
		);
		$this->assertFalse($this->template->templateExists($key_2));
		$this->assertFalse($this->template->getTemplate($key_2));
	}

	/**
	 * A build item is a value object used when building a template into 
	 * a string and assigning it to another template.
	 *
	 * @return null
	 */
	public function testCreateBuildItem()
	{
		$source = 'my-source';
		$target = 'my-target';
		$label  = 'my-assign-label';
		$item   = $this->template->createBuildItem($source, $target, $label);
		$this->assertInstanceOf(
			'Appfuel\View\BuildItem',
			$item
		);
		$this->assertEquals($source, $item->getSource());
		$this->assertEquals($target, $item->getTarget());
		$this->assertEquals($label,  $item->getAssignLabel());
	}

	/**
	 * @return null
	 */
	public function testAddBuildItemGetBuildItems()
	{
		$item_1 = $this->template->createBuildItem(
			'source_1',
			'target_1',
			'assign_label_1'
		);

		$this->assertSame(
			$this->template,
			$this->template->addBuildItem($item_1),
			'must be a fluent interface'
		);

		$expected = array($item_1);
		$this->assertEquals($expected, $this->template->getBuildItems());

		$item_2 = $this->template->createBuildItem(
			'source_2',
			'target_2',
			'assign_label_2'
		);
		$this->template->addBuildItem($item_2);

		$expected = array($item_1, $item_2);
		$this->assertEquals($expected, $this->template->getBuildItems());

		$item_3 = $this->template->createBuildItem(
			'source_3',
			'target_3',
			'assign_label_3'
		);
		$this->template->addBuildItem($item_3);

		$expected = array($item_1, $item_2, $item_3);
		$this->assertEquals($expected, $this->template->getBuildItems());
	}

	/**
	 * Build to allows you to assing one template to build into another 
	 * another template. It will create a build item and push that item 
	 * onto the stack where build will then use it to build the templates
	 *
	 * @return null
	 */
	public function testAssignBuild()
	{
		
		$interface	  = 'Appfuel\Framework\View\TemplateInterface';
		$sourceKey	  = 'source-key';
		$targetKey    = 'target-key';
		$assignLabel  = 'source-label';

		$this->assertSame(
			$this->template,
			$this->template->assignBuild($sourceKey, $assignLabel, $targetKey),
			'must use a fluent interface'
		);
		
		$result = $this->template->getBuildItems();
		$this->assertInternalType('array', $result);
		$this->assertArrayHasKey(0, $result);
		$this->assertEquals(1, count($result));

		$buildItem = $result[0];
		$this->assertInstanceOf(
			'Appfuel\Framework\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey, $buildItem->getSource());
		$this->assertEquals($targetKey, $buildItem->getTarget());
		$this->assertEquals($assignLabel, $buildItem->getAssignLabel());


		$sourceKey2	   = 'source-key2';
		$targetKey2    = 'target-key';
		$assignLabel2  = 'source-label2';

		$this->template->assignBuild($sourceKey2, $assignLabel2, $targetKey2);

		$result = $this->template->getBuildItems();
		$this->assertInternalType('array', $result);
		$this->assertEquals(2, count($result));
		$this->assertArrayHasKey(0, $result);
		$this->assertArrayHasKey(1, $result);

		/* make sure we can still get the other build item */
		$buildItem = $result[0];
		$this->assertInstanceOf(
			'Appfuel\Framework\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey, $buildItem->getSource());
		$this->assertEquals($targetKey, $buildItem->getTarget());
		$this->assertEquals($assignLabel, $buildItem->getAssignLabel());

		/* test the most recently added build item */
		$buildItem = $result[1];
		$this->assertInstanceOf(
			'Appfuel\Framework\View\BuildItemInterface',
			$buildItem
		);
		$this->assertEquals($sourceKey2, $buildItem->getSource());
		$this->assertEquals($targetKey2, $buildItem->getTarget());
		$this->assertEquals($assignLabel2, $buildItem->getAssignLabel());
	}

	/**
	 * When no assignment label is given the source key is used as the 
	 * assignment label
	 *
	 * @return null
	 */
	public function testBuildToDefaultAssignLabel()
	{
		$this->assertSame(
			$this->template,
			$this->template->assignBuild('my-source', null, 'my-target'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			$buildItem->getSource(), 
			$buildItem->getAssignLabel()
		);
	}

	/**
	 * When no target is specified a special keyword _this_ is used to 
	 * indicate the target is the current template.
	 *
	 * @return null
	 */
	public function testBuildToDefaultTarget()
	{
		$this->assertSame(
			$this->template,
			$this->template->assignBuild('my-source', 'my-label'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			'_this_', 
			$buildItem->getTarget()
		);

	}

	/**
	 * The label should be the same as source and the target sould be 
	 * the keyword _this_
	 *
	 * @return null
	 */
	public function testBuildToDefaultAssignLabelAndTarget()
	{
		$this->assertSame(
			$this->template,
			$this->template->assignBuild('my-source'),
			'always uses a fluent interface'
		);

		$result = $this->template->getBuildItems();
		$buildItem = $result[0];

		/* prove assignment label is the same as the source */
		$this->assertEquals(
			'_this_', 
			$buildItem->getTarget()
		);

		$this->assertEquals(
			$buildItem->getSource(),
			$buildItem->getAssignLabel(),
			'assignment label must be the same as the source key'
		);
	}

	/**
	 * AssignTo allows you to assign a name/value pair into any template that
	 * you are holding
	 *
	 * @return null
	 */
	public function testAssignTo()
	{
		$templateA = new Template('templateA');
		$templateB = new Template('templateB');

		$this->template->addTemplate('templateA', $templateA)
					   ->addTemplate('templateB', $templateB);

		$this->assertSame(
			$this->template,
			$this->template->assignTo('templateA', 'foo', 'bar'),
			'must use a fluent interface'
		);

		/* prove only templateA recieves the assignment for foo=>bar */
		$this->assertEquals('bar', $templateA->get('foo'));
		$this->assertNull($templateB->get('foo'));
		$this->assertNull($this->template->get('foo'));

		$this->assertSame(
			$this->template,
			$this->template->assignTo('templateB', 'baz', 'biz'),
			'must use a fluent interface'
		);
		$this->assertEquals('biz', $templateB->get('baz'));
		$this->assertNull($templateA->get('baz'));
		$this->assertNull($this->template->get('baz'));

		/* when the template does not exist the request is ignored */
		$this->assertSame(
			$this->template,
			$this->template->assignTo('no-template', 'no-label', 'no-value'),
			'should still be a fluent interface'
		);
	
		$this->assertNull($this->template->get('no-label'));
		$this->assertNull($templateA->get('no-label'));
		$this->assertNull($templateB->get('no-label'));
	}

	/**
	 * Allows you to store info used in a callback or an anonymous function
	 * to use to filter the results of the build. This method is used
	 * in a fluent interface after the builTo.
	 *
	 * @return null
	 */
	public function testFilterResultsWithString()
	{
		$this->template->assignBuild('my-source', 'my-label', 'my-target');
		
		$callback = 'my-callback-function';
		$this->template->filterResultsWith($callback);

		$results = $this->template->getBuildItems();
		$buildItem = $results[0];

		$this->assertEquals($callback, $buildItem->getResultFilter());
	}

	/**
	 * You can also describe a callback with an array where the first param
	 * is the object and the second is the method
	 * 
	 * @return	null
	 */
	public function testFilterResultsWithArray()
	{
		$this->template->assignBuild('my-source', 'my-label', 'my-target');
		
		$callback = array(new StdClass(), 'my-callback-function');
		$this->template->filterResultsWith($callback);

		$results = $this->template->getBuildItems();
		$buildItem = $results[0];

		$this->assertEquals($callback, $buildItem->getResultFilter());
	}

	/**
	 * You can also save a closure as a result filter
	 *
	 * @return null
	 */
	public function testFilterResultsWithClosure()
	{
		$this->template->assignBuild('my-source', 'my-label', 'my-target');
		
		$callback = function ($resultString) {
			return trim($resultString);	
		};

		$this->template->filterResultsWith($callback);

		$results = $this->template->getBuildItems();
		$buildItem = $results[0];

		$this->assertEquals($callback, $buildItem->getResultFilter());
	}

	/**
	 * Using filterResultsWith before buildTo is invalid and will result in
	 * an exception being thrown. Reason for this is build to actually creates
	 * the current build item giving it the required parameters. The filter
	 * is an optional parameter so it has to be used after buildTo.
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testFilterResultsInvalidUsage()
	{
		$callback = 'my-callback-function';
		$this->template->filterResultsWith($callback);
	}

	/**
	 * letBuildFailSilently tell build to ignore the build when the template
	 * can not be found
	 *
	 * @return null
	 */
	public function testLetBuildFailSilently()
	{
		$this->template->assignBuild('my-source', 'my-label', 'my-target');
		$this->assertSame(
			$this->template,
			$this->template->letBuildFailSilently(),
			'uses fluent interface'
		);

		$results = $this->template->getBuildItems();
		$buildItem = $results[0];

		$this->assertTrue($buildItem->isSilentFail());
	}

	/**
	 * Must use after assignBuild
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testLetBuildFailSilentlyInvalidUsage()
	{
		$this->template->letBuildFailSilently();
	}


	/**
	 * letBuildFailSilently tell build to fail hard and throw an exception
	 * when the template can not be found
	 *
	 * @return null
	 */
	public function testLetBuildThrowException()
	{
		$this->template->assignBuild('my-source', 'my-label', 'my-target');
		$this->assertSame(
			$this->template,
			$this->template->letBuildThrowException(),
			'uses fluent interface'
		);

		$results = $this->template->getBuildItems();
		$buildItem = $results[0];

		$this->assertFalse($buildItem->isSilentFail());
	}

	/**
	 * Must use after assignBuild
	 * 
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testLetBuildThrowExceptionInvalidUsage()
	{
		$this->template->letBuildThrowException();
	}
	
	/**
	 * Creates a SplFileInfo that points to a file in the files directory 
	 * located in the directory of this test
	 *
	 * @param	string	$path
	 * @return	SplFileInfo
	 */
	public function createFile($path)
	{
		$relpath = 'files' . DIRECTORY_SEPARATOR . $path;
		return new SplFileInfo($this->getCurrentPath($relpath));
	}

	/**
	 * We set the file to a template in the files directory with a simple known
	 * output and made no assignments with no other templates to build. The
	 * build result should be the same as the template
	 *
	 * @return null
	 */
	public function testBuildNoTemplates()
	{
		$file = $this->createFile('template.phtml');
		$this->template->setFile($file);

		$expected = 'This is a test template. Foo=baz. EOF.';
		$this->assertEquals($expected, $this->template->build());
	}

	public function loadTemplateABCD()
	{
		$file = $this->createFile('template_main.phtml');
		$this->template->setFile($file);

		$fileA = $this->createFile('template_a.phtml');
		$fileB = $this->createFile('template_b.phtml');
		$fileC = $this->createFile('template_c.phtml');
		$fileD = $this->createFile('template_d.phtml');

		$templateA = new Template($fileA);
		$templateB = new Template($fileB);
		$templateC = new Template($fileC);
		$templateD = new Template($fileD);
	
		$this->template->addTemplate('a', $templateA)
					   ->addTemplate('b', $templateB)
					   ->addTemplate('c', $templateC)
					   ->addTemplate('d', $templateD);
	}

	/**
	 * We created 5 very simple templates in order to test building templates
	 * into other templates. Each template contains its name followed by a 
	 * common. The main template expects template d, template d expects 
	 * template c, template c expects template b, template b expects template a
	 * and template a does not expect anything. The result is a comma separated
	 * list in reverse order starting from the main template. This is complex
	 * enough to test the building of each template and assigning it into 
	 * another. 
	 *
	 * @return	null
	 */
	public function testBuildManyTemplates()
	{
		$this->loadTemplateABCD();

		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

		$expected = 'main template, template d, template c,' .
					' template b, template a';

		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * Template a and b results will not show because template b does not exist
	 * Please note that silent fail is the default behavior and does not need
	 * to be specified
	 *
	 * @return null
	 */
	public function testTemplateDoesNotExistSilentFail()
	{
		$this->loadTemplateABCD();
		
		$this->template->removeTemplate('b');

		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->letBuildFailSilently()
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

        $expected = 'main template, template d, template c,';
		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * Template a and b results will not show because template b does not exist
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testTemplateDoesNotExistThrowException()
	{
		$this->loadTemplateABCD();

		$this->template->removeTemplate('b');
		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->letBuildThrowException()
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

        $expected = 'main template, template d, template c,';
		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * Test the ability to filter the results with a closure
	 *
	 * @return null
	 */
	public function testBuildManyTemplatesFilterResultsClosure()
	{
		$this->loadTemplateABCD();

		$filter = function($string) {
			return 'filter data added ' . $string;
		};
		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->filterResultsWith($filter)
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

		$expected = 'main template, template d, template c,' .
					' filter data added template b, template a';

		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * Test the ability to filter the results with a callback
	 *
	 * @return null
	 */
	public function testBuildManyTemplatesFilterResultsCallbackString()
	{
		$this->loadTemplateABCD();

		$callback = __NAMESPACE__ . '\\callbackFilter';

		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->filterResultsWith($callback)
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

		$expected = 'main template, template d, template c,' .
					' filter data added template b, template a';

		$this->assertEquals($expected, $this->template->build());
	}

	/**
	 * Callback function used for result filter test
	 *
	 * @param	string	$buildResult
	 * @return  string
	 */
	public function callbackFilter($buildResult)
	{
		return 'filter data added ' . $buildResult;
	}	


	/**
	 * Test the ability to filter the results with a callback
	 * in an object method
	 *
	 * @return null
	 */
	public function testBuildManyTemplatesFilterResultsCallbackObjectMethod()
	{
		$this->loadTemplateABCD();

		$callback = array($this, 'callbackFilter');

		$this->template->assignBuild('a', 'a_in_b', 'b')
					   ->assignBuild('b', 'b_in_c', 'c')
					   ->filterResultsWith($callback)
					   ->assignBuild('c', 'c_in_d', 'd')
					   ->assignBuild('d', 'd_in_this', '_this_');

		$expected = 'main template, template d, template c,' .
					' filter data added template b, template a';

		$this->assertEquals($expected, $this->template->build());
	}
}
