<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use OutOfBoundsException,
    InvalidArgumentException;

/**
 */
class RouteUriSpec implements RouteUriSpecInterface
{
    /**
     * Describes the static non changing components of the uri path
     * @var string | array
     */
    protected $static = null;

    /**
     * Describes the uri path parts that are captured by the regex pattern
     * @var array
     */
    protected $params = array();

    /**
     * Describes any defaults to be used when a path part is missing
     * @var array
     */
    protected $defaults = array();

    /**
     * Alternate class or callback used to generate the uri
     */
    protected $generator = null;

    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data)
    {
        if (isset($data['uri-static'])) {
            $this->setStaticPaths($data['uri-static']);
        }

        if (isset($data['uri-params'])) {
            $this->setPathParams($data['uri-params']);
        }

        if (isset($data['uri-gernerator'])) {
            $this->setGenerator($data['uri-generator']);
        }
    }

    /**
     * @return  string
     */
    public function generate(array $parts = null)
    {
    }

    /**
     * @return  array
     */
    public function getStaticParts()
    {
        return $this->static;
    }

    /**
     * @return  array
     */
    public function getPathParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getDefault($param = null)
    {
        if (null === $param) {
            return $this->defaults;
        }

        if (! is_string($param)) {
            $err = "parameter must be specified as a string";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->defaults[$param])) {
            return null;
        }

        return $this->defaults[$param];
    }

    /**
     * @param   string  $key
     * @return  null
     */
    protected function setStaticPaths($parts)
    {
        if (is_string($parts)) {
            $parts = array($parts);
        }
        else if (! is_array($parts)) {
            $err = "the uri static path must be a string or an array";
            throw new InvalidArgumentException($err);
        }

        $defaults = array();
        foreach ($parts as $key => $part) {
            if (! is_int($key)) {
                $err = "static path key must be an integer";
                throw new DomainException($err);
            }

            if (! is_string($part)) {
                $err = "the static path of the uri must be a string";
                throw new DomainException($err);
            }
        }

        $this->static = $parts;
    }

    /**
     * @param   array   $params
     * @return  null
     */
    protected function setPathParams(array $params)
    {
        $result = array();
        foreach ($params as $key => $name) {
            if (! is_string($name) || empty($name)) {
                $err = "the path param of the uri must be a non empty string";
                throw new DomainException($err);
            }

            $parts = explode(':', $name);
            $name  = $parts[0];
            if (empty($name)) {
                $err = "when using default -(:) path param can not be empty";
                throw new DomainException($err);
            }
            $default = null;
            if (isset($parts[1])) {
                $this->defaults[$name] = $parts[1];
            }
            $results[$key] = $name;
        }

        $this->params = $results;
    }
}
