<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

interface FileHandlerInterface
{
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
     * @param   string  $path                                                    
     * @param   bool    $isOnce                                                  
     * @return  mixed                                                            
     */                                                                          
    public function importScript($path, $isOnce = false);

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
}
