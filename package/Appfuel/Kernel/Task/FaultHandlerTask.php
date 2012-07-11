<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
	Appfuel\Kernel\FaultHandlerInterface;

/**
 * register php error_handler and exception_handler
 */
class FaultHandlerTask extends StartupTask
{
    /**
     * @var array
     */
    protected $keys = array(
	    'php-error-handler',
		'php-exception-handler',
		'fault-handler-class',
    );

	/**
	 * @return  bool
	 */
	public function execute()
	{
	    $params = $this->getParamData();	
		if ($params->exists('fault-handler-class')) {
			$class = $params->get('fault-handler-class');
			if (! is_string($class) || empty($class)) {
				$err = "fault-handler-class must be a non empty string";
				throw new DomainException($err);
			}
			
			$handler = new $class();
			if (! $handler instanceof FaultHandlerInterface) {
				$err  = 'fault handler must implment -(Appfuel\Kernel';
				$err .= '\FaultHandlerInterface';
				throw new DomainException($err);
			}
			
            set_error_handler(array($handler, 'handleError'));
            set_exception_handler(array($handler, 'handleException'));
			return true;
		}

        $result = false;
		if ($params->exists('php-error-handler')) {
			$data = $params->get('php-error-handler');
			if (! is_array($data)) {
				$err  = "error handler data must be an array of at most ";
				$err .= "two items: 1) callable handler 2) bitwise mask ";
				$err .= "used to limit which errors are triggered";
				throw new DomainException($err);
			}

			$func = current($data);
			$mask = next($data);
			if (is_int($mask) && $mask > 0) {
				set_error_handler($func, $mask);
			}
			else {
				set_error_handler($func);
			}
            $result = true;
		}

		if ($params->exists('php-exception-handler')) {
			$func = $params->get('php-exception-handler');
			if (! is_callable($func)) {
				$err  = "exception handler data must be callable";
				throw new DomainException($err);
			}
			set_error_handler($func);
            $result = true;
		}

        return $result;
	}
}
