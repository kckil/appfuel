<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use Appfuel\DataStructure\ArrayDataInterface;

interface ServiceBuilderInterface
{
    /**
     * @return  string
     */
    public function getServiceKey();

    /**
     * @param   string  $key
     * @return  ServiceBuilder
     */
    public function setServiceKey($key);

    /**
     * @return  ArrayDataInterface
     */
    public function getSettings();

    /**
     * @param   ArrayDataInterface  $data
     * @return  StartupTask
     */
    public function setSettings(ArrayDataInterface $data);

    /**
     * @return  bool
     */
    public function isValidSettings(ArrayDataInterface $data);

    /**
     * @return  bool
     */
    public function isError();

    /**
     * @return  string
     */
    public function getError();

    /**
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function build(DIContainerInterface $container);

    /**
     * List of keys to pull out of the registry
     *
     * @return    array
     */
    public function getKeys();

    /**
     * @param   array    $keys
     * @return  ServiceBuilder
     */
    public function setKeys(array $keys);
}
