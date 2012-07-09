<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    Appfuel\Http\HttpInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Route\MatchedRouteInterface;

class WebHandler extends AppHandler implements WebHandlerInterface
{
    /**
     * @throws  DomainException when strict
     * @param   bool    $isStrict
     * @return  MatchedRouteInterface | false
     */
    public function lookupRouteInQueryString($isStrict = true)
    {
        $key = $this->getRouteKeyFromHttpGet();
        $format = $this->getRouteFormatFromHttpGet();
        if (false === $format) {
            $format = null;
        }
       
        return $this->findRoute($key, $format, $isStrict);
    }

    /**
     * @return  string | false
     */
    public function getRouteKeyFromHttpGet()
    {
        if (! isset($_GET['routekey'])) {
            return false;
        }

        return filter_input(INPUT_GET, 'routekey', FILTER_SANITIZE_STRING);
    }

    /**
     * @return  string | false
     */
    public function getRouteFormatFromHttpGet()
    {
        if (! isset($_GET['routeformat'])) {
            return false;
        }

        return filter_input(INPUT_GET, 'routeformat', FILTER_SANITIZE_STRING);
    }


    /**
     * @return  bool
     */
    public function isQueryString()
    {
        $key = 'QUERY_STRING';
        if (isset($_SERVER[$key]) && ! empty($_SERVER[$key])) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        if (! isset($_SERVER['REQUEST_URI'])) {
            $err = "The request uri has not be set in the server super global";
            throw new LogicException($err);
        }

        $uri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        if (false === $uri) {
            $err = "The uri given is invalid";
            throw new DomainException($err);
        }
        
        return urldecode($uri);
    }

    /** 
     * @throws  LogicException 
     * @return  string 
     */ 
    public function getRequestMethod() 
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
 
        return strtolower($method);  
    }

    /**
     * The routing system will capture parameters from the request uri or even
     * duplicate GET params if the old school urls are used. These params need 
     * to be added to the app input. Input data can also be declared in the 
     * route action spec. All these inputs are collected under the key 'route'
     *
     * @param   MatchedRouteInterface   $route
     * @param   string  $method
     * @return  AppInputInterface
     */
    public function createWebInput(MatchedRouteInterface $route,
                                   $method, 
                                   array $additional = null)
    {
        $spec = $this->getRouteSpec('action', $route->getRouteKey()); 
        $factory = $this->getAppFactory();
        $params  = $this->createWebInputParams($method);

        /*
         * route captures will always override action input declarations
         */
        $data['route'] = $route->getCaptures();

        /*
         * put a copy of the route captures in the GET collection because
         * in the old school urls they would have been there. This allows 
         * legacy code to continue to work at the cost of some array bloat
         */
        $params['get'] = array_merge($params['get'], $data['route']);
        if ($spec->isInput()) {
            $data['route'] = array_merge($spec->getInput(), $data['route']);
        }

        if (null === $additional) {
            $additional = array();
        }
        
        $params = array_merge($params, $data, $additional);
        return $factory->createHttpInput($method, $params);
    }

    /**
     * @param   string  $key,
     * @param   AppInputInterface   $input
     * @param   mixed   $view
     * @return  MvcContextInterface
     */
    public function createWebContext($key, HttpInputInterface $in, $view = null)
    {
        $factory = $this->getAppFactory();
        $context = $factory->createContext($key, 'http', $in);
        
        if (null !== $view) {
            $context->setView($view);
        }     
        
        return $context;
    }

    /**
     * @param   string  $key    route key
     * @return  MvcViewInterface
     */
    public function createAppView(MatchedRouteInterface $route)
    {
        $spec = $this->getRouteSpec('view', $route->getRouteKey());
        $format = ($route->isFormat()) ? 
                    $route->getFormat() : $spec->getDefaultFormat();

        $data = array('format' => $format);
        if ($spec->isViewPackage()) {
            $data['pkg'] = $spec->getViewPackage();
        }        
    
        $factory = $this->getAppFactory();
        return $factory->createAppView($data);
    }

    /**
     * @param   array   $data
     * @return  array
     */
    public function createWebInputParams($method)
    {
        $valid  = array('get', 'put', 'post', 'delete');
        if (! in_array($method, $valid, true)) {
            $err  = "invalid http method: must be one of the following ";
            $err .= "-(get, put, post, delete)";
            throw new LogicException($err);
        }
       
        /*
         * we check if these super globals are set because you have the
         * ability to not use the with the ini setting variable_order which
         * sets the order of EGPCS (Env, Get, Post, Cookie, and Server) 
         * the reset we check becuase is causes no harm
         */ 
        $post    = (isset($_POST))    ? $_POST    : array();
        $get     = (isset($_GET))     ? $_GET     : array();
        $files   = (isset($_FILES))   ? $_FILES   : array();
        $cookies = (isset($_COOKIE))  ? $_COOKIE  : array();
        $session = (isset($_SESSION)) ? $_SESSION : array();

        $put    = array();
        $delete = array();
        if ('put' === $method) {
            $put  = $post;
            $post = array();
        }
        else if ('delete' === $method) {
            $delete  = $post;
            $post = array();
        }

        $params = array(
            'get'       => $get, 
            'post'      => $post, 
            'put'       => $put, 
            'delete'    => $delete, 
            'files'     => $files, 
            'cookie'    => $cookies, 
            'session'   => $session,
        );

        return $params;
    }

    /**
     * @param   string  $data
     * @param   array   $headers
     * @param   string  $ver
     * @return  null
     */
    public function outputHttp($data, $code, array $hdrs = null, $ver = '1.1')
    {
        if (! is_string($data)) {
            $err = "http content must be a string";
            throw new DomainException($err);
        }

        $factory  = $this->getAppFactory();
        $output   = $factory->createHttpOutput();
        $response = $factory->createHttpResponse($data, $code, $ver, $hdrs);
        $output->render($response);
    }
}
