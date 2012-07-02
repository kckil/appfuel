<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Mvc;

interface MvcControllerInterface extends ExecutableInterface
{
    /**
     * @param   string  $key
     * @return  OrmRepositoryInterface
     */
    public function getRepository($key, $source = 'db');

    /**
     * @param   string              $routeKey
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function call($key, MvcContextInterface $context);
}
