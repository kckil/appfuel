<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use DomainException,
    RunTimeException,
    Appfuel\Filesystem\FileHandler;

/**
 */
class ConfigHandler implements ConfigHandlerInterface
{
    /**
     * @var FileHandler
     */
    protected $fileHandler = null;

    /**
     * List of config files to be merge. This is a push down stack
     * @var array
     */
    protected $files = array();

    /**
     * The default reader is set to accept an absolute path because the normal
     * method of operation is to use the AppDetail to get the path to the 
     * config build file.
     *
     * @param   FileHandlerInterface $fileHandler
     * @return  ConfigHandler
     */
    public function __construct(FileHandler $fileHandler = null)
    {
        if (null === $fileHandler) {
            $fileHandler = new FileHandler();
        }

        $this->fileHandler = $fileHandler;
    }

    /**
     * @return  FileReaderInterface
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

}
