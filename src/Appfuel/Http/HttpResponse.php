<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;


use DomainException,
    InvalidArgumentException;

/**
 * Manage http headers, protocol, and status. 
 */
class HttpResponse implements HttpResponseInterface
{
    /**
     * List of headers to be sent 
     * @var HttpHeaderListInterface
     */
    protected $headers = null;

    /**
     * Data to be sent in this response
     * @var string
     */
    protected $content = '';

    /**
     * Http protocal being used
     * @var string
     */
    protected $version = '1.0';

    /**
     * Holds the details of the http status
     * @var HttpStatusInterface
     */
    protected $status = 200;

    /**
     * @param   mixed   $data 
     * @param   int     $status 
     * @param   array|HttpHeaderListInterface   $headers 
     * @return  HttpResponse
     */
    public function __construct($data = null, $status = null, $headers = null)
    {
        if (null != $data) {
            $this->setContent($data);
        }
        
        if (null === $headers) {
            $headers = new HttpHeaderList();
        }
        else if (is_array($headers)) {
            $headers = new HttpHeaderList($headers);
        }
        else if (! $headers instanceof HeaderListInterface) {
            $err  = 'header list must be an array or an object them implments ';
            $err .= '-(Appfuel\\Http\\HttpHeaderListInterface)';
            throw new DomainException($err);   
        }
        $this->setHeaderList($headers);

        if (null === $status) {
            $status = 200;
        }
            
        $this->setStatus($status);
    }

    /**
     * @return  HttpHeaderListInterface
     */
    public function getHeaderList()
    {
        return $this->headers;
    }

    /**
     * @param   HttpHeaderListInterface $list
     * @return  null
     */
    public function setHeaderList(HttpHeaderListInterface $list)
    {
        $this->headers = $list;
        return $this;
    }

    /**
     * Assign the content to be used and convert it to a string in necessary
     * 
     * @param   mixed   scalar|object   $data
     * @return  HttpResponse
     */
    public function setContent($data)
    {
        if (! $this->isValidContent($data)) {
            $type = gettype($data);
            $err  = "Http response content must be a string or an object ";
            $err .= "implementing __toString(). parameter type -($type)";
            throw new DomainException($err);
        }

        $this->content = (string) $data;
        return $this;
    }

    /**
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param   mixed   $data
     * @return  bool
     */
    public function isValidContent($data)
    {
        if (null !== $data && 
            ! is_scalar($data) && ! is_callable(array($data, '__toString'))) {
            return false;
        }

        return true;
    }

    /**
     * @return  string
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }

    /**
     * @param   string  $version
     * @return  null
     */
    public function setProtocolVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return  HttpHeaderStatus
     */
    public function getStatusLine()
    {
        return "HTTP/{$this->getProtocolVersion()} {$this->getStatus()}";
    }

    /**
     * @return  int
     */
    public function getStatusCode()
    {
        return $this->getStatus()
                    ->getCode();
    }

    /**
     * @return  string
     */
    public function getStatusText()
    {
        return $this->getStatus()
                    ->getText();
    }

    /**
     * @return  int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param   HttpResponseStatus
     * @return  HttpResponse
     */
    public function setStatus($status)
    {
        if (! ($status instanceof HttpStatusInterface)) {
            $status = new HttpStatus($status);
        }    
        $this->status = $status;

        return $this;
    }

    /**
     * @return  array
     */
    public function getAllHeaders()
    {
        return $this->getHeaderList()
                    ->getAllHeaders();
    }

    /**
     * @param   string  $header
     * @return  HttpResponse
     */
    public function addHeader($header)
    {
        $this->getHeaderList()
             ->addHeader($header);

        return $this;
    }

    /**
     * @param   array   $headers
     * @return  HttpResponse
     */
    public function loadHeaders(array $headers) 
    {
        $this->getHeaderList()
             ->loadHeaders($headers);

        return $this;
    }
}
