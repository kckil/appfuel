<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\Kernel\Route\RouteRegistry,
    Appfuel\Kernel\Route\RouteInterceptFilterSpecInterface;

/**
 * Dispatch a context based on information found in the route detail and 
 * context. This includes management of intercepting filters and by-pass
 * dispatching for any exit code that in not between 200 - 299
 */
class FrontController implements FrontControllerInterface
{    
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
     * @param   InterceptChainInterface $preChain
     * @param   InterceptChainInterface $postChain
     * @return  FrontController
     */
    public function __construct(InterceptChainInterface $preChain,
                                InterceptChainInterface $postChain)
    {
        $this->preChain = $preChain;
        $this->postChain = $postChain;
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
        $key = $context->getRouteKey();
        $filterSpec = $this->getRouteSpec('intercept-filter', $key); 

        if ($filterSpec->isPreFilteringEnabled()) {
            $context = $this->runPreFilters($filterSpec, $context);
        }

        /*
         * only dispatch to the proper http reponse codes. This allows pre 
         * filters to use http codes to control dispatching.
         */
        $exitCode = $context->getExitCode();
        if ($exitCode >= 200 && $exitCode < 300) {
            
            /*
             * PreFilters have the ability to change the current route
             * so we lets refresh the route key before getting any more specs
             */
            $key = $context->getRouteKey();
            $filterSpec = $this->getRouteSpec('intercept-filter',$key);
            $accessSpec = $this->getRouteSpec('access', $key);
            
            if ($accessSpec->isInternalOnlyAccess()) {
                $err = "Access to this route is denied: internal use only";
                throw new LogicException($err, 401);
            }

            Dispatcher::dispatch($context);

            if ($filterSpec->isPostFilteringEnabled()) {
                $context = $this->runPostFilters($filterSpec, $context);
            }
        }

        return $context;
    }

    /**
     * @param   RouteInterceptFilterSpecInterface   $spec
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function runPreFilters(RouteInterceptFilterSpecInterface $spec,
                                  MvcContextInterface $context)
    {
        $chain = $this->getPreChain();
        if ($spec->isExcludedPreFilters()) {
            $chain->removeFilters($spec->getExcludedPreFilters());
        }

        if ($spec->isPreFilters()) {
            $chain->loadFilters($spec->getPreFilters());    
        }

        return $chain->applyFilters($context);
    }

    /**
     * @param   RouteInterceptFilterSpecInterface   $spec
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function runPostFilters(RouteInterceptFilterSpecInterface $spec,
                                   MvcContextInterface $context)
    {
        $chain = $this->getPostChain();
        if ($spec->isExcludedPostFilters()) {
            $chain->removeFilters($spec->getExcludedPostFilters());
        }

        if ($spec->isPostFilters()) {
            $chain->loadFilters($spec->getPostFilters());    
        }

        return $chain->applyFilters($context);
    }

    /**
     * @param   string  $cat
     * @param   string  $key    route key
     * @return  object | false 
     */
    protected function getRouteSpec($cat, $key)
    {
        return RouteRegistry::getRouteSpec($cat, $key);
    }
}
