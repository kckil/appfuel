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
    * @param    MvcContextInterface $context
    * @return   null
    */
    static public function dispatch(MvcContextInterface $context)
    {
        $key    = $context->getRouteKey();
        $input  = $context->getInput();
        $method = $input->getMethod();

        $spec   = self::getActionSpec($key);
        $action = $spec->createAction($method);
        if (! ($action instanceof MvcActionInterface)) {
            $err  = "failed to dispatch to -($key) mvc action does not ";
            $err .= "implement Appfuel\Kernel\Mvc\MvcActionInterface";
            throw new DomainException($err, 404);
        }

        $spec = self::getAccessSpec($key);
        if (! $spec->isAccessAllowed($context->getAclCodes(), $method)) {
            $err = 'user request is not allowed: insufficient permissions';
            throw new DomainException($err);
        }

        $spec = self::getValidationSpec($key, 'validation');
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

    /**
     * @throws    DomainException
     * @param    string    $key
     * @return    RouteActionSpecInterface
     */
    static protected function getActionSpec($key)
    {
        $spec = self::getSpec($key, 'action');
        if (! $spec instanceof RouteActionSpecInterface) {
            $err  = "failed to dispatch to -($key) route spec was not found ";
            $err .= "or does not implement -(Appfuel\Kernel\\Route\\Route";
            $err .= "ActionSpecInterface)";
            throw new DomainException($err, 404);
        }

        return $spec;
    }

    /**
     * @throws    DomainException
     * @param     string    $key
     * @return    RouteAccessSpecInterface
     */
    static protected function getAccessSpec($key)
    {
        $spec = self::getSpec($key, 'access');
        if (! $spec instanceof RouteAccessSpecInterface) {
            $err  = "failed to dispatch to -($key) route spec was not found ";
            $err .= "or does not implement -(Appfuel\Kernel\\Route\\Route";
            $err .= "AccessSpecInterface)";
            throw new DomainException($err, 404);
        }

        return $spec;
    }

    /**
     * @throws    DomainException
     * @param     string    $key
     * @return    RouteValidationSpecInterface
     */
    static protected function getValidationSpec($key)
    {
        $spec = self::getSpec($key, 'validation');
        if (! $spec instanceof RouteValidationSpecInterface) {
            $err  = "failed to dispatch to -($key) route spec was not found ";
            $err .= "or does not implement -(Appfuel\Kernel\\Route\\Route";
            $err .= "ValidationSpecInterface)";
            throw new DomainException($err, 404);
        }

        return $spec;
    }

    /**
     * @param    string    $key
     * @param    string    $type
     * @return   mixed
     */
    static protected function getSpec($key, $type)
    {
        return RouteRegistry::getSpec($key, $type);
    }

}
