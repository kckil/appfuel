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
     * @return  bool
     */
    public function require($path)
    {
        $finder = $this->getFileFinder();
        if ($full = $finder->getExisting($path)) {
            return require $full;
        }

        return $finder->getFileAccessFailureToken();
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function requireOnce($path)
    {
        $finder = $this->getFileFinder();
        if ($full = $finder->getExisting($path)) {
            return require_once $full;
        }

        return $finder->getFileAccessFailureToken();
    }

    /**
     * @param   string  $path
     * @param   bool    $isAssoc
     * @param   int     $depth
     * @param   int     $options
     * @return  array | object
     */
    public function decodeJsonAt($path, $assoc=true, $depth=512, $options=0)
    {
        $finder  = $this->getFileFinder();
        $content = $this->getContents($path);
        if ($finder->isFileAccessFailureToken($content)) {
            return $content;
        }

        return json_decode($content, $assoc, $depth, $options);
    }

    /**
     * @return  string
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
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @param   int     $offset
     * @param   int     $max
     * @return  string | false when does not exist
     */
    public function getContents($path = null, $offset = null, $max = null)
    {
        if (null !== $offset && ! is_int($offset) || $offset < 0) {
            $err = 'offset must be a int that is greater than zero';
            throw new InvalidArgumentException($err);
        }

        if (null !== $max && ! is_int($max) || $max < 0) {
            $err = 'max must be a int that is greater than zero';
            throw new InvalidArgumentException($err);
        }

        if ($offset > $max) {
            $err = 'offset can not be larger than max';
            throw new DomainException($err);
        }

        $finder = $this->getFileFinder();
        if (! $full = $finder->getExistingPath($path)) {
            return $finder->getFileAccessFailureToken();
        }

        /*
         * file_get_contents will return an empty string if the last param is
         * null
         */
        if (null === $max) {
            return file_get_contents($full, false, null, $offset);
        }
        
        return file_get_contents($full, false, null, $offset, $max);
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $file
     * @param   int     $flags = 0
     * @return  array | false when not found
     */
    public function getContentsAsArray($file, $flags = 0)
    {
        if (! is_int($flags)) {
            $err = 'failed to get file contents: flags must be an int';
            throw new InvalidArgumentException($err);
        }

        $finder = $this->getFileFinder();
        if (! $full = $finder->getExistingPath($path)) {
            return $finder->getFileAccessFailureToken();
        }

        return file($full, $flags); 
    }
}
