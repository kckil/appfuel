<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use DomainException,
	Appfuel\Kernel\Route\RouteRegistry,
	Appfuel\Kernel\Route\RouteActionSpecInterface;

/**
 * Uses the route detail to find and create the mvc action controller based
 * based on the input method (get, post, put, delete or cli). Validate acl 
 * access with acl codes found in the context and the input method. Optionally,
 * validate input parameters using a parameter specification found in the 
 * route detail. Finally process the mvc action passing in the context.
 */
class Dispatcher implements DispatcherInterface
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null	
	 */
	static public function dispatch(MvcContextInterface $context)
	{
		$key    = $context->getRouteKey();
		$input  = $context->getInput();
		$method = $input->getMethod();
		
		$spec = RouteRegistry::getRouteObject($key, 'action');
		if (! $actionSpec instanceof RouteActionSpecInterface) {
			$err  = "failed to dispatch: route -($key) action spec not found ";
			$err .= "or does not implements the correct interface";
			throw new DomainException($err, 404);
		}

		$action = $spec->createAction($method);
        if (! ($action instanceof MvcActionInterface)) {
            $err  = 'mvc action does not implement Appfuel\Kernel\Mvc\Mvc';
            $err .= 'ActionInterface';
            throw new DomainException($err, 404);
        }

		if (! $spec->isAccessAllowed($context->getAclCodes(), $method)) {
			$err = 'user request is not allowed: insufficient permissions';
			throw new DomainException($err);
		}

		if ($spec->isInputValidation() && $spec->isValidationSpecList()) {
			if (! $input->isSatisfiedBy($spec->getValidationSpecList())) {
				if ($spec->isThrowOnValidationError()) {
					$errors = $input->getErrorString();
					$code   = $spec->getValidationErrorCode();
					throw new DomainException($errors, $code);
				}
			}
		}

		$action->process($context);
	}
}
