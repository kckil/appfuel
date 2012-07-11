<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Orm\DbSource;

use InvalidArgumentException;

/**
 * The orm map is used to map database
 */
class DbMap implements DbMapInterface
{
    /**
     * List of table maps to be injected into the sql template
     * @var array
     */
    protected $maps = array();

    /**
     * @param   array   $data
     * @return  DbMap
     */
    public function __construct(array $maps = null)
    {
        if (null !== $maps) {
            $this->initialize($maps);
        }
    }

    /**
     * @param   array   $maps
     * @return  null
     */
    public function initialize(array $maps)
    {
        if ($maps === array_values($maps)) {
            $err  = 'database map must be an associative array of ';
            $err .= 'key => array of table map data';
            throw new InvalidArgumentException($err);
        }

        foreach ($maps as $key => $table) {
            $map = $this->createTableMap($table);
            $this->addTableMap($key, $map);
        }
    }

    /**
     * @param   array   $data
     * @return  DbTableMap
     */
    public function createTableMap(array $data)
    {
        return new DbTableMap($data);
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isTableMap($key)
    {
        if (is_string($key) &&
            isset($this->maps[$key]) &&
            $this->maps[$key] instanceof DbTableMapInterface) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllTableMaps()
    {
        return $this->maps;
    }

    /**
     * @param   string  $key
     * @return  DbTableMapInterface
     */
    public function getTableMap($key)
    {
        if (! $this->isTableMap($key)) {
            return false;
        }

        return $this->maps[$key];
    }

    /**
     * @param   string  $key
     * @param   DbTableMapInterface $map
     * @return  SqlFileCompositor
     */
    public function addTableMap($key, DbTableMapInterface $map)
    {
        if (! is_string($key) || empty($key)) {
            $err = 'table map key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->maps[$key] = $map;
        return $this;
    }

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function loadTableMaps(array $list)
    {
        foreach ($list as $key => $map) {
            $this->addTableMap($key, $map);
        }

        return $this;
    }

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function setTableMaps(array $list)
    {
        $this->clearTableMaps();
        $this->loadTableMaps($list);
        return $this;
    }

    /**
     * @return  SqlFileCompositor
     */
    public function clearTableMaps()
    {
        $this->maps = array();
        return $this;
    }
}
