<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\App;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\App\AppInitializer;

class AppInitializerTest extends FrameworkTestCase 
{

    /**
     * @param   array   $spec
     * @return  FileFinder
     */
    public function createInitializer($env)
    {
        return new AppInitializer($env);
    }
    
    /**
     * @test
     * @return  AppInitializer
     */
    public function creatingAnAppInitializer()
    {
        $env = 'production';
        $init = $this->createInitializer($env);

        $interface = 'Appfuel\\App\\AppInitializerInterface';
        $this->assertInstanceOf($interface, $init);

        $this->assertEquals($env, $init->getEnv());

        return $init;
    }
}
