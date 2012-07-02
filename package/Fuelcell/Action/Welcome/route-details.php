<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Fuelcell\Action\Welcome;

return array(
    'welcome' => array(
        'is-public' => true,
        'pattern'   => '/^welcome/',
        'action'    => 'WelcomeAction',
        'namespace' => __NAMESPACE__,
        'view-pkg'  => 'fuelcell:page.welcome'
    )
);
