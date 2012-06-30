<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    RunTimeException,
    InvalidArgumentException,
    Appfuel\View\ViewInterface,
    Appfuel\Kernel\Route\Router,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
    Appfuel\Kernel\Mvc\MvcFactoryInterface;

/**
 * 
 */
class AppHandler implements AppHandlerInterface
{
    /**
     * @return  AppPathInterface
     */
    public function getAppPath()
    {
        return AppRegistry::getAppPath();
    }
    
    /**
     * @return  AppFactoryInterface
     */
    public function getAppFactory()
    {
        return AppRegistry::getAppFactory();
    }
    
    /**
     * @param   string  $uri
     * @return  array | false
     */
    public function findRoute($uri)
    {
        return Router::findRoute($uri);
    }

    /**
     * @param   string $key
     * @param   AppInputInterface   $input
     * @return  MvcContextInterface
     */
    public function createContext($key, AppInputInterface $input)
    {
        return $this->getAppFactory()
                    ->createContext($key, $input);
    }

    /**
     * @param   MvcRouteDetailInterface    $route
     * @param   MvcContextInterface        $context
     * @return  AppHandler
     */
    public function initializeApp(MvcRouteDetailInterface $route, 
                                  MvcContextInterface $context)
    {
        $handler = $this->loadTaskHandler();
        $handler->kernelRunTasks($route, $context);
        return $this;
    }

    /**
     * @param   MvcRouteDetailInterface    $route
     * @param   MvcContextInterface        $context
     * @param   string                    $format
     * @return  AppRunner
     */
    public function setupView(MvcRouteDetailInterface $route, 
                              MvcContextInterface $context, 
                              $format = null)
    {

        if (empty($format)) {
            $format = $route->getFormat();
        }

        $this->getAppFactory()
             ->createViewBuilder()
             ->setupView($context, $route, $format);

        return $this;
    }

    public function composeView(MvcRouteDetailInterface $route,
                                MvcContextInterface $context)
    {
        if ($route->isViewDisabled()) {
            return '';
        }

        $view = $context->getView();
        if (is_string($view)) {
            $result = $view;
        }
        else if ($view instanceof ViewInterface) {
            $result = $view->build();
        }
        else if (is_callable(array($view, '__toString'))) {
            $result =(string) $view;
        }
        else {
            $err  = "view must be a string or an object the implements ";
            $err .= "Appfuel\View\ViewInterface or an object thtat implemnts ";
            $err .= "__toString";
            throw new DomainException($err);
        }
    
        return $result;
    }

    /**
     * @param    MvcContextInterface        $context
     * @return    AppRunner
     */
    public function runAction(MvcContextInterface $context)
    {
        $context = $this->getAppFactory()
                        ->createFront()
                        ->run($context);

        return $context;
    }

    /**
     * @param    array    $tasks
     * @return    AppRunner
     */
    public function runTasks(array $tasks)
    {
        $this->getTaskHandler()
             ->runTasks($tasks);

        return $this;
    }
}
