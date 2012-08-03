<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use LogicException,
    DomainException,
    InvalidArgumentException,
    Appfuel\Http\HttpInput,
    Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpRequest,
    Appfuel\Http\HttpResponse,
    Appfuel\Http\HttpRequestInterface,
    Appfuel\Http\HttpResponseInterface;

/**
 */
class WebApplication extends AppKernel implements WebInterface
{
    /**
     * @param   HttpRequestInterface    $request
     * @return  HttpResponseInterface
     */
    public function handle(HttpRequestInterface $request)
    {
        return $this->createHttpResponse('hello world');
    }

    /**
     * @return  HttpRequestInterface
     */
    public function createBrowserInputParams($method)
    {
        $valid = array('get', 'post', 'put', 'delete');
        if (! in_array($method, $valid, true)) {
            $err  = "invalid http method: must be one of the following ";
            $err .= "-(get, put, post, delete)";
            throw new DomainException($err);
        }

        $get     = $_GET;
        $post    = $_POST;
        $put     = array();
        $delete  = array();
        $files   = (isset($_FILES))   ? $_FILES   : array();
        $cookies = (isset($_COOKIE))  ? $_COOKIE  : array();
        $session = (isset($_SESSION)) ? $_SESSION : array();
        
        if ('put' === $method) {
            $put = $post;
            $post = array();
        }
        else if ('delete' === $method) {
            $delete = $post;
            $post = array();
        }

        $params = array(
            'get'       => $get,
            'post'      => $post,
            'put'       => $put,
            'delete'    => $delete,
            'files'     => $files,
            'cookies'   => $cookies,
            'session'   => $session,
        );

        return $params;
    }

    /**
     * @param   string  $method 
     * @param   array   $params
     * @return  HttpInput
     */
    public function createHttpInput($method, array $params = array())
    {
        return new HttpInput($method, $params);
    }
    
    /**
     * @return  HttpRequest
     */
    public function createStandardHttpRequest()
    {
        return $this->createHttpRequest($_SERVER);
    }

    /**
     * @param   array   $params
     * @return  HttpRequest
     */
    public function createHttpRequest(array $params)
    {
        return new HttpRequest($params);
    }

    /**
     * @param   string  $data
     * @param   int     $status
     * @param   array   $hdrs
     */
    public function createHttpResponse($data=null, $status=null, $hdrs=null)
    {
        return new HttpResponse($data, $status, $hdrs);
    } 

    /**
     * @return  HttpOutputInterface
     */
    public function httpOutput(HttpResponseInterface $response)
    {
        HttpOutput::render($response);
    }

    /**
     * @param   array   $list
     * @return  AppInitializer
     */
    public function restrictAccessTo(array $list, $msg)
    {
        foreach ($list as $ip) {
            if (! is_string($ip) || empty($ip)) {
                $err  = "each item in the list must be a non empty string ";
                $err .= "that represents an ip address";
                throw new DomainException($err);
            }
        }

        if (! is_string($msg)) {
            $err = "script restriction message must be a string";
            throw new InvalidArgumentException($err);
        }

        if (isset($_SERVER['HTTP_CLIENT_IP']) ||
            isset($_SERVER['HTTP_X_FORWARDED_FOR']) ||
            ! in_array(@$_SERVER['REMOTE_ADDR'], $list)) {
            header('HTTP/1.0 403 Forbidden');
            exit($msg);
        }
    }
}
