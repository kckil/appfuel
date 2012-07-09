<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\Orm\OrmManager,
    Appfuel\Route\RouteRegistry;

/**
 * @deprecated  no longer under development use MvcController
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
     * Allow this deprecated controller to be dispatched by the dispatcher
     *
     * @param   MvcContextInterface     $context
     * @return  null
     */
    public function execute(MvcContextInterface $context)
    {
        return $this->process($context);
    }

    /**
     * @param   string  $routeKey
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function callWithContext($routeKey, MvcContextInterface $context)
    {
        $tmp = $context->cloneContext($routeKey);
        $this->dispatch($tmp);
        $context->merge($tmp);
        return $context;
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

    /**
     * @param   string  $cat
     * @param   string  $key
     * @return  object
     */
    protected function getRouteSpec($cat, $key)
    {
        if (! $spec = RouteRegistry::getRouteSpec($cat, $key)) {
            $err = "route specificiation -($cat) was not found for -($key)";
            throw new LogicException($err);
        }

        return $spec;
    }
}
