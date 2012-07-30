<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection\Fixtures\FixtureServiceA;

use Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\DependencyInjection\ServiceBuilder,
    Appfuel\DependencyInjection\DIContainerInterface;

class ServiceABuilder extends ServiceBuilder
{
    /**
     * @return  ServiceABuilder
     */
    public function __construct()
    {
        $this->setSettingsKeys(array('key-a', 'key-b', 'key-c'));
    }

        
    public function build(DIContainerInterface $container)
    {
        $settings = $this->getSettings();
        $valuea = $settings->get('key-a');
        $valueb = $settings->get('key-b');
        $valuec = $settings->get('key-c');

        return new ServiceA($valuea, $valueb, $valuec);
    }

    /**
     * @param   mixed   $obj
     * @return  bool
     */
    public function isService($obj)
    {
        return $obj instanceof ServiceA;
    }

    /**
     * @param   ArrayDataInterface  $settings
     * @return  bool
     */
    public function isValidSettings(ArrayDataInterface $settings)
    {
        if (! $settings->existsAs('key-a', 'non-empty-string')) {
            $this->setError('key-a must be a non empty string');
            return false;
        }

        if (! $settings->existsAs('key-b', 'bool')) {
            $this->setError('key-b must be a bool value');
            return false;
        }

        if (! $settings->existsAs('key-c', 'int')) {
            $this->setError('key-c must be an integer');
            return false;
        }

        return true;
    }
}
