<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Validate;

use DomainException,
    InvalidArgumentException,
    Appfuel\Kernel\Task\StartupTask,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader;

/**
 * Locate the validation map and added it to the validation factory
 */
class ValidationStartupTask extends StartupTask
{
    /**
     * @var array
     */
    protected $keys = array('validation-file');
    
    /**
     * @return  bool
     */
    public function execute()
    {
        $params = $this->getParamData();
        $file = $params->get('validation-file', 'app/validation-map.php');
        if (! is_string($file) || empty($file)) {
            $err  = "validation file is the relative path to the ";
            $err .= "file holding validation mapping and must be a non ";
            $err .= "empty string";
            throw new DomainException($err);
        }
        $finder = new FileFinder($file);
        $reader = new FileReader($finder);
        
        $map = $reader->import();
        if (isset($map['validators']) && is_array($map['validators'])) {
            ValidationFactory::setValidatorMap($map['validators']);
        }

        if (isset($map['filters']) && is_array($map['filters'])) {
            ValidationFactory::setFilterMap($map['filters']);
        }

        if (isset($map['coordinator'])) {
            ValidationFactory::setCoordinatorClass($map['coordinator']);
        }
    }
}
