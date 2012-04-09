<?php
/**
 * PythoPhant_Logger_Console
 * 
 * logger which prints events on the console
 */
class PythoPhant_Logger_Console implements PythoPhant_Observer
{
    /**
     * receives and logs events
     * 
     * @param PythoPhant_Event $event 
     */
    public function update(PythoPhant_Event $event)
    {
        echo $event->__toString() . PHP_EOL;
    }
}
