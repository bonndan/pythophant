<?php
/**
 * 
 */
class PythoPhant_ConsoleLoggerObserver implements PythoPhant_Observer
{
    /**
     * receives and logs events
     * 
     * @param PythoPhant_Event $event 
     */
    public function update(PythoPhant_Event $event)
    {
        echo $event . PHP_EOL;
    }
}
