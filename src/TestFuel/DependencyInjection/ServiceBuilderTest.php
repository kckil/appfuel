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
     * @return  ServiceBuilder
     */
    public function createServiceBuilder()
    {
        return  new ServiceBuilder();
    }

    /**
     * @test
     * @return  ServiceBuilder
     */
    public function creatingServiceBuilder()
    {
        $builder = $this->createServiceBuilder();
        $interface = 'Appfuel\DependencyInjection\ServiceBuilderInterface';
        $this->assertInstanceOf($interface, $builder);

        return $builder;
    }

    /**
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  ServiceBuilder
     */
    public function serviceKey(ServiceBuilder $builder)
    {
        $this->assertNull($builder->getServiceKey());
        
        $key = 'my-key';
        $this->assertSame($builder, $builder->setServiceKey($key));
        $this->assertEquals($key, $builder->getServiceKey());

        return $builder;
    }

    /**
     * @test
     * @depends         serviceKey
     * @dataProvider    provideInvalidStringsIncludeEmpty 
     * @param           ServiceBuilder
     * @return          null
     */
    public function serviceKeyInvalidKeyFailure($badKey)
    {
        $msg = 'service key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $builder = $this->createServiceBuilder();
        $builder->setServiceKey($badKey);
    }

    /**
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  ServiceBuilder
     */   
    public function settingsKeys(ServiceBuilder $builder)
    {
        $this->assertEquals(array(), $builder->getSettingsKeys());
   
        $keys = array('mykey', 'yourkey', 'ourkey');
        $this->assertSame($builder, $builder->setSettingsKeys($keys));
        $this->assertSame($keys, $builder->getSettingsKeys());

        return $builder; 
    }

    /**
     * @test
     * @depends         settingsKeys
     * @dataProvider    provideInvalidStringsIncludeEmpty 
     * @param           ServiceBuilder
     * @return          null
     */
    public function settingsKeysInvalidKeyFailure($badKey)
    {
        $msg = 'settings key must be a non empty string';
        $this->setExpectedException('OutOfBoundsException', $msg);
        $builder = $this->createServiceBuilder();
        
        $keys = array('mykey', $badKey, 'yourKey');
        $builder->setSettingsKeys($keys);
    }

    /**
     * This method will always return true unless you extend it to provide
     * your own validation checks on the settings data.
     *
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  ServiceBuilder
     */   
    public function isValidSettings(ServiceBuilder $builder)
    {
        $data = $this->getMock($this->getArrayDataInterface());
        $this->assertTrue($builder->isValidSettings($data));
        return $builder; 
    }

    /**
     *
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  ServiceBuilder
     */   
    public function settings(ServiceBuilder $builder)
    {
        $this->assertNull($builder->getSettings());
        
        $data = $this->getMock($this->getArrayDataInterface());
        $this->assertSame($builder, $builder->setSettings($data));
        $this->assertSame($data, $builder->getSettings());

        return $builder; 
    }

    /**
     * Errors can be controlled programatically or by the class internally,
     * to meet both these conditions we need to make the error setter public
     *
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  ServiceBuilder
     */
    public function errors(ServiceBuilder $builder)
    {
        $this->assertFalse($builder->isError());
        $this->assertNull($builder->getError());

        $error = 'this is an error';
        $this->assertSame($builder, $builder->setError($error));
        $this->assertTrue($builder->isError());
        $this->assertSame($error, $builder->getError());

        $this->assertSame($builder, $builder->clearError());
        $this->assertFalse($builder->isError());
        $this->assertNull($builder->getError());

        return $builder;
    }

    /**
     * @test
     * @depends         errors
     * @dataProvider    provideInvalidStringsIncludeEmpty 
     * @param           ServiceBuilder
     * @return          null
     */
    public function settingsErrorFailure($badError)
    {
        $msg = 'a service builder error must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);
        $builder = $this->createServiceBuilder();
    
        $builder->setError($badError);    
    }
    /**
     * You needed to extend this method otherwise a LogicException is thrown
     *
     * @test
     * @depends creatingServiceBuilder
     * @param   ServiceBuilder
     * @return  null
     */
    public function building(ServiceBuilder $builder)
    {
        $msg = 'concrete class must extend this method';
        $this->setExpectedException('LogicException', $msg);

        $interface = 'Appfuel\\DependencyInjection\\DIContainerInterface';
        $container = $this->getMock($interface);
        
        $builder->build($container);
    }
}
