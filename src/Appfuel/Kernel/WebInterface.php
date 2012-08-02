<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use InvalidArgumentException;

interface WebInterface extends AppKernelInterface
{
    /**
     * @param   array   $list
     * @return  AppInitializer
     */
    public function restrictAccessTo(array $list, $msg);
}
