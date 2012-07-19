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
     * Flag used to determine if an exception should be thrown when the 
     * finder, reader or writer failures
     * @var bool
     */
    protected $isThrowOnFailure = false;

    /**
     * Message used when throwing an exception
     * @var string
     */
    protected $errMsg = null;

    /**
     * Code used when throwing an exception
     * @var string|int
     */
    protected $errCode = 500;

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
     * @param   string  $token
     * @return  bool
     */
    public function isReadFailureToken($token)
    {
        return $token === FileHandlerInterface::READ_FAILURE;
    }

    /**
     * @return  string
     */
    public function getReadFailureToken()
    {
        return FileHandlerInterface::READ_FAILURE;
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
            return $this->getReadFailureToken();
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
        $finder  = $this->getFileFinder();
        $content = $this->read($path);
        if ($this->isReadFailureToken($content)) {
            return $content;
        }

        return json_decode($content, $assoc, $depth, $options);
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
            return $this->getReadFailureToken(); 
        } 

        return file_get_contents($full); 
    }

    /**
     * @param   string  $path
     * @return  string
     */                                                                          
    public function readSerialized($path)
    {
        $content = $this->read($path);
        if ($this->isReadFailureToken($content)) {
            return $content;
        }
 
        return unserialize($content); 
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
            return $this->getReadFailureToken();
        }
        
        return file($full,$flags); 
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
                    ->getPath();
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
        return $this->getFinder()
                    ->getPathBase($path);
    }

    /**
     * @param   string  $path
     * @return  string
     */    
    public function getDirPath($path)
    {
        $finder = $this->getFinder();
        $full = $finder->getPath($path);

        return $finder->getDirPath($full);
    }

    /**
     * @param   string  $path
     * @return  Unix timestamp | false
     */
    public function getLastModifiedTime($path)
    {
        $finder = $this->getFinder();
        return $finder->getLastModifiedTime($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isWritable($path)
    {
        $finder = $this->getFinder();
        return $finder->isWritable($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path)
    {
        $finder = $this->getFinder();
        return $finder->isReadable($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path)
    {
        $finder = $this->getFinder();
        return $finder->isFile($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path)
    {
        $finder = $this->getFinder();
        return $finder->isDir($finder->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path)
    {
        $finder = $this->getFinder();
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
}
