<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Event;

interface EventDataInterface
{
    /**
     * @return  bool
     */
    public function isPropagation();

    /**
     * @return EventDataInterface
     */
    public function enablePropagation();

    /**
     * @return EventDataInterface
     */
    public function disablePropagation();

    /**
     * @return  string
     */
    public function getName();

    /**
     * @param   string  $name
     * @return  EventDataInterface
     */
    public function setName($name);
}
