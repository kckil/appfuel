<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use LogicException,
    Appfuel\Orm\OrmManager;

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
}
