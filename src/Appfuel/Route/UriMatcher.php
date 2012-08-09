<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use OutOfBoundsException,
    InvalidArgumentException;

class UriMatcher implements UriMatcherInterface
{
    /**
     * @var string
     */
    protected $uriPath = null;

    /**
     * Used in hierarchical matches where current uri contains only the part
     * of the uri path that has not been matched.
     * @var string
     */
    protected $currentUri = null;

    /**
     * @var string
     */
    protected $uriScheme = null;

    /**
     * @var string
     */
    protected $httpMethod = null;

    /**  
     * @var array
     */ 
    protected $captures = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct(array $data)
    {
        if (! isset($data['uri-path'])) {
            $err = '-(uri-path) is required bu not given';
            throw new OutOfBoundsException($err);
        }
        $this->setUriPath($data['uri-path']);

        if (! isset($data['uri-scheme'])) {
            $err = '-(uri-scheme) is required but not given';
            throw new OutOfBoundsException($err);
        }
        $this->setUriScheme($data['uri-scheme']);


        if (! isset($data['http-method'])) {
            $err = '-(http-method) is required but not given';
            throw new OutOfBoundsException($err);
        }
        $this->setHttpMethod($data['http-method']);
    }

    /**
     * Intended to be used in a chain of command like matches. When a match
     * occurs it the matched string will be removed and the remaining path will
     * be the current uri for the next match. Also, route spec's can define
     * params which will replace an index capture with a name. So if you capture
     * position 0 then it will be given the name located at $params[0].
     *
     * @param   string  $pattern
     * @param   array   $params
     * @return  bool
     */
    public function match($pattern, array $params = array())
    {
        $path = $this->getCurrentUri();
        $matches = array();
        if (! preg_match($pattern, $path, $matches)) {
            return false;
        }
        $this->adjustCurrentUri($path, array_shift($matches));
        $this->addCaptures($matches, $params);

        return true;
    }

    /**
     * @return  UriMatcher
     */
    public function clearCurrentUri()
    {
        $this->currentUri = null;
        return $this;
    }

    /**
     * @return  string
     */
    public function getCurrentUri()
    {
        if (null === $this->currentUri) {
            return $this->getUriPath();
        }

        return $this->currentUri;
    }

    /**
     * @return  string
     */
    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * @return string
     */
    public function getUriScheme()
    {
        return $this->uriScheme;
    }

    /**
     * @return  string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @return  array
     */
    public function getCaptures()
    {
        return $this->captures;
    }

    /**
     * @return  UriMatcher
     */
    public function clearCaptures()
    {
        $this->captures = array();
        return $this;
    }

    /**
     * @param   array   $params
     * @return  null
     */
    public function setCaptures(array $list)
    {
        $this->captures = $list;
    }

    /**
     * @param   string  $path
     * @return  null
     */
    protected function setUriPath($path)
    {
        if (! is_string($path) || empty($path)) {
            $err = "uri path must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->uriPath = $path;
    }

    /**
     * @param   string  $path
     * @return  null
     */
    protected function setUriScheme($scheme)
    {
        if (! is_string($scheme) || empty($scheme)) {
            $err = "uri scheme must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->uriScheme = $scheme;
    }

    /**
     * @param   string  $path
     * @return  null
     */
    protected function setHttpMethod($method)
    {
        if (! is_string($method) || empty($method)) {
            $err = "http method must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->httpMethod = strtoupper($method);
    }

    /**
     * @param   string  $currentUri
     * @return  string  $matchedPath
     * @return  null
     */
    protected function adjustCurrentUri($currentUri, $matchedPath)
    {
        $pos = strpos($currentUri, $matchedPath) + strlen($matchedPath);
        $this->currentUri = substr($currentUri, $pos);
    }

    /**
     * @param   array   $matches
     * @param   array   $params   manaully named captures from the spec
     * @return  null
     */
    protected function addCaptures(array $matches, array $params)
    {
        foreach ($matches as $key => $capture) {
            // manually captured and given a name by the regex
            if (is_string($key)) {
                $this->captures[$key] = $capture;
                continue; 
            }

            // name of this capture is specified by the params array
            // this is usually given by the RouteSpec 
            if (isset($params[$key])) {
                $this->captures[$params[$key]] = $capture;
            }
        }
    }
}
