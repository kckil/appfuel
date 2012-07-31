<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use LogicException;

/**
 * The path finder uses a path collection to allow the developer to specify
 * a name or key to identify the path rather than the path itself.
 */
class PathFinder extends FileFinder implements PathFinderInterface
{
    /**
     * Collection of paths this handler will get its paths from
     * @var PathCollectionInterface
     */
    protected $paths = null;

    /**
     * @param   PathCollectionInterface $paths
     * @return  PathHandler
     */
    public function __construct(PathCollectionInterface $paths)
    {
        $this->setPathCollection($paths);
        unset($this->root);
    }

    /**
     * @return  PathCollectionInterface
     */
    public function getPathCollection()
    {
        return $this->paths;
    }

    /**
     * @param   PathCollectionInterface $paths
     * @return  PathHandler
     */
    public function setPathCollection(PathCollectionInterface $paths)
    {
        $this->paths = $paths;
        return $this;
    }

    /**
     * @return  string
     */
    public function getRoot()
    {
        return $this->getPathCollection()
                    ->getRoot();
    }

    /**
     * @param   string  $path
     * @return  PathFinder
     */
    public function setRoot($path)
    {
        $this->getPathCollection()
             ->setRoot($path);

        return $this;
    }

    /**
     * @throws  LogicException
     */
    public function clearRoot()
    {
        $err = "path finders can not have their root path cleared";
        throw new LogicException($err); 
    }

    /**
     * Path finders are required to have their root path be absolute paths
     *
     * @return  true
     */
    public function isRootAbsolute()
    {
        return true;
    }

    /**
     * @param   string  $name
     * @return  string
     */
    public function getPath($name = null)
    {
        $paths = $this->getPathCollection();
        if (null === $name) {
            return $paths->getRoot();
        }

        return $paths->get($name);
    }
}
