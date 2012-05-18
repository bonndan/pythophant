<?php
namespace PythoPhant\Event;

/**
 * PythoPhant_AbstractSubject
 * 
 * base class of subjects, uses SplObjectStorage to keep track of observers
 * 
 * 
 */
abstract class AbstractSubject
{
    /**
     * observers
     * @var SplObjectStorage 
     */
    private $observers;

    /**
     * the constructor initialises the observer object storage 
     */
    public function __construct()
    {
        $this->observers = new \SplObjectStorage();
    }

    /**
     * attach an observer
     * 
     * @param Observer $observer
     * 
     * @return AbstractSubjectr 
     */
    public function attach(Observer $observer)
    {
        $this->observers->attach($observer);
        return $this;
    }

    /**
     *
     * @param Observer $observer
     * 
     * @return AbstractSubject
     */
    public function detach(Observer $observer)
    {
        $this->observers->detach($observer);
        return $this;
    }

    /**
     * get the observer storage
     * @return SplObjectStorage 
     */
    protected function getObservers()
    {
        return $this->observers;
    }
    
    /**
     * notify all observers
     * 
     * @param PythoPhant_Event $event 
     */
    protected function notify(Event $event)
    {
        foreach ($this->getObservers() as $observer) {
            $observer->update($event);
        }

    }

}

