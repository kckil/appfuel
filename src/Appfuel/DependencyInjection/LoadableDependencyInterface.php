<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use LogicException,
    DomainException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface;

interface LoadableDependencyInterface extends DependencyInterface
{
    /**
     * @return  ServiceBuilderInterface
     */
    public function getServiceBuilder();

    /**
     * @param   DIContainerInterface    $container
     * @return  mixed
     */
    public function loadService(DIContainerInterface $container);

    /**
     *
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function build(DIContainerInterface $container);
}
