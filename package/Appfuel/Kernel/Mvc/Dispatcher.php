<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

use DomainException,
    Appfuel\Kernel\Route\RouteRegistry;

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
    * @param    MvcContextInterface $context
    * @return   null
    */
    static public function dispatch(MvcContextInterface $context)
    {
        $key    = $context->getRouteKey();
        $input  = $context->getInput();
        $method = $input->getMethod();

        $spec   = self::getRouteSpec('action', $key);
        $action = $spec->createAction($method);
        if (! ($action instanceof ExecutableInterface)) {
            $err  = "failed to dispatch to -($key) mvc action does not ";
            $err .= "implement Appfuel\Kernel\Mvc\MvcActionInterface";
            throw new DomainException($err, 404);
        }

        $spec = self::getRouteSpec('access', $key);
        $acl  = $context->getAcl();
        if (! $spec->isAccessAllowed($acl->getCodes(), $method)) {
            $err = 'user request is not allowed: insufficient permissions';
            throw new DomainException($err);
        }

        $spec = self::getRouteSpec('input-validation', $key);
        if ($spec->isInputValidation() && $spec->isSpecList()) {
            if (! $input->isSatisfiedBy($spec->getSpecList())) {
                if ($spec->isThrowOnValidationError()) {
                    $errors = $input->getErrorString();
                    $code   = $spec->getValidationErrorCode();
                    throw new DomainException($errors, $code);
                }
            }
        }

        $action->execute($context);

        return $context;
    }

    /**
     * @throws  DomainException
     * @param   string  $cat
     * @param   string  $key
     * @return  object
     */
    static protected function getRouteSpec($cat, $key)
    {
        if (! ($route = RouteRegistry::getRouteSpec($cat, $key))) {
            $err = "route specification -($cat) not found for -($key)";
            throw new DomainException($err);
        }

        return $route;
    }
}
