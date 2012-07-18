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
     * @var FileFinderInterface
     */
    protected $finder = null;

    /**
     * @var FileReaderInterface
     */
    protected $reader = null;


    /**
     * @param   string | FileFinderInterface $path 
     * @return  FileReader
     */
    public function __construct(FileFinderInterface $finder)
    {
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

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader()
    {
        if (! $this->reader) {
            $this->loadReader();

        }

        return $this->reader;
    }

    /**
     * @param   FileReaderInterface $reader
     * @return  FileHandler
     */
    public function setFileReader(FileReaderInterface $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    
    /**
     * Use to lazy load the reader
     * @return  null
     */
    protected function loadReader()
    {
        $this->setReader(
            FilesystemFactory::createFileReader($this->getFileFinder())
        );
    }
}
