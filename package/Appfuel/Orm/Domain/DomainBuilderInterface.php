<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\Domain;

/**
 * Data Builder turns already mapped data into domain objects, strings, 
 * json or any other format required.
 */
interface DomainBuilderInterface
{
    /**
     * @param   string  $key    used to determine which object to create
     * @return  DomainModelInterface
     */
    public function createDomainObject($key);

    /**
     * @param   string  $key
     * @param   array   $data  
     * @return  DomainModel
     */
    public function buildDomain($key, array $data);
}
