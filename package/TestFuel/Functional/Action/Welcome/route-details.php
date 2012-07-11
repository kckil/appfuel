<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Functional\Action\Welcome;

return [                                                                         
    'welcome' => [                                                               
        'is-public' => true,                                                     
        'pattern'   => '/^welcome/',                                             
        'action'    => 'WelcomeAction',                                          
        'namespace' => __NAMESPACE__,                                            
    ]                                                                            
];  
