<?php
namespace PythoPhant\Event\Logger;

use PythoPhant\Event\Event;
use PythoPhant\Event\Observer;

/**
 * PythoPhant_Logger_Console
 * 
 * logger which prints events on the console
 */
class Console implements Observer
{
    /**
     * receives and logs events
     * 
     * @param Event $event 
     */
    public function update(Event $event)
    {
        echo $event->__toString() . PHP_EOL;
    }
}
