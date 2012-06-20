<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

/**
 * Dispatch a context based on information found in the route detail and 
 * context. This includes management of intercepting filters and by-pass
 * dispatching for any exit code that in not between 200 - 299
 */
class FrontController implements FrontControllerInterface
{    
    /**
     * Used to create the action based on the route and dispatch the context
     * into that action
     * @var MvcActionDispatcher
     */
    protected $dispatcher = null;

    /**
     * Apply Intercept filter logic before mvc action is dispatched
     * @var FilterChainInterface
     */
    protected $preChain = null;

    /**
     * Apply Intercept filter logic after mvc action is dispatched
     * @var FilterChainInterface
     */
    protected $postChain = null;

    /**
     * @param   DispatcherInterface $dispatcher
     * @param   InterceptChainInterface $preChain
     * @param   InterceptChainInterface $postChain
     * @return  FrontController
     */
    public function __construct(DispatcherInterface $dispatcher,
                                InterceptChainInterface $preChain,
                                InterceptChainInterface $postChain)
    {
        $this->dispatcher = $dispatcher;
        $this->preChain = $preChain;
        $this->postChain = $postChain;
    }

    /**
     * @return  DispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return  InterceptChainInterface
     */
    public function getPreChain()
    {
        return $this->preChain;
    }

    /**
     * @return InterceptChainInterface
     */
    public function getPostChain()
    {
        return $this->postChain;
    }

    /**
     *  
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function run(MvcContextInterface $context)
    {
        $routeKey = $context->getRouteKey();
        /*
         * Mark this as the current route. Allows you to tell the difference
         * between the initial route and one called by an mvc action
         */
        $this->setCurrentRoute($routeKey);
        $detail = $this->getRouteDetail($routeKey);

        if ($detail->isPreFilteringEnabled()) {
            $context = $this->runPreFilters($detail, $context);
        }

        /*
         * Only dispatch a context if its exit code is within the range of 
         * success. Note console and html, ajax and api all follow http status
         * codes.
         */
        $exitCode = $context->getExitCode();
        if ($exitCode >= 200 && $exitCode < 300) {
            $dispatcher = $this->getDispatcher();
            $dispatcher->dispatch($context);

            /*
             * PreFilters have the ability to change the current route
             * so we grab it again just incase 
             */
            $tmpRouteKey = $context->getRouteKey();
            if ($tmpRouteKey !== $routeKey) {
                $this->setCurrentRoute($tmpRouteKey);
                $detail = $this->getRouteDetail($tmpRouteKey);
            }

            if ($detail->isPostFilteringEnabled()) {
                $context = $this->runPostFilters($detail, $context);
            }
        }

        return $context;
    }

    /**
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function runPreFilters(MvcRouteDetailInterface $detail,
                                  MvcContextInterface $context)
    {
        $chain  = $this->getPreChain();

        if ($detail->isExcludedPreFilters()) {
            $chain->removeFilters($detail->getExcludedPreFilters());
        }

        if ($detail->isPreFilters()) {
            $chain->loadFilters($detail->getPreFilters());    
        }

        return $chain->applyFilters($context);
    }

    /**
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function runPostFilters(MvcRouteDetailInterface $detail,
                                   MvcContextInterface $context)
    {
        $chain  = $this->getPostChain();

        if ($detail->isExcludedPostFilters()) {
            $chain->removeFilters($detail->getExcludedPostFilters());
        }

        if ($detail->isPostFilters()) {
            $chain->loadFilters($detail->getPostFilters());    
        }

        return $chain->applyFilters($context);
    }

    /**
     * @return  MvcRouteDetail
     */
    protected function getRouteDetail($key)
    {
        return MvcRouteManager::getRouteDetail($key);
    }
}
