<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection;

use StdClass,
    Testfuel\FrameworkTestCase,
    Testfuel\DependencyInjection\Fixtures\FixtureServiceA\ServiceABuilder,
    Testfuel\DependencyInjection\Fixtures\FixtureServiceA\ServiceADependency,
    Appfuel\DependencyInjection\DIContainer,
    Appfuel\DependencyInjection\ServiceBuilder,
    Appfuel\DependencyInjection\LoadableDependency;

require_once __DIR__ . '/Fixtures/FixtureServiceA/ServiceA.php';
require_once __DIR__ . '/Fixtures/FixtureServiceA/ServiceABuilder.php';
require_once __DIR__ . '/Fixtures/FixtureServiceA/ServiceADependency.php';

require_once __DIR__ . '/Fixtures/StaticService/StaticService.php';
require_once __DIR__ . '/Fixtures/StaticService/StaticServiceBuilder.php';

class LoadableDependencyTest extends FrameworkTestCase 
{
    /**
     * @param   string  $key
     * @param   Appfuel\DependencyInjection\ServiceBuilderInterface
     * @return  Dependency
     */
    public function createLoadableDependency($key, $builder)
    {
        return  new LoadableDependency($key, $builder);
    }

    /**
     * @return  DIContainer
     */
    public function createDIContainer()
    {
        return new DIContainer();
    }

    /**
     * @return  ServiceABuilder
     */
    public function createServiceABuilder()
    {
        return new ServiceABuilder();
    }

    /**
     * @return  ServiceADependency
     */    
    public function createServiceADependency()
    {
        return new ServiceADependency();
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
    public function creatingLoadableDependency()
    {
        $key = 'service-a';
        $builder = $this->createMockServiceBuilder();
        $dependency = $this->createLoadableDependency($key, $builder);

        $iface = 'Appfuel\\DependencyInjection\\DependencyInterface';
        $this->assertInstanceOf($iface, $dependency);
        
        $iface = 'Appfuel\\DependencyInjection\\LoadableDependencyInterface';
        $this->assertInstanceOf($iface, $dependency);
        

        $this->assertEquals($key, $dependency->getServiceKey());
        $this->assertSame($builder, $dependency->getServiceBuilder());

        return $dependency;
    }

    /**
     * @test
     */
    public function loadingAServiceA()
    {
        $dependency = $this->createServiceADependency();
        $this->assertFalse($dependency->isServiceAvailable());

        $container = $this->createDIContainer();
        $container->assign('key-a', 'this is a string')
                  ->assign('key-b', true)
                  ->assign('key-c', 12345);

        $service = $dependency->loadService($container);
        $class  = 'Testfuel\\DependencyInjection\\Fixtures\\FixtureServiceA';
        $class .= '\\ServiceA';
        $this->assertInstanceOf($class, $service);
        $this->assertTrue($dependency->isServiceAvailable());
        $this->assertSame($service, $dependency->getService());
    }

    /**
     * @test
     */
    public function loadingAServiceAFailureNoKeys()
    {
        $dependency = $this->createServiceADependency();
        $this->assertFalse($dependency->isServiceAvailable());
        
        $msg  = 'dependency settings are invalid: key-a must be a ';
        $msg .= 'non empty string';
        $this->setExpectedException('DomainException', $msg);
    
        $container = $this->createDIContainer();
        $service = $dependency->loadService($container);
    }

    /**
     * @test
     * @depends creatingLoadableDependency
     */
    public function loadingAServiceBuildFailure(LoadableDependency $dependency)
    {
        $builder = $dependency->getServiceBuilder();
        $builder->expects($this->once())
                ->method('build')
                ->will($this->returnValue(false));
      
        $builder->expects($this->once())
                ->method('getSettingsKeys')
                ->will($this->returnValue(array('key-a', 'key-b', 'key-c')));

        $error = 'this is an error';
        $builder->expects($this->once())
                ->method('getError')
                ->will($this->returnValue($error)); 
        
        $key  = $dependency->getServiceKey();
        $msg  = "static loading failed. build error: -($key, $error)";
        $this->setExpectedException('DomainException', $msg);
    
        $container = $this->createDIContainer();
        $service = $dependency->loadService($container);
    }

    /**
     * @test
     * @return null
     */
    public function loadStaticService()
    {
        $key = 'my-service';
        $builder = $this->createServiceABuilder();
        $dependency = $this->createLoadableDependency($key, $builder, true);

        $container = $this->createDIContainer();
        $container->assign('key-a', 'this is a string')
                  ->assign('key-b', true)
                  ->assign('key-c', 12345);

        $service1 = $dependency->loadService($container);
        $class  = 'Testfuel\\DependencyInjection\\Fixtures\\FixtureServiceA'; 
        $class .= '\\ServiceA'; 
        $this->assertInstanceOf($class, $service1);

        $service2 = $dependency->loadService($container);
        $this->assertInstanceOf($class, $service2);
        $this->assertSame($service1, $service2);
       
        /* no effect when you change the settings */
        $container->assign('key-c', 12345);
        $service3 = $dependency->loadService($container);
        $this->assertSame($service1, $service3);
    }

    /**
     * @test
     * @return null
     */
    public function loadNonStaticService()
    {
        $key = 'my-service';
        $builder = $this->createServiceABuilder();
        $dependency = $this->createLoadableDependency($key, $builder, false);

        $container = $this->createDIContainer();
        $container->assign('key-a', 'this is a string')
                  ->assign('key-b', true)
                  ->assign('key-c', 12345);

        $service1 = $dependency->loadService($container);
        $class  = 'Testfuel\\DependencyInjection\\Fixtures\\FixtureServiceA'; 
        $class .= '\\ServiceA'; 
        $this->assertInstanceOf($class, $service1);

        $service2 = $dependency->loadService($container);
        $this->assertInstanceOf($class, $service2);
        $this->assertSame($service1, $service2);
       
        /* no effect when you change the settings */
        $container->assign('key-c', 12345);
        $service3 = $dependency->loadService($container);
        $this->assertSame($service1, $service3);
    }
}
