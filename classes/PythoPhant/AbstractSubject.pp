<?php
/**
 * base class of subjects
 * 
 * 
 */
abstract class PythoPhant_AbstractSubject

    /**
     * observers
     * @var SplObjectStorage 
     */
    private observers = []
    
    /**
     * the constructor initialises the observer object storage 
     */
    __construct()
        @observers = new SplObjectStorage()
    
    /**
     * attach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return \PythoPhant_DirectoryWatcher 
     */
    attach: PythoPhant_Observer observer
        @observers.attach: observer
        return this
    
    /**
     *
     * @param PythoPhant_Observer $observer
     * @return \PythoPhant_DirectoryWatcher 
     */
    detach: PythoPhant_Observer observer
        @observers.detach: observer
        return this
    
    /**
     * notify all observers
     * 
     * @param PythoPhant_Event $event 
     */
    protected notify: PythoPhant_Event event
        foreach @observers as observer
            observer.update: event