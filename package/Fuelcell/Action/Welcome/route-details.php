<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Fuelcell\Action\Welcome;

/*
 * projects/99/22/33/someother-field/12/34
 */
return array(
    'welcome' => array(
        'is-public'     => true,
        'pattern'       => '^welcome/(\d+)$',
        'uri-static'    => 'welcome',
        'uri-params'    => array('projectId:8', 'folderId', 'assetId:5'),
        'uri-generator' => 'Appfuel\Kernel\Route\UrlGenerator',
        'action'    => 'WelcomeAction',
        'namespace' => __NAMESPACE__,
        'view-pkg'  => 'fuelcell:page.welcome'
    )
);
