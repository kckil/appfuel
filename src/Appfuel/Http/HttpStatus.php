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
 * Maps the http status code to status text. Used in the http response mainly
 * to build the status line text of the first header that is sent back for
 * the http response.
 */
class HttpStatus implements HttpStatusInterface
{
    /**
     * Data to be sent in this response
     * @var string
     */
    protected $code = null;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * Http status codes to use a defaults
     * @var array
     */
    static protected $statusMap = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * @param   int $status status code of the response
     * @return  HttpStatus
     */
    public function __construct($code = 200, $text = null)
    {
        if (! is_numeric($code)) {
            $err  = "invalid http status code, must be a number or string ";
            $err .- "that is a number";
            throw new InvalidArgumentException($err);
        }

        $code = (int)$code;
        if (! is_int($code) || $code < 100 || $code >= 600) {
            throw new DomainException("invalid http status code");
        }
        
        $this->code = $code;
        if (null === $text) {
            $text = '';
            if (isset(self::$statusMap[$code])) {
                $text = self::$statusMap[$code];
            }
        }
            
        $this->text = $text;
    }

    /**
     * @return  array
     */
    public static function getStatusMap()
    {
        return self::$statusMap;
    }

    /**
     * @return  int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return  string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        return "{$this->getCode()} {$this->getText()}";
    }
}
