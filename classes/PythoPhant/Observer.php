<?php
/**
 * PythoPhant_Observer
 * 
 * subject-observer pattern. 
 */
interface PythoPhant_Observer 
{
    /**
     * the observer receives events
     * 
     * @param PythoPhant_Event $event
     */
    public function update(PythoPhant_Event $event);
}