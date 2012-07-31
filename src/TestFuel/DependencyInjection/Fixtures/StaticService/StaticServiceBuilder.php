<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection\Fixtures\StaticService;

use Appfuel\DependencyInjection\ServiceBuilder,
    Appfuel\DependencyInjection\DIContainerInterface;

class StaticServiceBuilder extends ServiceBuilder
{
    /**
     * @return  ServiceABuilder
     */
    public function __construct()
    {
        $this->setSettingsKeys(array('key-a'));
    }

        
    public function build(DIContainerInterface $container)
    {
        $settings = $this->getSettings();
        return new StaticService($setting->get('key-a', 'default value'));
    }

    /**
     * @param   mixed   $obj
     * @return  bool
     */
    public function isService($obj)
    {
        return $obj instanceof StaticService;
    }
}
