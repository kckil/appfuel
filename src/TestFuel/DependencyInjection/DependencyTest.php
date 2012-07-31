<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\DependencyInjection\Dependency,
    Appfuel\DependencyInjection\DIContainer,
    Appfuel\DependencyInjection\ServiceBuilder;

class DependencyTest extends FrameworkTestCase 
{
    /**
     * @return  array
     */
    public function provideValidServices()
    {
        return array(
            array(new StdClass),
            array(12345),
            array('this is a service'),
            array(array(1,2,3,4))
        );
    }

    /**
     * @param   string  $key
     * @param   Appfuel\DependencyInjection\ServiceBuilderInterface
     * @return  Dependency
     */
    public function createDependency($key, $isUnique = null, $service = null)
    {
        return  new Dependency($key, $isUnique, $service);
    }

    /**
     * @return  ServiceBuilder
     */
    public function createMockServiceBuilder()
    {
        $interface = 'Appfuel\\DependencyInjection\\ServiceBuilderInterface';
        return  $this->getMock($interface);
    }

    /**
     * @test
     * @return  Dependency
     */
    public function creatingEmptyDependency()
    {
        $key = 'my-service';
        $dependency = $this->createDependency($key);

        $interface = 'Appfuel\\DependencyInjection\\DependencyInterface';
        $this->assertInstanceOf($interface, $dependency);
        $this->assertEquals($key, $dependency->getServiceKey());
        $this->assertFalse($dependency->isUniqueService());

        return $dependency;
    }

    /**
     * @test
     * @return  Dependency
     */
    public function creatingDependencyWithService()
    {
        $key = 'my-service';
        $service = new StdClass;
        $isUnique = false;
        $dependency = $this->createDependency($key, $isUnique, $service);
        
        $interface = 'Appfuel\\DependencyInjection\\DependencyInterface';
        $this->assertInstanceOf($interface, $dependency);
        $this->assertEquals($key, $dependency->getServiceKey());
        $this->assertTrue($dependency->isServiceAvailable());
        $this->assertSame($service, $dependency->getService());
        $this->assertFalse($dependency->isUniqueService());
    }

    /**
     * @test
     * @param   string  $badKey
     * @depends creatingEmptyDependency 
     * @dataProvider provideInvalidStringsIncludeEmpty
     */
    public function creatingInvalidDependency($badKey)
    {
        $msg = 'service key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $dependency = $this->createDependency($badKey);
    }


    /**
     * This method is ment to be overriden. The default behavior is to always
     * return true which is basically a setting with no validation.
     * 
     * @test
     * @depends creatingEmptyDependency
     * @return  Dependency
     */
    public function validatingService(Dependency $dependency)
    {
        $service = new StdClass;
        $this->assertTrue($dependency->isValidService($service));
    }

    /**
     * @test
     * @dataProvider    provideValidServices
     * @depends         creatingEmptyDependency
     */
    public function settingAService($service)
    {
        $dependency = $this->createDependency('my-service'); 
        $this->assertFalse($dependency->isServiceAvailable());
        $this->assertNull($dependency->getService());

        $this->assertSame($dependency, $dependency->setService($service));
        $this->assertTrue($dependency->isServiceAvailable());
        $this->assertSame($service, $dependency->getService());

        $this->assertSame($dependency, $dependency->clearService());
        $this->assertFalse($dependency->isServiceAvailable());
        $this->assertNull($dependency->getService());
       
        return $dependency; 
    }
}
