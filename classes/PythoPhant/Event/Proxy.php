<?php
/**
 * PythoPhant_Event_Proxy
 * 
 * all-purpose observer, forwards events
 * 
 */
class PythoPhant_Event_Proxy
extends PythoPhant_AbstractSubject
implements PythoPhant_Subject, PythoPhant_Observer
{
    /**
     * update all observers with an event
     * 
     * @param PythoPhant_Event $event
     * 
     * @return PythoPhant_Event_Proxy  
     */
    public function update(PythoPhant_Event $event)
    {
        foreach ($this->getObservers() as $observer) {
            $observer->update($event);
        }
        
        return $this;
    }

}
