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
     * @param   FileFinderInterface
     * @return  FileHandler
     */
    public function setFileFinder(FileFinderInterface $finder)
    {
        $this->finder = $finder;
        return $this;
    }
}
