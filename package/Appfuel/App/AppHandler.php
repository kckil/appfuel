<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    Appfuel\View\ViewInterface,
    Appfuel\Kernel\Route\Router,
    Appfuel\Kernel\Route\MatchedRouteInterface,
    Appfuel\Kernel\Route\RouteRegistry,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
    Appfuel\Kernel\Mvc\MvcFactoryInterface;

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
     * @param   string  $cat
     * @param   string  $key    route key
     * @return  object | false 
     */ 
    public function getRouteSpec($cat, $key)
    {
        if (! $spec = RouteRegistry::getRouteSpec($cat, $key)) {
            $err = "route specificiation -($cat) was not found for -($key)";
            throw new LogicException($err);
        }

        return $spec;
    }

    /**
     * @param   string  $uri
     * @return  array | false
     */
    public function matchRoute($uri, $method, $isStrict = true)
    {
        $route = Router::matchRoute($uri, $method);
        if (false === $isStrict) {
            return $route;
        }

        return $this->validateRoute($route, $uri, $method);
    }

    /**
     * @throws  DomainException by default
     * @param   strin   $key
     * @param   string  $format
     * @param   bool    $isStrict
     * @return  MatchedRouteInterface
     */
    public function findRoute($key, $format = null, $isStrict = true)
    {
        $route = Router::findRoute($key, $format);
        if (false === $isStrict) {
            return $route;
        }

        return $this->validateRoute($route);
    }
    
    /**
     * @param   mixed   $route
     * @return  bool
     */
    public function isMatchedRoute($route)
    {
        return $route instanceof MatchedRouteInterface;
    }

    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function runStartupTasks(MvcContextInterface $context)
    {
        $key  = $context->getRouteKey();
        $spec = $this->getRouteSpec('startup', $key);
        if (! $spec) {
            $err = "route startup specification was not found for -($key)";
            throw new DomainException($err);
        }

        if ($spec->isStartupDisabled()) {
            return;
        }

        $tasks = array();
        if (! $spec->isIgnoreConfigStartupTasks()) {
            $tasks = AppRegistry::get('startup-tasks', array());
            if (! is_array($tasks)) {
                $err  = "tasks saved in the app registry -(startup-tasks) ";
                $err .= "must be any array";
                throw new DomainException($err);
            }
            
            if ($spec->isExcludedStartupTasks()) {
                $excluded = $route->getExcludedStartupTasks();
                foreach ($excluded as $exclude) {
                    foreach ($tasks as $index => $target) {
                        if ($exclude === $target) {
                            unset($tasks[$index]);
                        }
                    }
                }
                $tasks = array_values($tasks);
            }
        }

        if ($spec->isStartupTasks()) {
            $routeTasks = $spec->getStartupTasks();
            if ($spec->isPrependStartupTasks()) {
                $tasks = array_merge($routeTasks, $tasks);
            }
            else {
                $tasks = array_merge($tasks, $routeTasks);
            }
        }

        $handler = AppRegistry::getTaskHandler();
        $handler->runTasks($tasks, $context);
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

    /**
     * @throws  DomainException
     * @param   MvcContextInterface $context
     * @return  string
     */
    public function composeView(MvcContextInterface $context)
    {
        $spec = $this->getRouteSpec('view', $context->getRouteKey());
        if ($spec->isViewDisabled()) {
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
        $front = $this->createFrontController();
        return $front->run($context);
    }

    public function createFrontController()
    {
        $factory = $this->getAppFactory();
        
        $default  = array();
        $filters  = AppRegistry::getWhen('pre-filters', 'array', $default);
        $preChain = $factory->createInterceptChain();
        $preChain->loadFilters($filters);
        
        $filters   = AppRegistry::getWhen('post-filters', 'array', $default);
        $postChain = $factory->createInterceptChain();
        $postChain->loadFilters($filters);
        
        return $factory->createFrontController($preChain, $postChain);
    }

    /**
     * @throws  DomainException
     * @param   MatchedRouteInterface $route
     * @return  MatchedRouteInterface
     */
    protected function validateRoute($route, $uri = null, $method = null)
    {
        if (! $this->isMatchedRoute($route)) {
            $err  = "Request to this application could not be resolved for ";
            $err .= "-($uri, $method)";
            throw new DomainException($err, 404);
        }

        return $route;
    }

}
