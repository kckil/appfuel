<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Mvc;

interface MvcActionInterface extends ExecutableInterface
{
    /**
     * @param   string  $key
     * @return  OrmRepositoryInterface
     */
    public function getRepository($key, $source = 'db');

    /**
     * Must be implemented by concrete class
     *
     * @param   AppContextInterface $context
     * @return  null
     */
    public function process(MvcContextInterface $context);

    /**
     * @param   string              $routeKey
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function callWithContext($key, MvcContextInterface $context);
}
