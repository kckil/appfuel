<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\Kernel;

use DomainException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Kernel\Mvc\MvcRouteManager;

class RouteListTask extends StartupTask 
{
    /**
     * @param   array   $params
     * @return  null
     */
    public function execute(array $params = null)
    {
        $reader = new FileReader(new FileFinder('app'));
        $map = $reader->import('routes.php', true);
        //MvcRouteManager::setRouteMap($map);

        $total = count($map);
        $this->setStatus("route map set with -($total) items");
    }
}
