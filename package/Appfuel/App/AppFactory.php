<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use RunTimeException,
    Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpResponse,
    Appfuel\Console\ArgParser,
    Appfuel\Console\ConsoleOutput,
    Appfuel\Console\ConsoleOutputInterface,
    Appfuel\View\ViewBuilder,
    Appfuel\Kernel\Task\TaskHandler,
    Appfuel\Kernel\Mvc\MvcFront,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcDispatcher,
    Appfuel\Kernel\Mvc\MvcDispatcherInterface,
    Appfuel\Kernel\Mvc\InterceptChain,
    Appfuel\Kernel\Mvc\InterceptChainInterface;

/**
 * Create all object required to implement appfuels take on the mvc pattern
 */
class AppFactory implements AppFactoryInterface
{
    /**
     * @return ConfigHandler
     */    
    public function createConfigHandler()
    {
        return new ConfigHandler();
    }

    /**
     * @return  ConfigBuilder
     */
    public function createConfigBuilder()
    {
        return new ConfigBuilder();
    }
   
    /**
     * @param   ConsoleOutputInterface  $output
     * @return  ConsoleHandlerInterface
     */ 
    public function createConsoleHandler(ConsoleOutputInterface $output = null)
    {
        return new ConsoleHandler($output);
    }

    /**
     * We look for query string first because the path info in the request uri
     * gets lost with rewrite rules.
     * 
     * @return  RequestUri
     */
    public function createUriFromServerSuperGlobal()
    {
        $isQueryString = isset($_SERVER['QUERY_STRING']) &&
                         is_string($_SERVER['QUERY_STRING']) &&
                         ! empty($_SERVER['QUERY_STRING']);

        if ($isQueryString) {
            $str = '?' . $_SERVER['QUERY_STRING'];
        }
        else if (isset($_SERVER['REQUEST_URI'])) {
            $str = $_SERVER['REQUEST_URI'];
        }
        else {
            $err  = 'ConextBuilder failed: php super global ';
            $err .= '$_SERVER[\'REQUEST_URI\']';
            throw new RunTimeException("$err is not set");
        }

        return $this->createUri($str);
    }

    /**
     * @return  RequestUri
     */
    public function createUri($str)
    {
        return new RequestUri($str);
    }

    /**
     * @param   string  $method
     * @param   array   $params
     * @return  AppInput
     */
    public function createInput($method, array $params = array())
    {
        return new AppInput($method, $params);
    }

    /**
     * @param   array $data
     * @return  AppInput
     */
    public function createConsoleInput(array $data)
    {
        $parser = $this->createCliArgParser();
        return $this->createInput('cli', $parser->parse($data));
    }

    /**
     * @return  CliParser
     */
    public function createCliArgParser()
    {
        return new ArgParser();
    }

    /**
     * @param   RequestUriInterface $uri 
     * @return  AppInput
     */
    public function createRestInputFromBrowser(RequestUriInterface $uri = null)
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }
        else if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        else {
            $err = 'http request method was not set';
            throw new LogicException($err);
        }

        if (! is_string($method) || empty($method)) {
            $err = "http method must be a non empty string";
            throw new DomainException($err);
        }
        $method = strtolower($method);
        $valid  = array('get', 'put', 'post', 'delete');
        if (! in_array($method, $valid, true)) {
            $err  = "invalid http method: must be one of the following ";
            $err .= "-(get, put, post, delete)";
            throw new DomainException($err);
        }

        $get    = array();
        $put    = array();
        $post   = array();
        $delete = array();
        
        switch ($method) {
            case 'post'  : $post   = $_POST; break;
            case 'put'   : $put    = $_POST; break;
            case 'delete': $delete = $_POST; break;
        }   
            
        if (null !== $uri) {
            $get = $uri->getParams();
        }
        else {
            $get = $_GET;
        }
        $params = array(
            'get'     => $get,
            'post'    => $post,
            'put'     => $put,
            'delete'  => $delete,
            'files'   => (isset($_FILES))   ? $_FILES    : array(),
            'cookie'  => (isset($_COOKIE))  ? $_COOKIE    : array(),
            'session' => (isset($_SESSION)) ? $_SESSION : array(),
        );

        return $this->createInput($method, $params);
    }

    /**
     * @return    AppInput
     */
    public function createEmptyInput()
    {
        $method = 'get';
        if (PHP_SAPI === 'cli') {
            $method   = 'cli';
        }
        
        return $this->createInput($method, array());
    }

    /**
     * @param    string    $key
     * @return    MvcRouteDetailInterface
     */
    public function createRouteDetail($key)
    {
        return MvcRouteManager::getRouteDetail($key);
    }

    /**
     * @param   string  $key
     * @param   AppInputInterface $input
     * @return  AppContext
     */
    public function createContext($key, AppInputInterface $input)
    {
        return new MvcContext($key, $input);
    }

    /**
     * @return  MvcViewBuilderInterface
     */
    public function createViewBuilder()
    {
        return new ViewBuilder();
    }

    /**
     * @param    MvcDispatcherInterface $dispatcher
     * @param    InterceptChainInterface $preChain
     * @param    InterceptChainInterface $postChain
     * @return    MvcFront
     */
    public function createFront(MvcDispatcherInterface $dispatcher = null,
                                InterceptChainInterface $preChain  = null,
                                InterceptChainInterface $postChain = null)
    {
        $preList = AppRegistry::get('pre-filters', array());
        if (null === $preChain) {
            $preChain = new InterceptChain();
        }

        if (is_array($preList) && ! empty($preList)) {
            $preChain->loadFilters($preList);
        }

        $postList = AppRegistry::get('post-filters', array());
        if (null === $postChain) {
            $postChain = new InterceptChain();
        }

        if (is_array($postList) && ! empty($postList)) {
            $postChain->loadFilters($postList);
        }

        if (null === $dispatcher) {
            $dispatcher = new MvcDispatcher();
        }

        return new MvcFront($dispatcher, $preChain, $postChain);
    }

    /**
     * @return    MvcDispatcher
     */
    public function createDispatcher()
    {
        return new MvcDispatcher();
    }

    /**    
     * @return    TaskHandler
     */
    public function createTaskHandler()
    {
        return new TaskHandler();
    }

    public function createHttpResponse($data, 
                                       $status,
                                       $version = null,
                                       array $headers = null)
    {
        return new HttpResponse($data, $status, $version, $headers);
    }

    /**
     * @return    HttpOutputInterface
     */
    public function createHttpOutput()
    {
        return new HttpOutput();
    }

    public function createConsoleOutput()
    {
        return new ConsoleOutput();
    }
}
