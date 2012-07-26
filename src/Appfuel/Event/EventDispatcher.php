<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Event;

/**
 * The main player in the Observer design pattern
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * List of listeners to be notified of events
     * @var array
     */
    protected $listeners = array();

    /**
     * @param   string  $name   name of the event
     * @param   EventDataInterface   $event
     * @return  null
     */
    public function dispatch($name, EventDataInterface $event = null)
    {
        if (null === $event) {
            $event = new EventData();
        }

        $event->setDispatcher($this);
        $event->setName($name);
        if (! $this->hasListeners()) {
            return $event;
        }

        $this->doDispatch($this->getListeners($name), $name, $event);
    }

    /**
     * @param   string  $name
     * @param   callable $listener
     * @return  EventDispatcher
     */
    public function addListener($name, $listener, $priority = 0)
    {
        $this->listeners[$name][] = $listener;
        return $this;
    }

    /**
     * @param   string  $name
     * @return  bool
     */
    public function hasListeners($name)
    {
        if (isset($this->listeners[$name]) &&
            count($this->listeners[$name]) > 0) {
            return true;
        }

        return false;
    }

    public function getListeners($name)
    {
        
    }

    protected function execute(array $list, $name, EventDataInterface $event)
    {
        foreach ($list as $listener) {
            call_user_func($listener, $event);
            if (! $event->isPropagation()) {
                break;
            }
        }
    }
}
