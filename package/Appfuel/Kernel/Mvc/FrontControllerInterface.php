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
 * The front controller is used build the intialize context, run the pre
 * intercepting filters, dispatch to the mv action, handle any errors,
 * run post filters and output the results.
 */
interface FrontControllerInterface
{    
    /**
     * @return    InterceptChainInterface
     */
    public function getPreChain();

    /**
     * @return    InterceptChainInterface
     */
    public function getPostChain();

    /**
     *  
     * @param    string    $strategy    console|ajax|htmlpage
     * @return    int
     */
    public function run(MvcContextInterface $context);

    /**
     * @param   RouteInterceptFilterSpecInterface  $spec
     * @param   MvcContextInterface     $context
     * @return  MvcContextInterface
     */
    public function runPreFilters(RouteInterceptFilterSpecInterface $spec,
                                  MvcContextInterface $context);

    /**
     * @param    MvcRouteDetailInterface $detail
     * @param    MvcContextInterface        $context
     * @return    MvcContextInterface
     */
    public function runPostFilters(RouteInterceptFilterSpecInterface $spec,
                                   MvcContextInterface $context);
}
