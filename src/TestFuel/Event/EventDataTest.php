<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Console;

use StdClass,
    Appfuel\Event\EventData,
    Testfuel\FrameworkTestCase;

class EventDataTest extends FrameworkTestCase
{
    /**
     * @param    array    $data
     * @return    array
     */
    public function createEventData()
    {
        return new EventData();
    }

    /**
     * @test
     * @return  EventData
     */
    public function creatingEventData()
    {
        $event = $this->createEventData();
        $interface = 'Appfuel\\Event\\EventDataInterface';
        $this->assertInstanceOf($interface, $event);
        
        return $event;
    }

    /**
     * @test
     * @depends creatingEventData
     * @return  EventData
     */
    public function propagation(EventData $event)
    {
        // default value
        $this->assertFalse($event->isPropagation());

        $this->assertSame($event, $event->enablePropagation());
        $this->assertTrue($event->isPropagation());

        $this->assertSame($event, $event->disablePropagation());
        $this->assertFalse($event->isPropagation());

        return $event;
    }

    /**
     * @test
     * @depends propagation
     * @return  EventData
     */
    public function eventName(EventData $event)
    {
        $this->assertNull($event->getName());

        $name = 'my.event';
        $this->assertSame($event, $event->setName($name));
        $this->assertEquals($name, $event->getName());

        return $event;
    }

    /**
     * @test
     * @depends         propagation
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function eventNameFailure($badName)
    {
        $msg = 'event name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException');

        $event = $this->createEventData();
        $event->setName($badName);
    }

    /**
     * @test
     * @depends eventName
     * @return  EventData
     */
    public function eventDispatcher(EventData $event)
    {
        $this->assertNull($event->getDispatcher());

        $interface = 'Appfuel\\Event\\EventDispatcherInterface';
        $dispatcher = $this->getMock($interface);

        $this->assertSame($event, $event->setDispatcher($dispatcher));
        $this->assertSame($dispatcher, $event->getDispatcher());

        return $event;
    }


}
