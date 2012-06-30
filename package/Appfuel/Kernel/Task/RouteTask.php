<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
    Appfuel\App\AppRegistry,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Kernel\Route\RouteFactory,
    Appfuel\Kernel\Route\RouteRegistry;

/**
 * Ensure all routes specifications, all patterns, and url group map have 
 * been added to the registry. The RoutePatternSpec (pattern) is added in
 * two places because its stored as a spec and it is needed to create a 
 * categorized list of route patterns based on url groups.
 */
class RouteTask extends StartupTask
{
    /**
     * @return  null
     */
    public function execute()
    {
        $path = AppRegistry::getAppPath();
        
        $finder = new FileFinder(null, false);
        $reader = new FileReader(new FileFinder(null, false));
        $groups = $reader->import($path->get('url-groups', true, true));
        if (is_array($groups)) {
            RouteRegistry::setGroupMap($groups);
        }

        $routes = $reader->decodeJsonAt($path->get('routes-build', true, true));
        if (! $routes) {
            $err = $reader->getLastJsonError();
            throw new DomainException("route startup task: $err"); 
        }

        $specList = array(
            'access'            => 'access',
            'action'            => 'action',
            'input-validation'  => 'inputValidation',
            'intercept-filter'  => 'interceptFilter',
            'startup'           => 'startup',
            'view'              => 'view',
            'pattern'           => 'pattern',
        );

        foreach ($routes as $key => $spec) {
            $spec['route-key'] = $key;
            $list = RouteFactory::createRouteSpecs($specList, $spec);
            foreach ($list as $cat => $routeSpec) {
                RouteRegistry::addRouteSpec($key, $cat, $routeSpec);
            }

            $pattern = $list['pattern'];
            RouteRegistry::addRouteSpec($key, 'pattern', $pattern);
            RouteRegistry::addPattern($pattern);            
        }
    }
}
