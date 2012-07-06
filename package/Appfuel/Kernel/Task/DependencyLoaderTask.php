<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
    Appfuel\ClassLoader\ManualClassLoader;

/**
 * Manual load dependent php classes into memory
 */
class DependencyLoaderTask extends StartupTask
{
    protected $keys = array(
        'depend-files',
        'depend-classes',
        'depend-lib-classes'
    );

    /**
     * @return  bool
     */
    public function execute()
    {
        $result = false;
        $params = $this->getParamData(); 
        if ($params->exists('depend-files')) {
            $list = $params->get('depend-files');
            if (! is_array($list)) {
                $err  = "a dependency file list was declared but was not ";
                $err .= "an array";
                throw new DomainException($err);
            }

            foreach ($list as $data) {
                if (is_string($data)) {
                    $file = $data;
                    $isPkgPath = true;
                }
                else if (is_array($data)) {
                    $file = current($data);
                    $isPkgPath = next($data);
                }
                else {
                    $err  = "a dependency can be a string, which when used ";
                    $err .= "assumes AF_CODE_PATH will be prepended to each ";
                    $err .= "path, or an array where the first item is the ";
                    $err .= "path to file holding the dependency map and the ";
                    $err .= "second item is flag used to determine if ";
                    $err .= "AF_CODE_PATH will be used";
                    throw new DomainException($err);
                }

                $full = AF_BASE_PATH . DIRECTORY_SEPARATOR . $file;
                ManualClassLoader::loadCollectionFromFile($full, $isPkgPath);
            }
            $result = true;
        }

        if ($params->exists('depend-pkg-classes')) {
            $list = $params->get('depend-pkg-classes');
            if (! is_array($list)) {
                $err  = "list of dependency classes was declared but is not ";
                $err .= "an array";
                throw new DomainException($err);
            }

            foreach ($list as $className => $path) {
                ManualClassLoader::loadClass($className, $path);                
            }

            $result = true;
        }

        if ($params->exists('depend-classes')) {
            $list = $params->get('depend-classes');
            if (! is_array($list)) {
                $err  = "list of dependency classes was declared but is not ";
                $err .= "an array";
                throw new DomainException($err);
            }

            foreach ($list as $className => $path) {
                ManualClassLoader::loadClass($className, $path, false);                
            }
            $result = true;
        }

        return $result;
    }
}
