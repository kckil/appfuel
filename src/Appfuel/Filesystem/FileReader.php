<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use InvalidArgumentException;

/**
 * @see interface
 */
class FileReader implements FileReaderInterface
{
    /**
     * @var FileFinderInterface 
     */
    protected $finder = null;

    /**
     * @param   string | FileFinderInterface $path 
     * @return  FileReader
     */
    public function __construct(FileFinderInterface $finder)
    {
        $this->setFileFinder($finder);
    }

    /**
     * @return  FileFinderInterface
     */
    public function getFileFinder()
    {
        return $this->finder;
    }

    /**
     * @param   FileFinderInterface $finder
     * @return  FileReader
     */
    public function setFileFinder(FileFinderInterface $finder)
    {
        $this->finder = $finder;
        return $this;
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
     * @return  bool
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
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                $result = 'maximum stack depth exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $result = 'unexpected control char found';
                break;
            case JSON_ERROR_SYNTAX:
                $result = 'syntax error, malformed JSON';
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

        return file($full, $flags); 
    }

    /**
     * @param   mixed   $token
     * @return  bool
     */
    public function isFailureToken($token)
    {
        return FileReaderInterface::READ_FAILURE === $token;
    }

    /**
     * @return  string
     */
    public function getFailureToken()
    {
        return FileReaderInterface::READ_FAILURE;
    }
}
