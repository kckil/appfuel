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
    Appfuel\ClassLoader\ManualClassLoader,
    Appfuel\Config\ConfigRegistry,
    Appfuel\Kernel\TaskHandlerInterface,
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
     * Used to run individual startup startegies
     * @var TaskHandlerInterface
     */
    protected $taskHandler = null;

    /**
     * @return  AppDetailInterface
     */
    public function getAppDetail()
    {
        return AppRegistry::getAppDetail();
    }
    
    /**
     * Create the app factory and task handler and define constants
     * 
     * @param   array    $tasks 
     * @return  AppHandler
     */
    public function initialize(array $tasks = null)
    {
        $default = 'Appfuel\App\AppFactory';
        $class   = ConfigRegistry::get('app-factory-class', $default);
        if (! is_string($class) || empty($class)) {
            $err = "app factory class must be a non empty string";
            throw new DomainException($err);
        }

        if (! class_exists($class, false)) {
            $err  = "the app factory class should be added to the ";
            $err .= "kernel dependency file because it is needed before the ";
            $err .= "the autoloader is in use";
            throw new LogicException($err);
        }

        $factory = new $class();
        if (! $factory instanceof AppFactoryInterface) {
            $err  = "app factory -($class) must implment Appfuel\Kernel";
            $err .= "\AppFactoryInterface";
            throw new LogicException($err);
        }

        $handler = $factory->createTaskHandler();
        $this->factory = $factory;
        $this->setTaskHandler($handler);

        if (null !== $tasks) {
            $this->runTasks($tasks);
        }

        return $this;
    }

    /**
     * @return  AppFactoryInterface
     */
    public function getAppFactory()
    {
        return AppRegistry::getAppFactory();
    }


    /**
     * @param   array    $tasks
     * @return  RouteDetailInterface
     */
    public function findRoute($key, $format = null)
    {
        $factory = $this->getAppFactory();

        if ($key instanceof RequestUriInterface) {
            $format = $key->getRouteFormat();
            $key    = $key->getRouteKey();
        }
        else if (! is_string($key)) {
            $err  = 'first param must be a string or an object that ';
            $err .= 'implments Appfuel\Kernel\Mvc\RequestUriInterface';
            throw new DomainException($err);
        }

        $route = $factory->createRouteDetail($key);
        if (! empty($format)) {
            $route->setFormat($format);
        }

        return $route;
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
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface $context
     * @param   bool    $isHttp
     * @return  null
     */
    public function outputHttpContext(MvcRouteDetailInterface $route, 
                                      MvcContextInterface $context,
                                      $version = '1.1')
    {
        $content = $this->composeView($route, $context);
        $status  = $context->getExitCode();
        $headers = $context->get('http-headers', null); 
        if (! is_array($headers) || empty($headers)) {
                $headers = null;
        }
        $factory = $this->getAppFactory();
        $response = $factory->createHttpResponse(
            $content, 
            $status, 
            $version, 
            $headers
        );

        $output = $factory->createHttpOutput();
        $output->render($response);
    }

    /**
     * @param    MvcContextInterface $context
     * @return    null
     */
    public function outputConsoleContext(MvcRouteDetailInterface $route,
                                         MvcContextInterface $context)
    {
        $content = $this->composeView($route, $context);
        $output  = $this->getAppFactory()
                        ->createConsoleOutput();
        
        $output->render($content);
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

    /**
     * @return    TaskHandlerInterface
     */
    public function getTaskHandler()
    {
        return $this->taskHandler;
    }

    /**
     * @param   TaskHandlerInterface $handler
     * @return  AppHandler
     */
    public function setTaskHandler(TaskHandlerInterface $handler)
    {
        $this->taskHandler = $handler;
        return $this;    
    }
}
