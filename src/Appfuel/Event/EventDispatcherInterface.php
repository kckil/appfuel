<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Event;

interface EventDispatcherInterface
{
    /**
     * @param   string  $name   name of the event
     * @param   EventDataInterface   $event
     * @return  null
     */
    public function dispatch($name, EventDataInterface $event = null);

    /**
     * @param   string  $name
     * @param   callable $listener
     * @return  EventDispatcher
     */
    public function addListener($name, $listener);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function hasListeners($name);

    /**
     * @param   string  $name
     * @return  array   
     */
    public function getListeners($name);

}
