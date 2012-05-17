<?php
namespace PythoPhant\Event;

/**
 * PythoPhant_Observer
 * 
 * subject-observer pattern. 
 */
interface Observer 
{
    /**
     * the observer receives events
     * 
     * @param Event $event
     */
    public function update(Event $event);
}