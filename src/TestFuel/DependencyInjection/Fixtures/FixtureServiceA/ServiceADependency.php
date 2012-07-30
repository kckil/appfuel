<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DependencyInjection\Fixtures\FixtureServiceA;

use LogicException,
    DomainException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DependencyInjection\ServiceBuilder,
    Appfuel\DependencyInjection\LoadableDependency,
    Appfuel\DataStructure\ArrayDataInterface;

class ServiceADependency extends LoadableDependency
{
    public function __construct()
    {
        $builder = new ServiceABuilder();
        parent::__construct('service-a', $builder);
    }
}
