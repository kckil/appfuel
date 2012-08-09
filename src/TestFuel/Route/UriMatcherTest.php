<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use Appfuel\Route\UriMatcher;

class UriMatcherTest extends TestRouteCase 
{

    /**
     * @return  array
     */
    public function getDefaultData()
    {
        return array(
            'uri-path'    => '/sections/my/section',
            'http-method' => 'GET',
            'uri-scheme'  => 'http'
        );
    }

    /**
     * @test
     * @return  UriMatcher
     */
    public function creatingUriMatcher()
    {
        $data = $this->getDefaultData();
        $matcher = $this->createUriMatcher($data);

        $interface = $this->getUriMatcherInterface();
        $this->assertInstanceOf($interface, $matcher);

        $this->assertEquals($data['uri-path'], $matcher->getUriPath());
        $this->assertEquals($data['uri-scheme'], $matcher->getUriScheme());
        $this->assertEquals($data['http-method'], $matcher->getHttpMethod());
        $this->assertEquals(array(), $matcher->getCaptures());
        return $matcher;
    }

    /**
     * @test
     * @depends creatingUriMatcher
     * @return  null
     */
    public function creatingUriMatcherUriPathNotThereFailure()
    {
        $data = $this->getDefaultData();
        unset($data['uri-path']);

        $msg = '-(uri-path) is required bu not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @depends         creatingUriMatcher
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function creatingUriMatcherUriPathInvalidFailure($invalid)
    {
        $data = $this->getDefaultData();
        $data['uri-path'] = $invalid;

        $msg = 'uri path must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @depends creatingUriMatcher
     * @return  null
     */
    public function creatingUriMatcherUriSchemeNotThereFailure()
    {
        $data = $this->getDefaultData();
        unset($data['uri-scheme']);

        $msg = '-(uri-scheme) is required but not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @depends         creatingUriMatcher
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function creatingUriMatcherUriSchemeInvalidFailure($invalid)
    {
        $data = $this->getDefaultData();
        $data['uri-scheme'] = $invalid;

        $msg = 'uri scheme must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @depends creatingUriMatcher
     * @return  null
     */
    public function creatingUriMatcherHttpMethodNotThereFailure()
    {
        $data = $this->getDefaultData();
        unset($data['http-method']);

        $msg = '-(http-method) is required but not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @depends         creatingUriMatcher
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function creatingUriMatcherHttpMethodInvalidFailure($invalid)
    {
        $data = $this->getDefaultData();
        $data['http-method'] = $invalid;

        $msg = 'http method must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $matcher = $this->createUriMatcher($data);
    }

    /**
     * @test
     * @return  null
     */
    public function matchingWithNoParams()
    {
        $data = $this->getDefaultData();
        $data['uri-path'] = '/users/groups/rsb/12345';
     
        $matcher = $this->createUriMatcher($data);
        $this->assertEquals($matcher->getUriPath(), $matcher->getCurrentUri());
      
        $this->assertTrue($matcher->match('#^/users#i')); 
        $this->assertFalse($matcher->match('#^/projects#i')); 
        $this->assertEquals('/groups/rsb/12345', $matcher->getCurrentUri());
        $this->assertSame($matcher, $matcher->clearCurrentUri());
    
        return $matcher;
    }

    /**
     * @test
     * @depends matchingWithNoParams
     * @return  null
     */
    public function matchingWithCapturesNoParams(UriMatcher $matcher)
    {
        $pattern = '#^/users/(?<section>\w+)/(?<name>\w+)/(?<id>\d+)#';
        $this->assertTrue($matcher->match($pattern));
        $this->assertEquals('', $matcher->getCurrentUri());

        $expected = array(
            'section' => 'groups',
            'name'    => 'rsb',
            'id'      => '12345'
        );
        $this->assertEquals($expected, $matcher->getCaptures());
        $this->assertSame($matcher, $matcher->clearCaptures());

        $matcher->clearCurrentUri();
    
        return $matcher;
    }

    /**
     * @test
     * @depends matchingWithNoParams
     * @return  null
     */
    public function matchingWithCapturesWithParams(UriMatcher $matcher)
    {
        $pattern = '#^/users/(\w+)/(\w+)/(\d+)#';
        $params = array('section', 'name', 'id');
        $this->assertTrue($matcher->match($pattern, $params));
        $this->assertEquals('', $matcher->getCurrentUri());
        $expected = array(
            'section' => 'groups',
            'name'    => 'rsb',
            'id'      => '12345'
        );
        $this->assertEquals($expected, $matcher->getCaptures());

        $matcher->clearCaptures();
        $matcher->clearCurrentUri();
    
        return $matcher;
    }



}
