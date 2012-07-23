<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

/**
 * The FileHandler is used to encapsulate the most common file operations into
 * behind a simple interface. 
 */
interface FileHandlerInterface
{
    /** 
     * @param   string  $path
     * @param   bool    $isOnce
     * @return  mixed    
     */ 
    public function importScript($path, $isOnce = false);

    /**
     * @param   string  $path 
     * @param   bool    $isOnce
     * @return  mixed 
     */ 
    public function includeScript($path, $isOnce = false); 

    /**
     * @param   string  $path 
     * @param   bool    $isAssoc 
     * @param   int     $depth 
     * @param   int     $options 
     * @return  array | object 
     */ 
    public function readJson($path, $assoc=true, $depth=512, $options=0);

    /**
     * @return  string | false
     */
    public function getLastJsonError();

    /**
     * @param   string  $path 
     * @return  string | false when does not exist 
     */ 
    public function read($path);

    /**
     * @param   string  $path
     * @return  string
     */ 
    public function readSerialized($path);

    /** 
     * @param   string  $file
     * @param   int     $flags = 0 
     * @return  array | false when not found
     */ 
    public function readLinesIntoArray($path, $flags = 0);

    /** 
     * @param   string  $path
     * @param   string  $data 
     * @param   int     $flags 
     * @return  int 
     */ 
    public function write($path, $data, $flags = 0);

    /** 
     * @param   string  $path
     * @param   string  $data 
     * @param   int     $flags 
     * @return  int 
     */ 
    public function writeSerialized($path, $data, $flags = 0);

    /** 
     * @param    string $path 
     * @param    int    $mode 
     * @param    bool   $isRecursive 
     * @return 
     */ 
    public function createDir($path, $mode = 0755, $recursive = false);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function deleteDir($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function deleteFile($path);
 
    /**
     * @return  string | null
     */
    public function getRootPath();

    /**
     * @param   string  $path
     * @return  FileHandler
     */
    public function setRootPath($path);

    /**
     * @param   string  $path 
     * @return  string
     */
    public function getPath($path = null);

    /** 
     * @throws  DomainException 
     * @throws  InvalidArgumentException 
     * @param   string  $path 
     * @return  string | false if path does not exist 
     */ 
    public function getExistingPath($path);

    /**
     * @param   string  $path
     * @return  string
     */
    public function getPathBase($path);

    /**
     * @param   string  $path
     * @return  string
     */    
    public function getDirPath($path);

    /**
     * @param   string  $path
     * @return  Unix timestamp | false
     */
    public function getLastModifiedTime($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isWritable($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path);

    /**
     * @return  FileFinderInterface
     */
    public function getFileFinder();

    /**
     * @param   FileFinderInterface
     * @return  FileHandler
     */
    public function setFileFinder(FileFinderInterface $finder);

    /**
     * @return  FileHandler
     */
    public function throwExceptionOnFailure();

    /**
     * @return  FileHandler
     */
    public function disableExceptionsOnFailure();

    /**
     * @return  bool
     */
    public function isThrowOnFailure();

    /**
     * @return  string
     */
    public function getFailureMsg();

    /**
     * @throws  InvalidArgumentException
     * @param   string  $msg
     * @return  FileHandler
     */
    public function setFailureMsg($msg);

    /**
     * @return  scalar
     */
    public function getFailureCode();

    /**
     * @param   scalar  $code
     * @return  FileHandler
     */
    public function setFailureCode($code);

    /**
     * @return  mixed
     */
    public function getFailureReturnValue();

    /**
     * @param   mixed   $value
     * @return  FileHandler
     */
    public function setFailureReturnValue($value);
}