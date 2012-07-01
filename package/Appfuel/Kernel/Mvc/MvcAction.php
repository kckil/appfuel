<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\Orm\OrmManager;

/**
 * The mvc action is the controller in mvc. The front controller always 
 * dispatches a context to be processed by the mvc action based on a 
 * route (obtained via request uri, generally) that maps to that mvc action.
 * Every mvc action can also dispatch calls (process context) to any other
 * mvc action based on route (and context building), which always mvc actions
 * to be used rather than duplicated. 
 */
class MvcAction implements MvcActionInterface
{
    /**
     * @param   string  $key
     * @return  OrmRepositoryInterface
     */
    public function getRepository($key, $source = 'db')
    {
        return OrmManager::getRepository($key, $source);
    }

    /**
     * Must be implemented by concrete class
     *
     * @param   AppContextInterface $context
     * @return  null
     */
    public function process(MvcContextInterface $context)
    {
        throw new LogicException("must implement concrete process");
    }

    /**
     * @param   string              $routeKey
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function call($key, MvcContextInterface $context)
    {
        return $context->merge($this->dispatch($context->clone($key)));
    }

    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    protected function dispatch(MvcContextInterface $context)
    {
        Dispatcher::dispatch($context);
        return $context;
    }
}
