<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    SplFileInfo,
    Appfuel\Http\HttpResponse,
    Testfuel\FrameworkTestCase;

class HttpResponseTest extends FrameworkTestCase 
{
    /**
     * @param   string  $data
     * @param   int $status
     * @param   array|HttpHeaderListInterface
     * @return  HttpResponse
     */
    public function createResponse($data = null, $status = null, $hdrs = null)
    {
        return new HttpResponse($data, $status, $hdrs);
    }

    /**
     * @return  array
     */
    public function provideValidContent()
    {
        return array(
            array('', ''),
            array('i am a string', 'i am a string'),
            array(1, '1'),
            array(1.1, '1.1'),
            array(
                new SplFileInfo('this implements __toString'),
                'this implements __toString'
            ),
        );
    }

    /**
     * @return  array
     */
    public function provideInvalidContent()
    {
        return array(
            array(array(1,2,3)),
            array(new StdClass)
        );
    }

    /**
     * @test
     * @return  HttpResponse
     */
    public function creatingEmptyHttpResponse()
    {
        $response = $this->createResponse();
        $interface = 'Appfuel\\Http\\HttpResponseInterface';
        $this->assertInstanceOf($interface, $response);
        $this->assertEquals('', $response->getContent());
        $this->assertEquals('1.0', $response->getProtocolVersion());

        $list = $response->getHeaderList();
        $interface = 'Appfuel\\Http\\HttpHeaderListInterface';
        $this->assertInstanceOf($interface, $list);
       
        $status = $response->getStatus();
        $interface = 'Appfuel\\Http\\HttpStatusInterface';
        $this->assertInstanceOf($interface, $status);
         
        $this->assertEquals(200, $status->getCode());
        $this->assertEquals('OK', $status->getText());

        $expected = 'HTTP/1.0 200 OK';
        $this->assertEquals($expected, $response->getStatusLine());
        return $response;
    }

    /**
     * @test
     * @depends creatingEmptyHttpResponse
     * @return  HttpResponse
     */
    public function usingHeaderList(HttpResponse $response)
    {
        $backup = $response->getHeaderList();
        $list = $this->getMock('Appfuel\\Http\\HttpHeaderListInterface');
        
        $this->assertNotSame($backup, $list);
        $this->assertSame($response, $response->setHeaderList($list));
        $this->assertSame($list, $response->getHeaderList());

        $response->setHeaderList($backup);
        return $response;
    }

    /**
     * @test
     * @depends creatingEmptyHttpResponse
     * @return  HttpResponse
     */
    public function addingHeaders(HttpResponse $response)
    {
        $this->assertEquals(array(), $response->getAllHeaders());

        $header1 = 'Location: http://www.example.com/';
        $this->assertSame($response, $response->addHeader($header1));

        $this->assertEquals(array($header1), $response->getAllHeaders());
 
        $header2 = 'Location: http://www.example2.com/';
        $this->assertSame($response, $response->addHeader($header2));

        $expected = array($header1, $header2);
        $this->assertEquals($expected, $response->getAllHeaders());

        return $response;
    }


    /**
     * @test
     * @depends         creatingEmptyHttpResponse
     * @dataProvider    provideValidContent
     */
    public function content($content, $expected)
    {
        $response = $this->createResponse();
        $this->assertTrue($response->isValidContent($content));
        $this->assertSame($response, $response->setContent($content));
        $this->assertEquals($expected, $response->getContent());

        $response = $this->createResponse($content);
        $this->assertEquals($expected, $response->getContent());
    }

    /**
     * @test
     * @depends         creatingEmptyHttpResponse
     * @dataProvider    provideInvalidContent
     */
    public function contentFailure($badContent)
    {
        $type = gettype($badContent);
        $msg  = "Http response content must be a string or an object ";
        $msg .= "implementing __toString(). parameter type -($type)";
        $this->setExpectedException('DomainException', $msg);

        $response = $this->createResponse();
        $this->assertFalse($response->isValidContent($badContent));
        $response->setContent($badContent);
    }

    /**
     * @test
     * @depends creatingEmptyHttpResponse
     * @return  HttpResponse
     */
    public function protocolVersion(HttpResponse $response)
    {
        $this->assertSame($response, $response->setProtocolVersion('1.1'));
        $this->assertEquals('1.1', $response->getProtocolVersion());

        $this->assertSame($response, $response->setProtocolVersion('1.0'));
        $this->assertEquals('1.0', $response->getProtocolVersion());

        return $response;
    }

    /**
     * @test
     * @depends creatingEmptyHttpResponse
     * @return  HttpResponse
     */
    public function httpStatus(HttpResponse $response)
    {
        $backup = $response->getStatus();
        $mock = $this->getMock('Appfuel\\Http\\HttpStatusInterface');
        $this->assertNotSame($backup, $mock);
        $this->assertSame($response, $response->setStatus($mock));
        $this->assertSame($mock, $response->getStatus());

        $response->setStatus($backup);
        return $response;
    }



}
