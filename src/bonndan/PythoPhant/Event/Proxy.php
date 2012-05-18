<?php
namespace PythoPhant\Event;

/**
 * Proxy
 * 
 * all-purpose observer, forwards events
 * 
 */
class Proxy extends AbstractSubject implements Subject, Observer
{

    /**
     * update all observers with an event
     * 
     * @param PythoPhant_Event $event
     * 
     * @return PythoPhant_Event_Proxy  
     */
    public function update(Event $event)
    {
        foreach ($this->getObservers() as $observer) {
            $observer->update($event);
        }

        return $this;
    }

    /**
     * add a logger (observer instance) via class name, filename or instance
     * 
     * @param mixed $logger
     * 
     * @return boolean
     */
    public function addLogger($logger)
    {
        if (is_string($logger)) {
            if (class_exists($logger)) {
                $logger = new $logger();
            } else {
                throw new \PythoPhant\Exception($logger . ' is not loadable.');
            }
        }
        
        /**
         * observer passed 
         */
        if ($logger instanceof Observer) {
            $this->attach($logger);
            return $logger;
        } else {
            throw new \PythoPhant\Exception(gettype($logger) . ' passed object must implement Observer.');
        }
    }
}
