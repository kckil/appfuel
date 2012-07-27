<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Event;

use InvalidArgumentException;

class EventData implements EventDataInterface
{
    /**
     * Flag used to detemine if the other listeners should be triggered
     * @var bool
     */
    private $isPropagation = false;

    /**
     * Used to dispatch this event
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * The name of this event
     * @var string
     */
    private $name = null;

    /**
     * @return  bool
     */
    public function isPropagation()
    {
        return $this->isPropagation;
    }

    /**
     * @return EventData
     */
    public function enablePropagation()
    {
        $this->isPropagation = true;
        return $this;
    }

    /**
     * @return EventData
     */
    public function disablePropagation()
    {
        $this->isPropagation = false;
        return $this;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param   string  $name
     * @return  EventData
     */
    public function setName($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "event name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return  EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param   EventDispatcherInterface $dispatcher
     * @return  EventData
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
}
