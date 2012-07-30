<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

interface DependencyInterface
{
    /**
     * @return  string
     */
    public function getServiceKey();

    /**
     * @return  bool
     */
    public function isServiceAvailable();

    /**
     * @return  mixed
     */
    public function getService();

    /**
     * @param   mixed   $service
     * @return  Dependency
     */
    public function setService($service);

    /**
     * @param   mixed   $service
     * @return  bool
     */
    public function isValidService($service);

    /**
     * @return  Dependency
     */
    public function clearService();
}
