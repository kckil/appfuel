<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Http\HttpStatus,
    Testfuel\FrameworkTestCase;

class HttpStatusTest extends FrameworkTestCase 
{
    /**
     * @return  array
     */
    public function provideInvalidCodeRange()
    {
        return array(
            array(0),
            array(99),
            array(700),
            array(600)
        );
    }

    /**
     * @test
     * @return  HttpOutput
     */
    public function creatingEmptyHttpStatus()
    {
        $status = new HttpStatus();
        $interface = 'Appfuel\\Http\\HttpStatusInterface';
        $this->assertInstanceOf($interface, $status);
        $this->assertEquals(200, $status->getCode());
        $this->assertEquals('OK', $status->getText());

        return $status;
    }

    /**
     * Test that all codes are between 100 and 600 and all values and non
     * empty strings
     *
     * @test
     * @return    array
     */
    public function statusMap()
    {
        $map = HttpStatus::getStatusMap();
        $this->assertInternalType('array', $map);
        $this->assertNotEmpty($map);

        foreach ($map as $code => $text) {
            $this->assertGreaterThanOrEqual(100, $code);
            $this->assertLessThan(600, $code);

            $this->assertNotEmpty($text);
            $this->assertInternalType('string', $text);
        }
    }

    /**
     * In this test we will get the status map and create new status objects
     * with only the code and test that the correct text was set
     *
     * @test
     * @depends creatingEmptyHttpStatus
     * @return  null
     */
    public function usingAllStatusMapCodes()
    {
        foreach (HttpStatus::getStatusMap() as $code => $text) {
            $status = new HttpStatus($code);
            $this->assertEquals($code, $status->getCode());
            $this->assertEquals($text, $status->getText());
            $this->assertEquals("$code $text", $status->__toString());
        }
    }

    /**
     * This will test the ability to manually supply you own text for the 
     * code
     * 
     * @test 
     * @return    null
     */
    public function manualCodeOverride()
    {
        $code = 200;
        $text = 'my own text';
        $status = new HttpStatus($code, $text);
        $this->assertEquals($code, $status->getCode());
        $this->assertEquals($text, $status->getText());
        $this->assertEquals("$code $text", $status->__toString());
    }

    /**
     * @test
     * @dataProvider    provideInvalidInts
     */ 
    public function invalidCodeFailure($badInt)
    {
        $msg = 'invalid http status code, must be a number or string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $status = new HttpStatus($badInt);
    }

    /**
     * @test
     * @dataProvider    provideInvalidCodeRange
     */ 
    public function invalidCodeRangeFailure($badCode)
    {
        $msg = 'invalid http status code';
        $this->setExpectedException('DomainException', $msg);

        $status = new HttpStatus($badCode);
    }


}
