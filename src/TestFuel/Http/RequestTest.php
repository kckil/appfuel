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
}
