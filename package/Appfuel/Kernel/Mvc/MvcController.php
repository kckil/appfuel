<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use LogicException,
    Appfuel\Orm\OrmManager,
    Appfuel\Kernel\Route\RouteRegistry;

class MvcController implements MvcControllerInterface
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
    public function execute(MvcContextInterface $context)
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

    /**
     * @throws  LogicException
     * @param   string  $cat
     * @param   string  $key
     * @return  object
     */
    public function getRouteSpec($cat, $key)
    {
        if (! $spec = RouteRegistry::getRouteSpec($cat, $key)) {
            $err = "route specificiation -($cat) was not found for -($key)";
            throw new LogicException($err);
        }

        return $spec;
    }
}
