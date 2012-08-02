<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Appfuel\Http\HttpRequest,
    Testfuel\FrameworkTestCase;

class HttpRequestTest extends FrameworkTestCase 
{

    /**
     * @return  null
     */
    public function setUp()
    {
        HttpRequest::markProxyAsUnsafe();
    }

    /**
     * @param   string  $evn    name of env app is running in
     * @return  HttpRequest
     */
    public function createRequest(array $params)
    {
        return new HttpRequest($params);
    }
   
    /**
     * @return  array
     */
    public function provideHttpsOnValues()
    {
        return array(
            array('on'),
            array('ON'),
            array('On'),
            array('oN'),
            array(1),
            array('1')
        );
    }

    /**
     * @return  array
     */
    public function provideIsMethodSafeData()
    {
        return array(
            array('GET',    true),
            array('HEAD',   true),
            array('POST',   false),
            array('PUT',    false),
            array('DELETE', false)
        );
    }

    /**
     * @return  array
     */
    public function provideQueryStringData()
    {
        return array(
            array('', '', 'empty string is safe'),
            array('foo', 'foo', 'works with valueless params'),
            array('foo=', 'foo=', 'includes dangling equal signs'),
            array('foo=&bar=baz', 'foo=&bar=baz', 'works with empty params'),
            array(
                'him=John%20Doe&her=Jane+Doe',
                'him=John%20Doe&her=Jane%20Doe', 
                'normalize spaces in both encodings "%20" and "+"'
            ),
            array(
                'foo[]=1&foo[]=2',
                'foo%5B%5D=1&foo%5B%5D=2',
                'allows array notation'
            ),
            array('foo=1&foo=2', 'foo=1&foo=2', 'allows repeated params'),
            array(
                'pa%3Dram=foo%26bar%3Dbaz&test=test',
                'pa%3Dram=foo%26bar%3Dbaz&test=test',
                'works with encoded delimiters'
            ),
            array('0', '0', 'allows "0"'),
            array(
                'Jane Doe&John%20Doe',
                'Jane%20Doe&John%20Doe',
                'normalizes encoding in keys'
            ),
            array(
                'her=Jane Doe&him=John%20Doe',
                'her=Jane%20Doe&him=John%20Doe',
                'normalizes encoding in values'
            ),
            array('foo=bar&&&test&&', 'foo=bar&test', 'removes uneeded delims'),
            array(
                'formula=e=m*c^2',
                'formula=e%3Dm%2Ac%5E2',
                'correctly treats only the first "=" as delimiter and the next as value'
            ),
            array(
                'foo=bar&=a=b&=x=y', 
                'foo=bar', 
                'removes params with empty key'
            ),
        );
    }

    /**
     * @return  array
     */
    public function provideBaseUrlData()
    {
        return array(
            array(
                array(
                    'REQUEST_URI'     =>  '/foo%20bar',
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/',
            ),
            array(
                array(
                    'REQUEST_URI'     => '/foo%20bar/home',
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/home',
            ),
            array(
                array(
                    'REQUEST_URI'     => '/foo%20bar/app.php/home',
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home',
            ),
            array(
                array(
                    'REQUEST_URI'     => '/foo%20bar/app.php/home%3Dbaz',
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home%3Dbaz',
            ),
            array(
                array(
                    'REQUEST_URI'     => '/foo/bar+baz',
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo/app.php',
                    'SCRIPT_NAME'     => '/foo/app.php',
                    'PHP_SELF'        => '/foo/app.php',
                ),
                '/foo',
                '/bar+baz',
            ),
        );
    }

    /**
     * @test
     * @return  HttpRequest
     */
    public function creatingHttpRequest()
    {
        $params = array(
            'param1' => 'value1',
            'param2' => 'value2'
        );
        $request = $this->createRequest($params);
        $interface = 'Appfuel\\Http\\HttpRequestInterface';
        $this->assertInstanceOf($interface, $request);

        return $request;
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  HttpRequest
     */
    public function trustingTheProxy()
    {
        $this->assertFalse(HttpRequest::isProxyTrusted());
        $this->assertNull(HttpRequest::markProxyAsTrusted());
        $this->assertTrue(HttpRequest::isProxyTrusted());

        $this->assertNull(HttpRequest::markProxyAsUnsafe());
        $this->assertFalse(HttpRequest::isProxyTrusted());
    }

    /** 
     * @test 
     * @depends creatingHttpRequest 
     * @return  HttpRequest 
     */ 
    public function retrievingServerData() 
    {
        $server = array(
            'param1' => 'value-1',
            'param2' => 'value-2',
            'param3' => 'value-3'
        );
        $request = $this->createRequest($server);
        $this->assertEquals($server, $request->getAll());

        foreach ($server as $key => $value) {
            $this->assertTrue($request->exists($key));
            $this->assertEquals($value, $request->get($key));
        }

        $key = 'n/a';
        $this->assertFalse($request->exists($key));
        $this->assertNull($request->get($key));

        $default = 'default value';
        $this->assertEquals($default, $request->get($key, $default));
        
        $default = 12345;
        $this->assertEquals($default, $request->get($key, $default));
    
        return $request;
    }

    /**
     * @test
     * @depends         retrievingServerData
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function checkingSometingExistsFailure($badKey)
    {
        $msg = 'server key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

         $server = array(
            'param1' => 'value-1',
            'param2' => 'value-2',
            'param3' => 'value-3'
        );
        $request = $this->createRequest($server);

        $result = $request->exists($badKey);
    }

    /**
     * @test
     * @depends         retrievingServerData
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function getSomeServerDataFailure($badKey)
    {
        $msg = 'server key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

         $server = array(
            'param1' => 'value-1',
            'param2' => 'value-2',
            'param3' => 'value-3'
        );
        $request = $this->createRequest($server);

        $result = $request->get($badKey);
    }

    /**
     * @test
     * @dataProvider    provideBaseUrlData
     */
    public function gettingBaseUrl($server, $expected, $expectedPathInfo)
    {
        $request = $this->createRequest($server);
        $this->assertEquals($expected, $request->getBaseUrl());
        $this->assertEquals($expectedPathInfo, $request->getPathInfo());
    }

    /**
     * @test
     * @depends         retrievingServerData
     * @dataProvider    provideHttpsOnValues
     * @return          null
     */
    public function isSecureNotTrustedProxyHttpsOn($https)
    {
        HttpRequest::markProxyAsUnsafe();
        
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertTrue($request->isSecure());
    }

    /**
     * @test
     * @depends retrievingServerData
     * @return  null
     */
    public function isSecureNotTrustedProxyHttpsOff()
    {
        HttpRequest::markProxyAsUnsafe();
        
        $https = 'off';
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $https = 'any value thats not -(on)';
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());
    }

    /**
     * @test
     * @depends         isSecureNotTrustedProxyHttpsOn
     * @dataProvider    provideHttpsOnValues
     * @return          null
     */
    public function isSecureTrustedProxyHttpsOn($https)
    {
        HttpRequest::markProxyAsTrusted();
        
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertTrue($request->isSecure());

        $server = array('SSL_HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertTrue($request->isSecure());
    }

    /**
     * @test
     * @depends isSecureTrustedProxyHttpsOn
     * @return  null
     */
    public function isSecureTrustedProxyHttpsOff()
    {
        HttpRequest::markProxyAsTrusted();
    
        $https = 'off';
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $server = array('SSL_HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $https = 'any value thats not -(on)';
        $server = array('HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $server = array('SSL_HTTPS' => $https);
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertFalse($request->isSecure()); 
    }

    /**
     * @test
     * @depends isSecureTrustedProxyHttpsOn
     * @return  null
     */
    public function getScheme()
    {
        $server = array('HTTPS' => 'on');
        $request = $this->createRequest($server);
        $this->assertEquals('https', $request->getScheme());

        $server = array('HTTPS' => 'off');
        $request = $this->createRequest($server);
        $this->assertEquals('http', $request->getScheme());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingThePort()
    {
        $server = array('SERVER_PORT' => '80');
        $request = $this->createRequest($server);
        $this->assertEquals('80', $request->getPort());

        HttpRequest::markProxyAsUnsafe();
        $server = array('X-Forwarded-Port' => '80');
        $request = $this->createRequest($server);
        $this->assertNull($request->getPort());


        HttpRequest::markProxyAsTrusted();
        $request = $this->createRequest($server);
        $this->assertEquals('80', $request->getPort());
 
        $server = array(
            'SERVER_PORT' => '80',
            'X-Forwarded-Port' => '88',
        );
        $request = $this->createRequest($server);
        $this->assertEquals('88', $request->getPort());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingTheScriptName()
    {
        $server = array('SCRIPT_NAME' => 'my_script.php');
        $request = $this->createRequest($server);
        $this->assertEquals('my_script.php', $request->getScriptName());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertNull($request->getScriptName());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingTheUser()
    {
        $server = array('PHP_AUTH_USER' => 'web-user');
        $request = $this->createRequest($server);
        $this->assertEquals('web-user', $request->getUser());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertNull($request->getUser());
    }

    /**
     * @test
     * @depends gettingTheUser
     * @return  null
     */
    public function gettingThePassword()
    {
        $server = array('PHP_AUTH_PW' => 'xxxxx');
        $request = $this->createRequest($server);
        $this->assertEquals('xxxxx', $request->getPassword());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertNull($request->getPassword());
    }

    /**
     * @test
     * @depends gettingThePassword
     * @return  null
     */
    public function gettingUserInfo()
    {
        $request = $this->createRequest(array());
        $this->assertEquals('', $request->getUserInfo());
        
        $server = array(
            'PHP_AUTH_USER' => 'web-user'
        );
        $request = $this->createRequest($server);
        $this->assertEquals('web-user', $request->getUserInfo());

        $server = array(
            'PHP_AUTH_USER' => 'web-user',
            'PHP_AUTH_PW' => 'password',
        );
        $request = $this->createRequest($server);
        $this->assertEquals('web-user:password', $request->getUserInfo());
 
        $server = array(
            'PHP_AUTH_USER' => 'web-user',
            'PHP_AUTH_PW' => '',
        );
        $request = $this->createRequest($server);
        $this->assertEquals('web-user', $request->getUserInfo());
    }

    /**
     * @test
     * @depends gettingUserInfo
     * @return  null
     */
    public function gettingSchemeAndHttpHost()
    {
        $server = array(
            'SERVER_NAME' => 'myserver',
            'SERVER_PORT' => '90'
        );
        $request = $this->createRequest($server);
        
        $expected = 'http://myserver:90';
        $this->assertEquals($expected, $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_USER'] = 'robert';
        $expected = 'http://robert@myserver:90';
        $request = $this->createRequest($server);
        $this->assertEquals($expected, $request->getSchemeAndHttpHost());
 
        $server['PHP_AUTH_PW'] = 'xxxx';
        $expected = 'http://robert:xxxx@myserver:90';
        $request = $this->createRequest($server);
        $this->assertEquals($expected, $request->getSchemeAndHttpHost());
    }

    /**
     * @test
     * @dataProvider    provideQueryStringData
     * @return  null
     */
    public function normalizeQueryString($str, $expected, $msg)
    {
        $result = HttpRequest::normalizeQueryString($str);
        $this->assertEquals($expected, $result, $msg);
    }

    /**
     * @test
     * @depends normalizeQueryString
     * @return  null
     */
    public function getQueryString()
    {
        $server = array("QUERY_STRING" => '');
        $request = $this->createRequest($server);

        $this->assertNull(
            $request->getQueryString(),
            "empty strings are converted to nulls"
        );
    }


    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function usingForwarded()
    {
        $host = 'www.example.com';
        $server = array('HTTP_X_FORWARDED_HOST' => $host);
        $request = $this->createRequest($server);
        $this->assertTrue($request->isForwarded());
        $this->assertEquals($host, $request->getForwardedHost());

        $server = array();
        $request = $this->createRequest($server);
        $this->assertFalse($request->isForwarded());
        $this->assertNull($request->getForwardedHost());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingTheHostWhenNothingIsSet()
    {
        $server = array();
        $request = $this->createRequest($server);
        $this->assertEquals('', $request->getHost());
    }

    /**
     * @test
     * @depends gettingTheHostWhenNothingIsSet
     * @return  null
     */
    public function gettingTheHostTrustedProxyNoForward()
    {
        HttpRequest::markProxyAsTrusted();
        $host = 'www.example.com';
        $server = array('HTTP_HOST' => $host);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyNoForward
     * @return  null
     */
    public function gettingTheHostTrustedProxyWithForward($host)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array('HTTP_X_FORWARDED_HOST' => "$host, www.second.com");
        $request = $this->createRequest($server);
        $this->assertEquals("www.second.com", $request->getHost());

        $server = array(
            'HTTP_X_FORWARDED_HOST' => "$host, www.second.com, www.third.com"
        );
        $request = $this->createRequest($server);
        $this->assertEquals("www.third.com", $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyWithForward
     * @return  null
     */
    public function gettingTheHostTrustedProxyWithForwardAndHost($host)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array(
            'HTTP_HOST' => $host,
            'HTTP_X_FORWARDED_HOST' => "$host, www.second.com"
        );
        $request = $this->createRequest($server);
        $this->assertEquals("www.second.com", $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyWithForward
     * @return  null
     */
    public function gettingTheHostTrustedProxyOnlyServerName($host)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array(
            'SERVER_NAME' => $host,
        );
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyOnlyServerName
     * @return  null
     */
    public function gettingTheHostTrustedProxyOnlyServerAddr($host)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array(
            'SERVER_ADDR' => $host,
        );
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyOnlyServerAddr
     * @return  null
     */
    public function gettingTheHostWithPort($host)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array( 'HTTP_X_FORWARDED_HOST' => "$host, www.second.com:80");
        $request = $this->createRequest($server);
        $this->assertEquals("www.second.com", $request->getHost());

        $server = array( 'HTTP_HOST' => "$host:80");
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        HttpRequest::markProxyAsUnsafe();
        $server = array('HTTP_HOST' => "$host:80");
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        $server = array('SERVER_NAME' => "$host:80");
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());


        $server = array('SERVER_ADDR' => "$host:80");
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostTrustedProxyOnlyServerAddr
     * @return  null
     */
    public function gettingTheHostCaseSensivity($host)
    {
        $uppercase = strtoupper($host);
        HttpRequest::markProxyAsTrusted();
        $server = array( 'HTTP_X_FORWARDED_HOST' => "$uppercase");
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        $server = array( 'HTTP_HOST' => $uppercase);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        HttpRequest::markProxyAsUnsafe();
        $server = array('HTTP_HOST' => $uppercase);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        $server = array('SERVER_NAME' => $uppercase);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());


        $server = array('SERVER_ADDR' => $uppercase);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostCaseSensivity
     * @return  null
     */
    public function gettingTheHostUnTrusted($host)
    {
        $server = array('HTTP_HOST' => $host);
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHost());

        return $host;
    }

    /**
     * @test
     * @depends gettingTheHostUnTrusted
     * @return  null
     */
    public function gettingTheHttpHost($host)
    {
        $server = array(
            'SERVER_PORT' => '80',
            'HTTP_HOST' => $host
        );
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHttpHost());

        $server = array(
            'HTTPS'         => 'on',
            'SERVER_PORT'   => '443',
            'HTTP_HOST' => $host
        );
        $request = $this->createRequest($server);
        $this->assertEquals($host, $request->getHttpHost());

         $server = array(
            'HTTPS'         => 'on',
            'SERVER_PORT'   => '449',
            'HTTP_HOST' => $host
        );
        $request = $this->createRequest($server);
        $this->assertEquals("$host:449", $request->getHttpHost());

         $server = array(
            'SERVER_PORT'   => '89',
            'HTTP_HOST' => $host
        );
        $request = $this->createRequest($server);
        $this->assertEquals("$host:89", $request->getHttpHost());

        return $host;
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingHttpMethodNormal()
    {
        $server = array('REQUEST_METHOD' => 'GET');
        $request = $this->createRequest($server);
        $this->assertEquals('GET', $request->getMethod());

        $server = array('REQUEST_METHOD' => 'post');
        $request = $this->createRequest($server);
        $this->assertEquals('POST', $request->getMethod());
    }

    /**
     * @test
     * @depends         gettingHttpMethodNormal
     * @dataProvider    provideIsMethodSafeData
     * @return          null
     */
    public function isMethodSafe($method, $expected)
    {
        $server = array('REQUEST_METHOD' => $method);
        $request = $this->createRequest($server);
        $this->assertEquals($expected, $request->isMethodSafe());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingHttpMethodXOverride()
    {
        $server = array('X-HTTP-METHOD-OVERRIDE' => 'GET');
        $request = $this->createRequest($server);
        $this->assertEquals('GET', $request->getMethod());

        $server = array('X-HTTP-METHOD-OVERRIDE' => 'post');
        $request = $this->createRequest($server);
        $this->assertEquals('POST', $request->getMethod());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingHttpMethodBoth()
    {
        $server = array(
            'REQUEST-METHOD' => 'get',
            'X-HTTP-METHOD-OVERRIDE' => 'delete'
        );
        $request = $this->createRequest($server);
        $this->assertEquals('DELETE', $request->getMethod());
    }

    /**
     * @test
     * @depends creatingHttpRequest
     * @return  null
     */
    public function gettingHttpMethodNone()
    {
        $msg = 'http request method was not set';
        $this->setExpectedException('LogicException', $msg);

        $server = array();
        $request = $this->createRequest($server);
        $request->getMethod();
    }

    /**
     * @test
     * @depends         creatingHttpRequest
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function gettingHttpMethodEmpty($badMethod)
    {
        $msg = 'http reqest method must be a non empty string';
        $this->setExpectedException('LogicException', $msg);

        $server = array('REQUEST_METHOD' => $badMethod);
        $request = $this->createRequest($server);
        $request->getMethod();
    }

    /**
     * @test
     * @depends         creatingHttpRequest
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function gettingHttpMethodXOverrideEmpty($badMethod)
    {
        $msg = 'http reqest method must be a non empty string';
        $this->setExpectedException('LogicException', $msg);

        $server = array('X-HTTP-METHOD-OVERRIDE' => $badMethod);
        $request = $this->createRequest($server);
        $request->getMethod();
    }

    /**
     * @test
     * @depends retrievingServerData
     * @return  null
     */
    public function gettingClientIpNoTrustedProxyRemoteAddr()
    {
        HttpRequest::markProxyAsUnsafe();

        $ip = '192.168.1.1';
        $server = array('REMOTE_ADDR' => $ip);
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpNoTrustedProxyRemoteAddr
     * @return  null
     */
    public function gettingClientIpTrustedProxyClientIp($ip)
    {
        HttpRequest::markProxyAsTrusted();

        $server = array('HTTP_CLIENT_IP' => $ip);
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpNoTrustedProxyRemoteAddr
     * @return  null
     */
    public function gettingClientIpTrustedProxyXForwarded($ip)
    {
        HttpRequest::markProxyAsTrusted();
        $ipChain = "$ip, 192.168.1.9, 192.168.1.99";
        $server = array('HTTP_X_FORWARDED_FOR' => $ipChain);
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpNoTrustedProxyRemoteAddr
     * @return  null
     */
    public function gettingClientIpTrustedProxyXForwardedBadIps($ip)
    {
        HttpRequest::markProxyAsTrusted();
        $ipChain = "abc, def, ghi";
        $server = array('HTTP_X_FORWARDED_FOR' => $ipChain);
        $request = $this->createRequest($server);
        $this->assertFalse($request->getClientIp());
   
        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpTrustedProxyXForwardedBadIps
     * @return  null
     */
    public function gettingClientIpTrustedProxyRemoteAddr($ip)
    {
        HttpRequest::markProxyAsTrusted();
        $server = array('REMOTE_ADDR' => $ip);
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpTrustedProxyRemoteAddr
     * @return  null
     */
    public function gettingClientIpTrustedAllAvailable($ip)
    {
        HttpRequest::markProxyAsTrusted();

        $server = array(
            'REMOTE_ADDR'           => '10.0.0.1',
            'HTTP_CLIENT_IP'        => $ip,
            'HTTP_X_FORWARDED_FOR'  => '10.0.0.2'
        );
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }

    /**
     * @test
     * @depends gettingClientIpTrustedAllAvailable
     * @return  null
     */
    public function gettingClientIpTrustedXForwardAndRemote($ip)
    {
        HttpRequest::markProxyAsTrusted();

        $server = array(
            'REMOTE_ADDR'           => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR'  => $ip
        );
        $request = $this->createRequest($server);
        $this->assertEquals($ip, $request->getClientIp());
   
        /* convert to integer passing true */
        $expected = ip2long($ip);
        $this->assertEquals($expected, $request->getClientIp(true));

        return $ip;
    }
}
