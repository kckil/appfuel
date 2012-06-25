<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use SplFileInfo,
	RunTimeException,
	InvalidArgumentException;

class FileManager implements FileManagerInterface
{
    /**
     * Flag used to determine if the app base path is enabled
     * @var bool
     */
    protected $isBase = true;

    /**
     * @var FileFinderInterface
     */
    protected $srcFinder = null;

    /**
     * @param   mixed   $src    string | FileFinderInterface
     * @return  FileManager
     */
    public function __construct($src = null, $isBase = true)
    {
        if (null !== $src) {
            $src = new FileFinder();
        }
        $this->setSourceFinder($src);

        if (false === $isBase) {
            $this->disableBasePath();
        }
    }

    /**
     * @return  bool
     */
    public function isBasePathEnabled()
    {
        return $this->isBase;
    }

    /**
     * @param   string  $path   
     * @return  FileManager
     */
    public function setSourceRootPath($path)
    {
        $this->getSourceFinder()
             ->setRootPath($path);

        return $this;
    }

    /**
     * @param   string  $relativeRoot   root path relative to base path
     * @return  FileManager
     */
    public function enableBasePath($relativeRoot = null)
    {
        $this->isBase = true;
        
        $finder = $this->getSourceFinder();
        if ($finder->isBasePath()) {
            if (null !== $relativeRoot) {
                $finder->seteRoot($relativeRoot);
            }
            return $this;
        }

        $this->setSourceFinder($this->createFileFinder($relativeRoot, true));
        return $this;
    }

    /**
     * @param   string  $root   new root path of the finder
     * @return  FileManager
     */
    public function disableBasePath($root = null)
    {
        $this->isBase = false;
        $finder = $this->getSourceFinder();
        if (! $finder->isBasePath()) {
            if (null !== $root) {
                $finder->setRootPath($root);
            }

            return $this;
        }

        $this->setSourceFinder($this->createFileFinder($root, false));
        return $this;
    }

    /**
     * @param   string  $rootPath
     * @param   bool    $isBase
     * @return  FileFinder
     */
    public function createFileFinder($rootPath = null, $isBase = true)
    {
        return new FileFinder($rootPath, $isBase);
    }

    /**
     * @return  FileFinderInterface
     */
    public function getSourceFinder()
    {
        return $this->srcFinder;
    }

    /**
     * @param   FileFinderInterface $finder
     * @return  null
     */
    protected function setSourceFinder($src)
    {
        if (is_string($src)) {

        }
        $this->srcFinder = $src;
    }
}
