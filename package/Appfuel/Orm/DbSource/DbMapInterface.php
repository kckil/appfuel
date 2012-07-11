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
interface DbMapInterface
{
    /**
     * @param   array   $maps
     * @return  null
     */
    public function initialize(array $maps);

    /**
     * @param   array    $data
     * @return  DbTableMap
     */
    public function createTableMap(array $data);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isTableMap($key);

    /**
     * @return array
     */
    public function getAllTableMaps();

    /**
     * @param   string  $key
     * @return  DbTableMapInterface
     */
    public function getTableMap($key);

    /**
     * @param   string  $key
     * @param   DbTableMapInterface $map
     * @return  SqlFileCompositor
     */
    public function addTableMap($key, DbTableMapInterface $map);

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function loadTableMaps(array $list);

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function setTableMaps(array $list);

    /**
     * @return  SqlFileCompositor
     */
    public function clearTableMaps();
}
