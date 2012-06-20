<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details. 
 */
namespace Appfuel\App;

use Appfuel\View\ViewInterface,
    Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * 
 */
interface AppHandlerInterface
{
    /**
     * @return  AppFactoryInterface
     */
    public function getAppFactory();

    /**
     * @param   AppFactoryInterface $factory
     * @return  AppHandler
     */
    public function setAppFactory(AppFactoryInterface $factory);

    /**
     * @return  RequestUriInterface
     */
    public function createUriFromServerSuperGlobal();

    /**
     * @param   string
     * @return  RequestUriInterface
     */
    public function createUri($str);

    /**
     * @param   array    $tasks
     * @return  AppHandler
     */
    public function findRoute($key, $format = null);

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
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface $context
     * @param   bool    $isHttp
     * @return  null
     */
    public function outputHttpContext(MvcRouteDetailInterface $route, 
                                      MvcContextInterface $context,
                                      $version = '1.1');
    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function outputConsoleContext(MvcRouteDetailInterface $route,
                                         MvcContextInterface $context);

    /**
     * @param   MvcContextInterface        $context
     * @return  AppHandler
     */
    public function runAction(MvcContextInterface $context);

    /**
     * @param   array    $tasks
     * @return  AppHandler
     */
    public function initialize(array $taks = null);
    public function runTasks(array $tasks);

    /**
     * @return    TaskHandlerInterface
     */
    public function getTaskHandler();

    /**
     * @param   TaskHandlerInterface $handler
     * @return  AppHandler
     */
    public function setTaskHandler(TaskHandlerInterface $handler);
}
