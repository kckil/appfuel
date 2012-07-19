<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use SplFileInfo,
    DomainException,
	RunTimeException,
	InvalidArgumentException;

class FileHandler implements FileHandlerInterface
{
    /**
     * Flag used to determine if an exception should be thrown when a failure
     * occurs
     * @var bool
     */
    protected $isThrowOnFailure = false;

    /**
     * Message used when throwing an exception
     * @var string
     */
    protected $failureMsg = null;

    /**
     * Code used when throwing an exception
     * @var string|int
     */
    protected $failureCode = 500;

    /** 
     * Value returned when error occurs.
     * @var mixed
     */
    protected $failureReturnValue = false;

    /**
     * @var FileFinderInterface
     */
    protected $finder = null;

    /**
     * @param   string | FileFinderInterface $path 
     * @return  FileReader
     */
    public function __construct($path)
    {
        if ($path instanceof FileFinderInterface) {
            $finder = $path;
        }
        else {
            $finder = new FileFinder($path);
        }

        $this->setFileFinder($finder);
    }

    /** 
     * @param   string  $path
     * @param   bool    $isOnce
     * @return  mixed    
     */ 
    public function importScript($path, $isOnce = false)  
    {
        $finder = $this->getFileFinder();
        if (false === ($full = $finder->getExistingPath($path))) {
            $op  = (true === $isOnce) ? 'require_once':'require';
            $msg = "$op: $full not found";
            return $this->handleError('read', $msg);
        }
 
        return (true === $isOnce) ? require_once $full : require $full;
    } 

    /**
     * @param   string  $path 
     * @param   bool    $isOnce
     * @return  mixed 
     */ 
    public function includeScript($path, $isOnce = false) 
    {
        $finder = $this->getFileFinder(); 
        $full = $finder->getPath($path);

        return (true === $isOnce) ? include_once $full : include $full;
    }

    /**
     * @param   string  $path 
     * @param   bool    $isAssoc 
     * @param   int     $depth 
     * @param   int     $options 
     * @return  array | object 
     */ 
    public function readJson($path, $assoc=true, $depth=512, $options=0)
    {
        $result = json_decode($this->read($path), $assoc, $depth, $options);
        if (null === $result) {
            $error = $this->getLastJsonError();
            return $this->handleError('read', $error);
        }

        return $result;
    }

    /**
     * @return  string | false
     */
    public function getLastJsonError()
    { 
        switch (json_last_error())
        {
            case JSON_ERROR_DEPTH:
                $result = 'Maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $result = 'Control char error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX:
                $result = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $result = 'Malformed UTF-8 chars, possibly incorrectly encoded';
                break; 
            case JSON_ERROR_NONE: 
            default: 
                $result = false; 
        }

        return $result;
    }

    /**
     * @param   string  $path 
     * @return  string | false when does not exist 
     */ 
    public function read($path)
    { 
        $finder = $this->getFileFinder(); 
        if (false === ($full = $finder->getExistingPath($path))) { 
            return $this->handleError('read', 'file_get_contents'); 
        } 

        return file_get_contents($full); 
    }

    /**
     * @param   string  $path
     * @return  string
     */ 
    public function readSerialized($path)
    {
        return unserialize($this->read($path)); 
    }

    /** 
     * @param   string  $file
     * @param   int     $flags = 0 
     * @return  array | false when not found
     */ 
    public function readLinesIntoArray($path, $flags = 0)
    { 
        $finder = $this->getFileFinder();
        if (false === ($full = $finder->getExistingPath($path))) { 
            return $this->handleError('read', 'file');
        }
        
        return file($full, $flags); 
    }

    /** 
     * @param   string  $path
     * @param   string  $data 
     * @param   int     $flags 
     * @return  int 
     */ 
    public function write($path, $data, $flags = 0) 
    { 
        $finder = $this->getFileFinder(); 
        $result = file_put_contents($finder->getPath($path), $data, $flags);
        if (false === $result) { 
            return $this->handleError('write', 'file_put_contents'); 
        }

        return $result; 
    }

    /** 
     * @param   string  $path
     * @param   string  $data 
     * @param   int     $flags 
     * @return  int 
     */ 
    public function writeSerialized($path, $data, $flags = 0)
    {
        return $this->write($path, serialize($data), $flags);
    }

    /** 
     * @param    string $path 
     * @param    int    $mode 
     * @param    bool   $isRecursive 
     * @return 
     */ 
    public function createDir($path, $mode = 0755, $recursive = false) 
    { 
        $finder = $this->getFileFinder();
        $full = $finder->getPath($path);
        if ($finder->isDir($full)) {
            return false;
        }

        if (false === @mkdir($full, $mode, (bool)$recursive)) {
            return $this->handleError('write', 'mkdir');
        }

        return true;
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function deleteDir($path)
    {
        $finder = $this->getFileFinder();
        $full = $finder->getExistingPath($path);
        if (false === $full) {
            return true;
        }

        if (! $finder->isDir($full)) {
            return $this->handlerError('write', 'rmdir: not a dir');
        }

        $result = @rmdir($full);
        if (false === $result) {
            return $this->handleError('write', 'rmdir: could not delete dir');
        }

        return true;
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function deleteFile($path)
    {
        $finder = $this->getFileFinder();
        $full = $finder->getPath($path);
        if (! $finder->isFile($full)) {
            return false;
        }

        if (false === @unlink($full)) {
            return $this->handleError('write', 'unlink');
        }

        return true;
    }
 
    /**
     * @return  string | null
     */
    public function getRootPath()
    {
        return $this->getFileFinder()
                    ->getRootPath();
    }

    /**
     * @param   string  $path
     * @return  FileHandler
     */
    public function setRootPath($path)
    {
        $this->getFileFinder()
             ->setRootPath($path);

        return $this;
    }

    /**
     * @param   string  $path 
     * @return  string
     */
    public function getPath($path = null)
    {
        return $this->getFileFinder()
                    ->getPath($path);
    }

    /** 
     * @throws  DomainException 
     * @throws  InvalidArgumentException 
     * @param   string  $path 
     * @return  string | false if path does not exist 
     */ 
    public function getExistingPath($path)
    {
        $finder = $this->getFileFinder(); 
        $full = $finder->getPath($path); 
        if (! $finder->exists($full)) { 
            return false; 
        } 

        return $full; 
    }

    /**
     * @param   string  $path
     * @return  string
     */
    public function getPathBase($path)
    {
        return $this->getFileFinder()
                    ->getPathBase($path);
    }

    /**
     * @param   string  $path
     * @return  string
     */    
    public function getDirPath($path)
    {
        $finder = $this->getFileFinder();
        $full = $finder->getPath($path);

        return $finder->getDirPath($full);
    }

    /**
     * @param   string  $path
     * @return  Unix timestamp | false
     */
    public function getLastModifiedTime($path)
    {
        $finder = $this->getFileFinder();
        return $finder->getLastModifiedTime($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isWritable($path)
    {
        $finder = $this->getFileFinder();
        return $finder->isWritable($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path)
    {
        $finder = $this->getFileFinder();
        return $finder->isReadable($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path)
    {
        $finder = $this->getFileFinder();
        return $finder->isFile($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path)
    {
        $finder = $this->getFileFinder();
        return $finder->isDir($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path)
    {
        $finder = $this->getFileFinder();
        return $finder->isLink($finder->getPath($path));
    }

    /**
     * @return  FileFinderInterface
     */
    public function getFileFinder()
    {
        return $this->finder;
    }

    /**
     * @param   FileFinderInterface
     * @return  FileHandler
     */
    public function setFileFinder(FileFinderInterface $finder)
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * @return  FileHandler
     */
    public function throwExceptionOnFailure()
    {
        $this->isThrowOnFailure = true;
        return $this;
    }

    /**
     * @return  FileHandler
     */
    public function disableExceptionsOnFailure()
    {
        $this->isThrowOnFailure = false;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isThrowOnFailure()
    {
        return $this->isThrowOnFailure;
    }

    /**
     * @return  string
     */
    public function getFailureMsg()
    {
        return $this->failureMsg;
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $msg
     * @return  FileHandler
     */
    public function setFailureMsg($msg)
    {
        if (! is_string($msg)) {
            throw new InvalidArgumentException("failure msg must be a string");
        }

        $this->failureMsg = $msg;
        return $this;
    }

    /**
     * @return  scalar
     */
    public function getFailureCode()
    {
        return $this->failureCode;
    }

    /**
     * @param   scalar  $code
     * @return  FileHandler
     */
    public function setFailureCode($code)
    {
        if (! is_scalar($code)) {
            throw new InvalidArgumentException("failure code must be a scalar");
        }

        $this->failureCode = $code;
        return $this;
    }

    /**
     * @return  mixed
     */
    public function getFailureReturnValue()
    {
        return $this->failureReturnValue;    
    }

    /**
     * @param   mixed   $value
     * @return  FileHandler
     */
    public function setFailureReturnValue($value)
    {
        $this->failureReturnValue = $value;
        return $this;
    }

    /**
     * @throws  DomainException
     * @param   string $type
     * @return  string
     */
    protected function handleError($type, $op = 'unknown')
    {
        if (! $this->isThrowOnFailure()) {
            return $this->getFailureReturnValue();
        }

        $msg = $this->getFailureMsg();
        if (! $msg) {
            $msg = "file handler error -($type, $op) occured";
        }
        $code = $this->getFailureCode();

        throw new DomainException($msg, $code);
    }
}
