<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\DependencyInjection\ServiceBuilder;

class ServiceBuilderTest extends FrameworkTestCase 
{
    /**
     * @test
     * @return  ServiceBuilder
     */
    public function creatingServiceBuilder()
    {
        $builder = new ServiceBuilder();
        $interface = 'Appfuel\DependencyInjection\ServiceBuilderInterface';
        $this->assertInstanceOf($interface, $builder);

        return $builder;
    }
}
