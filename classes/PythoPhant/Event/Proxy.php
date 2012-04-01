<?php

/**
 * PythoPhant_Event_Proxy
 * 
 * all-purpose observer, forwards events
 * 
 */
class PythoPhant_Event_Proxy extends PythoPhant_AbstractSubject implements PythoPhant_Subject, PythoPhant_Observer
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

    /**
     * add a logger (observer instance) via class name, filename or instance
     * 
     * @param mixed $logger
     * 
     * @return boolean
     */
    public function addLogger($logger)
    {
        /**
         * observer passed 
         */
        if ($logger instanceof PythoPhant_Observer) {
            $this->attach($logger);
            return $logger;
        }

        if (!is_string($logger)) {
            return;
        }

        /**
         * valid class name given
         */
        if ($instance = $this->tryLoadLoggerClass($logger)) {
            $this->attach($instance);
            return $instance;
        }

        /**
         * prepend namespace 
         */
        $className = 'PythoPhant_Logger_' . $logger;
        if ($instance = $this->tryLoadLoggerClass($className)) {
            $this->attach($instance);
            return $instance;
        }
    }

    /**
     * tries to load a class via autoloading
     * 
     * @param string $classname
     * @param string $requireClass require_once 
     * 
     * @return false|PythoPhant_Observer
     */
    private function tryLoadLoggerClass($classname, $requireClass = null)
    {
        $result = false;
        try {
            set_error_handler(array($this, 'silentErrorHandler'));
            if (class_exists($classname)) {
                $logger = new $classname();
                if (!$logger instanceof PythoPhant_Observer) {
                    $this->notify(
                        new PythoPhant_Event_Error(
                            $classname . ' must implement PythoPhant_Observer.',
                            $classname
                        )
                    );
                } else {
                    $result = $logger;
                }
            }
        } catch (Exception $exc) {
            
        }
        restore_error_handler();
        return $result;
    }

    /**
     * does nothing
     * 
     * @param type $null 
     */
    public function silentErrorHandler($null)
    {
        
    }
}
