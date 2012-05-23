<?php

namespace PythoPhant\Event;

/**
 * a trigger event for the directorywatcher
 * 
 *  
 */
class ScanTrigger implements \PythoPhant\Event\Event
{
    /**
     * notification
     */
    public function __toString()
    {
        return "Directory scan triggered.";
    }
}