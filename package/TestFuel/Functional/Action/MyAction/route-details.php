<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Functional\Action\MyAction;

return [                                                                         
    'my-action' => [                                         
        'is-public' => 'false',                                                     
        'pattern'   => '/myaction/',                                          
        'action'    => 'MyActionController',                               
        'namespace' => __NAMESPACE__,                                            
    ]                                                                       
];  
