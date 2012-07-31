<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

/**
 * The path finder uses a path collection to allow the developer to specify
 * a name or key to identify the path rather than the path itself.
 */
interface PathFinderInterface extends FileFinderInterface
{
    /**
     * @return  PathCollectionInterface
     */
    public function getPathCollection();

    /**
     * @param   PathCollectionInterface $paths
     * @return  PathFinderInterface
     */
    public function setPathCollection(PathCollectionInterface $paths);
}
