<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Http;

use StdClass,
    Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpResponse,
    TestFuel\TestCase\BaseTestCase;

class HttpOutputTest extends BaseTestCase
{
    /**
     * System under test
     * @var HttpOutput
     */
    protected $output = null;

    /**
     * @return    null
     */
    public function setUp()
    {
        $this->output = new HttpOutput();
    }

    /**
     * @return    null
     */
    public function tearDown()
    {
        $this->output = null;
    }

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Http\HttpOutputInterface',
            $this->output
        );
    }
}
