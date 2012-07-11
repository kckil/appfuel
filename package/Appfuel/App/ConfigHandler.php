<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use DomainException,
    RunTimeException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface;

/**
 * Loads config data into the configuration registry. The data can be from a
 * php file that returns an array or a json file, the data can also be just 
 * an array. A section can also be isolated and used as the config instead of
 * of the whole config. If section is used and an array key 'common' exists
 * the loader will try to merge common into the section. 
 */
class ConfigHandler implements ConfigHandlerInterface
{
    /**
     * @var FileReader
     */
    protected $reader = null;

    /**
     * The default reader is set to accept an absolute path because the normal
     * method of operation is to use the AppDetail to get the path to the 
     * config build file.
     *
     * @param   FileReaderInterface $reader
     * @return  ConfigLoader
     */
    public function __construct(FileReaderInterface $reader = null)
    {
        if (null === $reader) {
            $reader = new FileReader(new FileFinder(null, false));
        }

        $this->reader = $reader;
    }

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader()
    {
        return $this->reader;
    }

    /**
     * @param   string  $path
     * @param   string  $path
     * @return  bool
     */
    public function loadFile($path, $isReplace = true)
    {
        $data = $this->getFileData($path);
        if (true === $isReplace) {
            $this->set($data);
            return;
        }
            
        $this->load($data, $section);
    }

    /**
     * Read a json file or any php file. When php files are used it expects 
     * the file will return an array of config data
     *
     * @param   string  $path   relative path to the config file
     * @return  array
     */
    public function getFileData($path)
    {
        if (! is_string($path) || empty($path)) {
            $err = "path to config must be a none empty string";
            throw new DomainException($err);
        }

        $reader = $this->getFileReader();
        if (false !== strpos($path, '.json')) {
            $data = $reader->decodeJsonAt($path, true);
            if (! $data) {
                $full = $reader->getFileFinder()
                               ->getPath($path);
                $msg = $reader->getLastJsonError();
                $err = "could not load config file at -($full): $msg ";
                throw new RunTimeException($err);
            }
        }
        else {
            $data = $reader->import($path, false);
            if (! is_array($data)) {
                $err  = "could not find config at -($path) or the file is ";
                $err .= "not returning the expected format of an array";
                throw new DomainException($err);
            }
        }
        
        return $data;
    }
}
