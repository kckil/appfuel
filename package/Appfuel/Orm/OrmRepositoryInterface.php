<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm;

interface OrmRepositoryInterface
{
    /**
     * @return mixed
     */
    public function getDataSource();

    /**
     * @param   array   $data
     * @return  Dictionary
     */
    public function createDictionary(array $data = null);
}
