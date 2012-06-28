<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use Appfuel\View\ViewInterface,
    Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

interface AppHandlerInterface
{
    /**
     * @return  AppFactoryInterface
     */
    public function getAppFactory();

    /**
     * @param   string $key
     * @param   AppInputInterface   $input
     * @return  MvcContextInterface
     */
    public function createContext($key, AppInputInterface $input);

    /**
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface     $context
     * @return  AppHandler
     */
    public function initializeApp(MvcRouteDetailInterface $route, 
                                  MvcContextInterface $context);

    /**
     * @param   MvcRouteDetailInterface    $route
     * @param   MvcContextInterface        $context
     * @param   string     $format
     * @return  AppHandler
     */
    public function setupView(MvcRouteDetailInterface $route, 
                              MvcContextInterface $context, 
                              $format = null);

    public function composeView(MvcRouteDetailInterface $route,
                                MvcContextInterface $context);

    /**
     * @param   MvcContextInterface        $context
     * @return  AppHandler
     */
    public function runAction(MvcContextInterface $context);

    public function runTasks(array $tasks);
}
