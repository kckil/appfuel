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
    Appfuel\DependencyInjection\DIContainer;

class DIContainerTest extends FrameworkTestCase 
{
    /**
     * @param   string  $key
     * @param   Appfuel\DependencyInjection\ServiceBuilderInterface
     * @return  Dependency
     */
    public function createDIContainer()
    {
        return new DIContainer();
    }

    /**
     * @param   string  $key
     * @return  Appfuel\DependencyInjection\Dependency
     */
    public function createMockDependency($key)
    {
        $interface = 'Appfuel\\DependencyInjection\\DependencyInterface';
        $dependency = $this->getMock($interface);
        $dependency->expects($this->any())
                   ->method('getServiceKey')
                   ->will($this->returnValue($key));

        return $dependency;
    }


    /**
     * @param   string  $key
     * @return  Appfuel\DependencyInjection\LoadableDependencyInterface
     */
    public function createMockLoadableDependency($key)
    {
        $siface = 'Appfuel\\DependencyInjection\\ServiceBuilderInterface';
        $builder = $this->getMock($siface);

        $diface = 'Appfuel\\DependencyInjection\\LoadableDependencyInterface';
        $dependency = $this->getMock($diface);
        $dependency->expects($this->any())
                   ->method('getServiceKey')
                   ->will($this->returnValue($key));

        $dependency->expects($this->any())
                   ->method('getServiceBuilder')
                   ->will($this->returnValue($builder));

        return $dependency;
    }


    /**
     * @test
     * @return  Dependency
     */
    public function creatingContainer()
    {
        $container = $this->createDIContainer();
        $interface = 'Appfuel\\DependencyInjection\\DIContainer';
        $this->assertInstanceOf($interface, $container);

        return $container;
    }

    /**
     * @test
     * @depends creatingContainer
     * @param   DIContainer $container
     * @return  DIContainer
     */
    public function registeringDependencies(DIContainer $container)
    {
        $this->assertEquals(0, $container->dependencyCount());

        $key = 'service-a';
        $dependency = $this->createMockDependency($key);
        $this->assertSame($container, $container->addDependency($dependency));
        $this->assertEquals(1, $container->dependencyCount());
        $this->assertTrue($container->isDependency($key));
        $this->assertSame($dependency, $container->getDependency($key));
        
        $this->assertSame($container, $container->removeDependency($key));
        $this->assertFalse($container->isDependency($key));
        $this->assertFalse($container->getDependency($key));
        $this->assertEquals(0, $container->dependencyCount());

        return $container;
    }

    /**
     * @test
     * @depends registeringDependencies
     * @param   DIContainer $container
     * @return  DIContainer
     */
    public function removeDependencyUsingDependency(DIContainer $container)
    {
        $key = 'service-a';
        $dependency = $this->createMockDependency($key);
        $container->addDependency($dependency);
        $this->assertTrue($container->isDependency($key));

        $this->assertSame(
            $container,
            $container->removeDependency($dependency)
        );

        $this->assertFalse($container->isDependency($key));
        $this->assertFalse($container->getDependency($key));
        $this->assertEquals(0, $container->dependencyCount());

        return $container;
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @depends         registeringDependencies
     */
    public function removeDependencyNotAStringFailure($badKey)
    {
        $msg  = 'parameter must be a string or an object that implements '; 
        $msg .= '-(Appfuel\DependencyInjection\DependencyInterface)';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $container = $this->createDIContainer();
        $container->removeDependency($badKey); 
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @depends         registeringDependencies
     */
    public function isDependencyKeyNotAStringFailure($badKey)
    {
        $msg = 'service key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $container = $this->createDIContainer();
        $container->isDependency($badKey); 
    }

    /**
     * @test
     * @depends creatingContainer
     * @return  DIContainer
     */   
    public function determineLoadableDependencies(DIContainer $container)
    {
        $dependency = $this->createMockDependency('service-a');
        $lDependency = $this->createMockLoadableDependency('service-b');
        
        $this->assertFalse($container->isDependencyLoadable($dependency));
        $this->assertTrue($container->isDependencyLoadable($lDependency));

        return $container;
    }

    /**
     * @test
     * @depends determineLoadableDependencies
     * @return  DIContainer
     */
    public function gettingNonLoadableExistingDepedency(DIContainer $container)
    {
        $key = 'my-service';
        $dependency = $this->createMockDependency($key);
        
        $dependency->expects($this->once())
                   ->method('isServiceAvailable')
                   ->will($this->returnValue(true));

        $service = new StdClass;
        $dependency->expects($this->once())
                   ->method('getService')
                   ->will($this->returnValue($service));

        $container->addDependency($dependency);
        $this->assertSame($service, $container->getService($key));

        return $container;
    }

    /**
     * @test
     * @depends gettingNonLoadableExistingDepedency
     */
    public function getServiceNoDependency(DIContainer $container)
    {
        $key = 'other-service';
        $this->assertFalse($container->isDependency($key));

        $msg  = 'a dependency has not been added for this service ';
        $msg .= '-(other-service)';
        $this->setExpectedException('LogicException', $msg);

        $container->getService($key);
    }

    /**
     * @test
     * @depends gettingNonLoadableExistingDepedency
     */
    public function getServiceWithDependencyNotLoadable(DIContainer $container)
    {
        $key = 'service-a';
        $dependency = $this->createMockDependency($key);
        $dependency->expects($this->once())
                   ->method('isServiceAvailable')
                   ->will($this->returnValue(false));

        $container->addDependency($dependency);

        $msg  = 'service -(service-a) is not available and was not added as ';
        $msg .= 'a loadable dependency';
        $this->setExpectedException('LogicException', $msg);

        $service = $container->getService($key);
    }
}
