<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\DbSource;

use DomainException,
    InvalidArgumentException,
    Appfuel\View\FileTemplate,
    Appfuel\Filesystem\FileFinder;

class SqlTemplate extends FileTemplate
{
    /**
     * @param    mixed    $file 
     * @param    DbMapInterface $map
     * @param    PathFinderInterface    $pathFinder
     * @return    FileViewTemplate
     */
    public function __construct($file, DbMapInterface $dbMap = null)
    {
        if (null !== $dbMap) {
            $this->setDbMap($dbMap);
        }
        
        parent::__construct($file);
    }

    /**
     * @return    DbMapInterface
     */
    public function getDbMap()
    {
        return $this->dbMap;
    }

    /**
     * Build the template file indicated by key into string. Use data in
     * the dictionary as scope
     *
     * @param   string  $key    template file identifier
     * @param   array   $data   used for private scope
     * @return  string
     */
    public function build()
    {   
        $file = $this->getFile();
        $finder = new FileFinder('resource');

        $absolute = $finder->getPath($file);
        if (! $finder->fileExists($absolute, false)) {
            $err = "template file not found at -($absolute)";
            throw new DomainException($err, 404);
        }

        $data = $this->getAll();
        $compositor = new SqlFileCompositor();
        $compositor->setDbMap($this->getDbMap());
        return $compositor->compose($absolute, $data);
    }

    /**
     * @param     DbMapInterface    $map
     * @return    null
     */
    protected function setDbMap(DbMapInterface $map)
    {
        $this->dbMap = $map;
        return $this;
    }
}
