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
namespace TestFuel\Unit\View\Html;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Element\Script,
	Appfuel\View\Html\HtmlDocTemplate;

/**
 */
class HtmlDocTemplate_JsTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlDocTemplate
	 */
	protected $htmlDoc = null;

    /**
     * Path to template file 
     * @var string
     */
    protected $templatePath = null;

	/**
	 * @var string
	 */
	protected $tagInterface = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->tagInterface = 'Appfuel\View\Html\Element\HtmlTagInterface';
		$this->htmlDoc = new HtmlDocTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return	null
	 */
	public function testIsJsEnableDisable()
	{
		$this->assertTrue($this->htmlDoc->isJsEnabled());

		$this->assertSame($this->htmlDoc, $this->htmlDoc->disableJs());
		$this->assertFalse($this->htmlDoc->isJsEnabled());
		
		$this->assertSame($this->htmlDoc, $this->htmlDoc->enableJs());
		$this->assertTrue($this->htmlDoc->isJsEnabled());
	}

	/**
	 * @return	null
	 */
	public function testGetSetJsHeadScriptTag()
	{
		/* default value is an empty array */
		$this->assertEquals(array(), $this->htmlDoc->getJsHeadScriptTags());

		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$src = 'jsfile.js';	
		$this->assertFalse($this->htmlDoc->isJsHeadScript($src));
		
		$tag->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue($src));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadScriptTag($tag)
		);
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src));

		$expected = array($tag);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());

		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$src2 = 'jsfile_2.js';	
		$this->assertFalse($this->htmlDoc->isJsHeadScript($src2));
		
		$tag2->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue($src2));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadScriptTag($tag2)
		);
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src2));

		$expected = array($tag, $tag2);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testGetSetJsBodyScriptTag()
	{
		/* default value is an empty array */
		$this->assertEquals(array(), $this->htmlDoc->getJsBodyScriptTags());

		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$src = 'jsfile.js';	
		$this->assertFalse($this->htmlDoc->isJsBodyScript($src));
		
		$tag->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue($src));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyScriptTag($tag)
		);
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src));

		$expected = array($tag);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());

		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$src2 = 'jsfile_2.js';	
		$this->assertFalse($this->htmlDoc->isJsBodyScript($src2));
		
		$tag2->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue($src2));

		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyScriptTag($tag2)
		);
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src2));

		$expected = array($tag, $tag2);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsHeadScriptTagNotScript_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('link'));

		$this->htmlDoc->addJsHeadScriptTag($tag);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsBodyScriptTagNotScript_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('link'));

		$this->htmlDoc->addJsBodyScriptTag($tag);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsHeadScriptTagEmptySrc_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$tag->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue(''));


		$this->htmlDoc->addJsHeadScriptTag($tag);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsBodyScriptTagEmptySrc_Failure()
	{
		$tag = $this->getMock($this->tagInterface);
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('script'));

		$tag->expects($this->once())
			->method('getAttribute')
			->with($this->equalTo('src'))
			->will($this->returnValue(''));


		$this->htmlDoc->addJsBodyScriptTag($tag);
	}

	/**
	 * @return	null
	 */
	public function testAddJsHeadScriptTagsNoDuplicates()
	{
		$src = 'my-file.js';
		$tag1 = new Script();
		$tag1->addAttribute('src', $src);

		$tag2 = new Script();
		$tag2->addAttribute('src', $src);

		$this->assertFalse($this->htmlDoc->isJsHeadScript($src));
		$this->htmlDoc->addJsHeadScriptTag($tag1)
					  ->addJsHeadScriptTag($tag2);

		$this->assertTrue($this->htmlDoc->isJsHeadScript($src));

		$expected = array($tag2);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testAddJsBodyScriptTagsNoDuplicates()
	{
		$src = 'my-file.js';
		$tag1 = new Script();
		$tag1->addAttribute('src', $src);

		$tag2 = new Script();
		$tag2->addAttribute('src', $src);

		$this->assertFalse($this->htmlDoc->isJsBodyScript($src));
		$this->htmlDoc->addJsBodyScriptTag($tag1)
					  ->addJsBodyScriptTag($tag2);

		$this->assertTrue($this->htmlDoc->isJsBodyScript($src));

		$expected = array($tag2);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testLoadJsHeadScriptTags()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = new Script('your-file.js');
		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsHeadScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testLoadJsBodyScriptTags()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = new Script('your-file.js');
		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsBodyScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsHeadScriptTags_ItemNotScriptFailure()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('link'));


		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsHeadScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsBodyScriptTags_ItemNotScriptFailure()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('link'));


		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsBodyScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsBodyScriptTags());
	}


	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsHeadScriptTags_ItemEmptySrcFailure()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('script'));

		$tag2->expects($this->once())
			 ->method('getAttribute')
			 ->with($this->equalTo('src'))
			 ->will($this->returnValue(''));

		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsHeadScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsBodyScriptTags_ItemEmptySrcFailure()
	{
		$tag1 = new Script('my-file.js');
		$tag2 = $this->getMock($this->tagInterface);
		$tag2->expects($this->once())
			 ->method('getTagName')
			 ->will($this->returnValue('script'));

		$tag2->expects($this->once())
			 ->method('getAttribute')
			 ->with($this->equalTo('src'))
			 ->will($this->returnValue(''));

		$tag3 = new Script('there-file.js');
		$list = array($tag1, $tag2, $tag3);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsBodyScriptTags($list)
		);

		$this->assertEquals($list, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testAddJsHeadFile()
	{
		$src1 = 'my-file.js';
		$this->assertFalse($this->htmlDoc->isJsHeadScript($src1));
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadFile($src1)
		);
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src1));

		$file1 = new Script($src1);
		$expected = array($file1);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());

		$src2 = 'your-file.js';
		$this->assertFalse($this->htmlDoc->isJsHeadScript($src2));
	
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadFile($src2)
		);

		$this->assertTrue($this->htmlDoc->isJsHeadScript($src1));
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src2));

		$file2 = new Script($src2);
		$expected = array($file1, $file2);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());

		$src3 = 'there-file.js';
		$this->assertFalse($this->htmlDoc->isJsHeadScript($src3));
	
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadFile($src3)
		);

		$this->assertTrue($this->htmlDoc->isJsHeadScript($src1));
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src2));
		$this->assertTrue($this->htmlDoc->isJsHeadScript($src3));

		$file3 = new Script($src3);
		$expected = array($file1, $file2, $file3);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testAddJsBodyFile()
	{
		$src1 = 'my-file.js';
		$this->assertFalse($this->htmlDoc->isJsBodyScript($src1));
		
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyFile($src1)
		);
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src1));

		$file1 = new Script($src1);
		$expected = array($file1);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());

		$src2 = 'your-file.js';
		$this->assertFalse($this->htmlDoc->isJsBodyScript($src2));
	
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyFile($src2)
		);

		$this->assertTrue($this->htmlDoc->isJsBodyScript($src1));
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src2));

		$file2 = new Script($src2);
		$expected = array($file1, $file2);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());

		$src3 = 'there-file.js';
		$this->assertFalse($this->htmlDoc->isJsBodyScript($src3));
	
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyFile($src3)
		);

		$this->assertTrue($this->htmlDoc->isJsBodyScript($src1));
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src2));
		$this->assertTrue($this->htmlDoc->isJsBodyScript($src3));

		$file3 = new Script($src3);
		$expected = array($file1, $file2, $file3);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsHeadFileEmptyString_Failure()
	{
		$this->htmlDoc->addJsHeadFile('');
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsBodyFileEmptyString_Failure()
	{
		$this->htmlDoc->addJsBodyFile('');
	}

	/**
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsHeadFileNonStrings_Failure($src)
	{
		$this->htmlDoc->addJsHeadFile($src);
	}

	/**
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testAddJsBodyFileNonStrings_Failure($src)
	{
		$this->htmlDoc->addJsBodyFile($src);
	}

	/**
	 * @return	null
	 */
	public function testLoadJsHeadFiles()
	{
		$list = array(
			'my-file.js',
			'your-file.js',
			'there-file.js'
		);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsHeadFiles($list)
		);

		$expected = array(
			new Script($list[0]),
			new Script($list[1]),
			new Script($list[2])
		);
		$this->assertEquals($expected, $this->htmlDoc->getJsHeadScriptTags());
	}

	/**
	 * @return	null
	 */
	public function testLoadJsBodyFiles()
	{
		$list = array(
			'my-file.js',
			'your-file.js',
			'there-file.js'
		);
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->loadJsBodyFiles($list)
		);

		$expected = array(
			new Script($list[0]),
			new Script($list[1]),
			new Script($list[2])
		);
		$this->assertEquals($expected, $this->htmlDoc->getJsBodyScriptTags());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsHeadFilesEmptySrc_Failure()
	{
		$list = array(
			'my-file.js',
			'',
			'there-file.js'
		);
		$this->htmlDoc->loadJsHeadFiles($list);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsBodyFilesEmptySrc_Failure()
	{
		$list = array(
			'my-file.js',
			'',
			'there-file.js'
		);
		$this->htmlDoc->loadJsBodyFiles($list);
	}

	/**
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsHeadFilesNotStringSrc_Failure($src)
	{
		$list = array(
			'my-file.js',
			$src,
			'there-file.js'
		);
		$this->htmlDoc->addJsHeadFile($src);
	}

	/**
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testLoadJsBodyFilesNotStringSrc_Failure($src)
	{
		$list = array(
			'my-file.js',
			$src,
			'there-file.js'
		);
		$this->htmlDoc->addJsBodyFile($src);
	}

	/**
	 * @return	null
	 */
	public function testDefaultJsHeadInlineScriptTag()
	{
		$script = $this->htmlDoc->getJsHeadInlineScriptTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Script',
			$script
		);
	
		$this->assertEmpty($script->buildContent());	
	}

	/**
	 * @return	null
	 */
	public function testDefaultJsBodyInlineScriptTag()
	{
		$script = $this->htmlDoc->getJsBodyInlineScriptTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Element\Script',
			$script
		);
	
		$this->assertEmpty($script->buildContent());	
	}

	/**
	 * @depends	testDefaultJsHeadInlineScriptTag
	 * @return	null
	 */
	public function testGetSetJsHeadInlineScriptTag()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));
		
		$script->expects($this->once())
			   ->method('getAttribute')
			   ->with($this->equalTo('src'))
			   ->will($this->returnValue(''));


		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setJsHeadInlineScriptTag($script)
		);

		$this->assertSame($script, $this->htmlDoc->getJsHeadInlineScriptTag());
	}

	/**
	 * @depends	testDefaultJsHeadInlineScriptTag
	 * @return	null
	 */
	public function testGetSetJsBodyInlineScriptTag()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));
		
		$script->expects($this->once())
			   ->method('getAttribute')
			   ->with($this->equalTo('src'))
			   ->will($this->returnValue(''));


		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->setJsBodyInlineScriptTag($script)
		);

		$this->assertSame($script, $this->htmlDoc->getJsBodyInlineScriptTag());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testDefaultJsHeadInlineScriptTag
	 * @return				null
	 */
	public function testSetJsHeadInlineScriptNotScript_Failure()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('img'));
	
		$this->htmlDoc->setJsHeadInlineScriptTag($script);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testDefaultJsBodyInlineScriptTag
	 * @return				null
	 */
	public function testSetJsBodyInlineScriptNotScript_Failure()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('img'));
	
		$this->htmlDoc->setJsBodyInlineScriptTag($script);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testDefaultJsHeadInlineScriptTag
	 * @return				null
	 */
	public function testSetJsHeadInlineScriptSrcNotEmpty_Failure()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));
	
		$script->expects($this->once())
			   ->method('getAttribute')
			   ->with($this->equalTo('src'))
			   ->will($this->returnValue('my-js.js'));


		$this->htmlDoc->setJsHeadInlineScriptTag($script);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testDefaultJsBodyInlineScriptTag
	 * @return				null
	 */
	public function testSetJsBodyInlineScriptSrcNotEmpty_Failure()
	{
		$script = $this->getMock($this->tagInterface);
		$script->expects($this->once())
			   ->method('getTagName')
			   ->will($this->returnValue('script'));
	
		$script->expects($this->once())
			   ->method('getAttribute')
			   ->with($this->equalTo('src'))
			   ->will($this->returnValue('my-js.js'));


		$this->htmlDoc->setJsBodyInlineScriptTag($script);
	}

	/**
	 * @depends				testDefaultJsHeadInlineScriptTag
	 * @return				null
	 */
	public function testAddJsHeadInlineContent()
	{
		$script = $this->htmlDoc->getJsHeadInlineScriptTag();
		$content1 = 'alert("i am content 1");';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadInlineContent($content1)
		);
		$expected = array($content1);
		$this->assertEquals($expected, $script->getContent());
		$this->assertEquals(
			$content1, 
			$this->htmlDoc->getJsHeadInlineContent()
		);

		/* when the isArray parameter is true it will return the 
		 * content as an array of items that were entered
		 */
		$this->assertEquals(
			$expected, 
			$this->htmlDoc->getJsHeadInlineContent(true)
		);


		$content2 = 'alert("i am content 2");';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsHeadInlineContent($content2)
		);

		$expected = array($content1, $content2);
		$this->assertEquals($expected, $script->getContent());
		$this->assertEquals(
			$script->buildContent(), 
			$this->htmlDoc->getJsHeadInlineContent()
		);
		$this->assertEquals(
			$expected, 
			$this->htmlDoc->getJsHeadInlineContent(true)
		);
	}

	/**
	 * @depends				testDefaultJsBodyInlineScriptTag
	 * @return				null
	 */
	public function testAddJsBodyInlineContent()
	{
		$script = $this->htmlDoc->getJsBodyInlineScriptTag();
		$content1 = 'alert("i am content 1");';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyInlineContent($content1)
		);
		$expected = array($content1);
		$this->assertEquals($expected, $script->getContent());
		$this->assertEquals(
			$content1, 
			$this->htmlDoc->getJsBodyInlineContent()
		);

		/* when the isArray parameter is true it will return the 
		 * content as an array of items that were entered
		 */
		$this->assertEquals(
			$expected, 
			$this->htmlDoc->getJsBodyInlineContent(true)
		);


		$content2 = 'alert("i am content 2");';
		$this->assertSame(
			$this->htmlDoc,
			$this->htmlDoc->addJsBodyInlineContent($content2)
		);

		$expected = array($content1, $content2);
		$this->assertEquals($expected, $script->getContent());
		$this->assertEquals(
			$script->buildContent(), 
			$this->htmlDoc->getJsBodyInlineContent()
		);
		$this->assertEquals(
			$expected, 
			$this->htmlDoc->getJsBodyInlineContent(true)
		);
	}
}
