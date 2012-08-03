<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Http\HttpOutput,
    Testfuel\FrameworkTestCase;

class HttpOutputTest extends FrameworkTestCase 
{
    /**
     * @test
     * @return  HttpOutput
     */
    public function creatingEmptyHttpOutput()
    {
        $output = new HttpOutput();
        $interface = 'Appfuel\\Http\\HttpOutputInterface';
        $this->assertInstanceOf($interface, $output);
    }

}
