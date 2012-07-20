<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

/**
 * Value object used to represent an http status. The code and text should
 * be mapped so that all that is needed is the constructor to except the
 * immutable input. You could implement public setters to do this but appfuel
 * prefers small immutable value objects (reduces side effects)
 */
interface HttpStatusInterface
{
    /**
     * @return  string
     */
    public function getCode();
    
    /**
     * @return  bool
     */
    public function getText();

    /**
     * @return  string
     */
    public function __toString();
}
