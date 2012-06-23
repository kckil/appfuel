<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

interface RouteAccessSpecInterface
{
    /**
     * @param   array  $spec
     * @return  RouteAccessInterface
     */
    public function __construct(array $spec);

    /**
     * @return  bool
     */
    public function isPublicAccess();

    /**
     * @return  bool
     */
    public function isInternalOnlyAccess();

    /**
     * @return  bool
     */
    public function isAclAccessIgnored();

    /**
     * @return  bool
     */
    public function isAclForEachMethod();

    /**
     * @param   string  $code
     * @return  bool
     */
    public function isAccessAllowed($codes, $method = null);
}
