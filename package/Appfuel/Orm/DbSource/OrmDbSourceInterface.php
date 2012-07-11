<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\DbSource;

use Appfuel\Orm\OrmCriteriaInterface,
    Appfuel\DataSource\Db\DbSourceInterface;

interface OrmDbSourceInterface extends DbSourceInterface
{
    /**
     * @param   array   $list
     * @return  DSource
     */
    public function loadSqlTemplatePaths(array $list);

    /**
     * @param   string  $name
     * @return  string | false when not found
     */
    public function getSqlTemplatePath($name);

    /**
     * @param   string  $name   used to file the template path
     * @param   stirng  $path   path to sql template
     * @return  DbSource
     */
    public function addSqlTemplatePath($key, $path);

    /**
     * @param   string  $file
     * @param   array   $data
     * @return  string
     */
    public function loadSql($key,
                            OrmCriteriaInterface $criteria = null,
                            $isBuild = true);

    /**
     * @param   string  $file
     * @return  FileViewTemplate
     */
    public function createSqlTemplate($file);
}
