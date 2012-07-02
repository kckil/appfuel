<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\Orm\OrmManager;

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
        $tmp = $this->getMvcFactory()
                    ->createContext($routeKey, $context->getInput());

        if ($context->isContextView()) {
            $tmp->setView($context->getView());
        }

        $tmp->load($context->getAll());
        if ('' !== trim($context->getViewFormat())) {
            $tmp->setViewFormat($context->getViewFormat());
        }
        $this->dispatch($tmp);

        /* transfer all assignments made by mvc action */
        $context->load($tmp->getAll());
        $view = $tmp->getView();
        if (! empty($view)) {
            $context->setView($view);
        }
        $context->setExitCode($tmp->getExitCode());

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
}
