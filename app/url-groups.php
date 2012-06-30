<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */

/*
 * list of regex patters that when matched will point to the name of a group
 */
return array(
    '/^admin/'   => 'admin',
    '/^routes/'  => 'route',
    '/^domains/' => 'domain',
    '/^users/'   => 'users',
    '/(some|other)\/group\/(users|guests)/' => 'user-groups',
);
