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
	Appfuel\View\ViewTemplate,
	Appfuel\View\Html\HtmlPage,
	Appfuel\View\Html\Tag\LinkTag,
	Appfuel\View\Html\Tag\StyleTag,
	Appfuel\View\Html\Tag\ScriptTag,
	Appfuel\View\Html\Tag\HtmlTag,
	Appfuel\View\Html\Tag\MetaTag,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HtmlPageTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlPage
	 */
	protected $page = null;

	/**
	 * Content view of the html page. This gets turned into a string and
	 * added as the first content item of the body
	 *
	 * @var	 ViewTemplate
	 */
	protected $view = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->view = new ViewTemplate();
		$this->page = new HtmlPage($this->view);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageInterface',
			$this->page
		);
		$this->assertInstanceOf(
			'Appfuel\View\ViewInterface',
			$this->page
		);

		$this->assertSame($this->view, $this->page->getView());
		
		$htmlTag = $this->page->getHtmlTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface',
			$htmlTag
		);

		$htmlHead = $this->page->getHtmlHead();
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlHeadInterface',
			$htmlHead
		);

		$htmlBody = $this->page->getHtmlBody();
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlBodyInterface',
			$htmlBody
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetHtmlTag()
	{
		$tag = $this->getMock('Appfuel\View\Html\Tag\HtmlTagInterface');
		$tag->expects($this->once())
			->method('getTagName')
			->will($this->returnValue('html'));

		$this->assertSame($this->page, $this->page->setHtmlTag($tag));
		$this->assertSame($tag, $this->page->getHtmlTag());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetHtmlBody()
	{
		$body = $this->getMock('Appfuel\View\Html\HtmlBodyInterface');
		$this->assertSame($this->page, $this->page->setHtmlBody($body));
		$this->assertSame($body, $this->page->getHtmlBody());
	}

	/**
	 * @depends	testInitialState
	 * @return	null	
	 */
	public function testAddGetIsHtmlAttribute()
	{
		$html = $this->page->getHtmlTag();

		$this->assertFalse($html->isAttribute('manifest'));
		$this->assertNull($html->getAttribute('manifest'));
		
		$this->assertSame(
			$this->page,
			$this->page->addHtmlAttribute('manifest', 'mycache.file')
		);
		$this->assertTrue($html->isAttribute('manifest'));
		$this->assertEquals(
			'mycache.file',
			$html->getAttribute('manifest')
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null	
	 */
	public function testAddHeadttribute()
	{
		$head = $this->page->getHtmlHead();

		$this->assertFalse($head->isAttribute('id'));
		$this->assertNull($head->getAttribute('id'));
		
		$this->assertSame(
			$this->page,
			$this->page->addHeadAttribute('id', 'my-id')
		);
		$this->assertTrue($head->isAttribute('id'));
		$this->assertEquals('my-id', $head->getAttribute('id'));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetHeadTitle()
	{
		$title = $this->page->getHtmlHead()
							->getHeadTag()
							->getTitle();
		$text = "my-title";
		$this->assertSame($this->page, $this->page->setHeadTitle($text));
		$this->assertEquals($text, $title->getContent(0));
	
		$text2 = "my-other-title";
		$this->assertSame(
			$this->page, 
			$this->page->setHeadTitle($text2, 'append')
		);
		$this->assertEquals($text2, $title->getContent(1));
		
		$text3 = "my-other-other-title";
		$this->assertSame(
			$this->page, 
			$this->page->setHeadTitle($text3, 'prepend')
		);
		$this->assertEquals($text3, $title->getContent(0));
		$this->assertEquals($text, $title->getContent(1));
		$this->assertEquals($text2, $title->getContent(2));

	
		$this->assertSame(
			$this->page, 
			$this->page->setHeadTitle($text2, 'replace')
		);
		$this->assertEquals($text2, $title->getContent(0));
		$this->assertFalse($title->getContent(1));
		$this->assertFalse($title->getContent(2));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetHeadBase()
	{
		$head = $this->page->getHtmlHead();

		$this->assertNull($head->getBaseTag());
		$this->assertSame($this->page, $this->page->setHeadBase('myhref'));
		$base = $head->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\BaseTag',
			$base
		);
		$this->assertEquals('myhref', $base->getAttribute('href'));

		$this->assertSame(
			$this->page, 
			$this->page->setHeadBase(null, 'mytarget')
		);

		$base = $head->getBaseTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\BaseTag',
			$base
		);
		$this->assertEquals('mytarget', $base->getAttribute('target'));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetHeadMeta()
	{
		$head = $this->page->getHtmlHead();
		$this->assertEquals(array(), $head->getMetaTags());
		$this->assertSame(
			$this->page, 
			$this->page->addHeadMeta('auther', 'robert')
		);
		$list = $head->getMetaTags();
		$tag = new MetaTag('auther', 'robert');
		$this->assertEquals($tag, $list[0]);
		
		$this->assertSame(
			$this->page, 
			$this->page->addHeadMeta(null, null, null, 'UTF-8')
		);

		$list = $head->getMetaTags();
		$tag = new MetaTag(null, null, null, 'UTF-8');
		$this->assertEquals($tag, $list[1]);

		$this->assertSame(
			$this->page, 
			$this->page->addHeadMeta(null,'text/html','Content-Type','UTF-8')
		);

		$list = $head->getMetaTags();
		$tag = new MetaTag(null, 'text/html', 'Content-Type', 'UTF-8');
		$this->assertEquals($tag, $list[2]);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadMetaTag()
	{
		$head = $this->page->getHtmlHead();
		$this->assertEquals(array(), $head->getMetaTags());

		$tag1 = new MetaTag('auther', 'robert');
		$this->assertSame(
			$this->page, 
			$this->page->addHeadMetaTag($tag1)
		);

		$expected = array($tag1);
		$this->assertEquals($expected, $head->getMetaTags());

		$tag2 = new MetaTag(null, null, null, 'UTF-8');
		$this->assertSame(
			$this->page, 
			$this->page->addHeadMetaTag($tag2)
		);
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $head->getMetaTags());

		$tag3 = new MetaTag(null, 'text/html', 'Content-Type', 'UTF-8');
		$this->assertSame(
			$this->page, 
			$this->page->addHeadMetaTag($tag3)
		);
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $head->getMetaTags());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableCss()
	{
		$this->assertSame($this->page, $this->page->disableCss());
		$this->assertFalse($this->page->isCss());
		$this->assertSame($this->page, $this->page->enableCss());
		$this->assertTrue($this->page->isCss());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadLink()
	{
		$head = $this->page->getHtmlHead();
		$this->assertEquals(array(), $head->getCssTags());

		$this->assertSame(
			$this->page,
			$this->page->addHeadLink('myfile.css')
		);
		$tag1 = new LinkTag('myfile.css');
		$expected = array($tag1);
		$this->assertEquals($expected, $head->getCssTags());

		$this->assertSame(
			$this->page,
			$this->page->addHeadLink('my-other-file.css', 'index')
		);
		$tag2 = new LinkTag('my-other-file.css', 'index');
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $head->getCssTags());

		$this->assertSame(
			$this->page,
			$this->page->addHeadLink('file.css', 'index', 'text/html')
		);
		$tag3 = new LinkTag('file.css', 'index', 'text/html');
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $head->getCssTags());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadStyle()
	{
		$head = $this->page->getHtmlHead();
		$this->assertEquals(array(), $head->getCssTags());

		$content1 = 'p{color:red}';
		$this->assertSame($this->page, $this->page->addHeadStyle($content1));

		$tag1 = new StyleTag($content1);
		$expected = array($tag1);
		$this->assertEquals($expected, $head->getCssTags());

		$content2 = 'p{color:red}';
		$this->assertSame(
			$this->page, 
			$this->page->addHeadStyle($content1, 'text/html')
		);

		$tag2 = new StyleTag($content1, 'text/html');
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $head->getCssTags());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadInlineStyle()
	{
		$head = $this->page->getHtmlHead();
		$style = $head->getInlineStyleTag();
		$this->assertTrue($style->isEmpty());

		$content1 = 'p{color:red}';
		$this->assertSame(
			$this->page, 
			$this->page->addHeadInlineStyle($content1)
		);
		$this->assertEquals($content1, $style->getContent(0));

		$content2 = 'h1{color:blue}';
		$this->assertSame(
			$this->page, 
			$this->page->addHeadInlineStyle($content2)
		);
		$this->assertEquals($content2, $style->getContent(1));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testIsEnableDisableJs()
	{
		$this->assertSame($this->page, $this->page->disableJs());
		$this->assertFalse($this->page->isJs());
		$this->assertSame($this->page, $this->page->enableJs());
		$this->assertTrue($this->page->isJs());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadScript()
	{
		$head = $this->page->getHtmlHead();
		$this->assertEquals(array(), $head->getScripts());

		$this->assertSame(
			$this->page,
			$this->page->addHeadScript('my-file.js')
		);

		$tag1 = new ScriptTag('my-file.js');
		$expected = array($tag1);
		$this->assertEquals($expected, $head->getScripts());

		$this->assertSame(
			$this->page,
			$this->page->addHeadScript('my-other-file.js')
		);

		$tag2 = new ScriptTag('my-other-file.js');
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $head->getScripts());

		$tag3 = new ScriptTag('file.js');
		$this->assertSame(
			$this->page,
			$this->page->addHeadScript($tag3)
		);
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $head->getScripts());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddHeadInlineScript()
	{
		$head = $this->page->getHtmlHead();
		$script = $head->getInlineScriptTag();
		$this->assertTrue($script->isEmpty());

		$content1 = 'alert("blah");';
		$this->assertSame(
			$this->page,
			$this->page->addHeadInlineScript($content1)
		);
		$this->assertEquals($content1, $script->getContent(0));
	}

	/**
	 * @depends	testInitialState
	 * @return	null	
	 */
	public function testAddBodyAttribute()
	{
		$body = $this->page->getHtmlBody();

		$this->assertFalse($body->isAttribute('id'));
		$this->assertNull($body->getAttribute('id'));
		
		$this->assertSame(
			$this->page,
			$this->page->addBodyAttribute('id', 'my-id')
		);
		$this->assertTrue($body->isAttribute('id'));
		$this->assertEquals('my-id', $body->getAttribute('id'));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddMarkup()
	{
		$body = $this->page->getHtmlBody();
		
		$markup1 = '<h1>i am a title</h1>';
		$markup2 = '<p>i am some text</p>';
		$markup3 = '<div>i am a section</div>';

		$this->assertTrue($body->isMarkupEmpty());
		$this->assertSame(
			$this->page,
			$this->page->addMarkup($markup1)
		);
		$this->assertEquals($markup1, $body->getMarkup(0));
		
		$this->assertSame(
			$this->page,
			$this->page->addMarkup($markup2, 'append')
		);	
		$this->assertEquals($markup1, $body->getMarkup(0));
		$this->assertEquals($markup2, $body->getMarkup(1));

		$this->assertSame(
			$this->page,
			$this->page->addMarkup($markup3, 'prepend')
		);	
		$this->assertEquals($markup3, $body->getMarkup(0));
		$this->assertEquals($markup1, $body->getMarkup(1));
		$this->assertEquals($markup2, $body->getMarkup(2));

		$this->assertSame(
			$this->page,
			$this->page->addMarkup($markup2, 'replace')
		);	
		$this->assertEquals($markup2, $body->getMarkup(0));
		$this->assertFalse($body->getMarkup(1));
		$this->assertFalse($body->getMarkup(2));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddBodyScript()
	{
		$body = $this->page->getHtmlBody();
		$this->assertEquals(array(), $body->getScripts());

		$this->assertSame(
			$this->page,
			$this->page->addBodyScript('my-file.js')
		);

		$tag1 = new ScriptTag('my-file.js');
		$expected = array($tag1);
		$this->assertEquals($expected, $body->getScripts());

		$this->assertSame(
			$this->page,
			$this->page->addBodyScript('my-other-file.js')
		);

		$tag2 = new ScriptTag('my-other-file.js');
		$expected = array($tag1, $tag2);
		$this->assertEquals($expected, $body->getScripts());

		$tag3 = new ScriptTag('file.js');
		$this->assertSame(
			$this->page,
			$this->page->addBodyScript($tag3)
		);
		$expected = array($tag1, $tag2, $tag3);
		$this->assertEquals($expected, $body->getScripts());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testAddToBodyInlineScript()
	{
		$body = $this->page->getHtmlBody();
		$script = $body->getInlineScriptTag();
		$this->assertTrue($script->isEmpty());

		$content1 = 'alert("blah");';
		$content2 = 'var me="me";';
		$content3 = 'var you="you";';

		$this->assertSame(
			$this->page,
			$this->page->addToBodyInlineScript($content1)
		);
		$this->assertEquals($content1, $script->getContent(0));

		$this->assertSame(
			$this->page,
			$this->page->addToBodyInlineScript($content2, 'append')
		);
		$this->assertEquals($content1, $script->getContent(0));
		$this->assertEquals($content2, $script->getContent(1));

		$this->assertSame(
			$this->page,
			$this->page->addToBodyInlineScript($content3, 'prepend')
		);
		$this->assertEquals($content3, $script->getContent(0));
		$this->assertEquals($content1, $script->getContent(1));
		$this->assertEquals($content2, $script->getContent(2));


		$this->assertSame(
			$this->page,
			$this->page->addToBodyInlineScript($content2, 'replace')
		);
		$this->assertEquals($content2, $script->getContent(0));
		$this->assertFalse($script->getContent(1));
		$this->assertFalse($script->getContent(2));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testViewTemplateAssignIsAssign()
	{
		$view = new ViewTemplate();
		$this->assertSame($this->page, $this->page->setView($view));
		$this->assertSame($view, $this->page->getView());

		/* when a view template is assigned all page assigns get 
		 * routed into the view template via the content key. you
		 * can get the content key with getContentKey
		 */
		$key = $this->page->getContentKey();
		$this->assertTrue($this->page->isTemplate($key));
		$this->assertSame($view, $this->page->getTemplate($key));

		$this->assertSame(
			$this->page,
			$this->page->assign('foo', 'bar')
		);
		$this->assertTrue($this->page->isAssigned('foo'));
		$this->assertTrue($view->isAssigned('foo'));	
		$this->assertEquals('bar', $this->page->get('foo'));
		$this->assertEquals('bar', $view->get('foo'));
	}

	public function testViewAsString()
	{
		$view = '<div>some manual view content</div>';
		$this->assertSame($this->page, $this->page->setView($view));
		$this->assertEquals($view, $this->page->getView());

		$this->assertSame($this->page, $this->page->assign('foo', 'bar'));
		$this->assertTrue($this->page->isAssigned('foo'));
		$this->assertEquals('bar', $this->page->get('foo'));
	}
}
