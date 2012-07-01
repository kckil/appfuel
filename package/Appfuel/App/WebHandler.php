<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    Appfuel\Kernel\Mvc\MvcContextInterface;

class WebHandler extends AppHandler implements WebHandlerInterface
{
    /**
     * @return string
     */
    public function createRequestUri()
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
     * @param   array $routeData
     * @return  MvcContext
     */
    public function createWebContext(array $route)
    {
        if (! isset($route['route-key'])) {
            $err = 'route key is required but not found';
            throw new DomainException($err);
        }
        $key = $route['route-key'];
        $viewSpec = $this->getRouteSpec('view', $key);
        if (! $viewSpec) {
            $err = "route view specification could not be found for -($key)";
            throw new DomainException($err);
        }

        $format = $viewSpec->getDefaultFormat();
        if (isset($route['format'])) {
            $format = $route['format'];
            unset($route['format']);
        }
        $method = $this->getRequestMethod();
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
            'get'     => $get, 
            'post'    => $post, 
            'put'     => $put, 
            'delete'  => $delete, 
            'files'   => $files, 
            'cookie'  => $cookies, 
            'session' => $session, 
            'route'   => $route, 
        );
    
        $factory = $this->getAppFactory();
        $input = $factory->createInput($method, $params);
        $context = $factory->createContext($route['route-key'], $input);
        
        if (is_string($format)) {
            $context->setViewFormat($format);
        }

        return $context;
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
