<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\DbSource;

use RunTimeException,
    InvalidArgumentException,
    Appfuel\View\Compositor\FileCompositorInterface;

interface SqlFileCompositorInterface
{
    public function isDbMap($key);
    public function isTableMap($key);
    public function getTableMap($key);
    public function mapColumn($key, $member);
    public function mapColumns($key, $list = null);
}
