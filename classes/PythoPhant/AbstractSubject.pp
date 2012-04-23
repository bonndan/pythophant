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
    __construct
        @observers = new SplObjectStorage()
    
    /**
     * attach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return \PythoPhant_DirectoryWatcher 
     */
    attach
        @observers.attach: observer
        return this
    
    /**
     * detach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return \PythoPhant_DirectoryWatcher 
     */
    detach
        @observers.detach: observer
        return this
    
    /**
     * notify all observers
     * 
     * @param PythoPhant_Event $event 
     */
    protected notify
        foreach @observers as observer
            observer.update: event