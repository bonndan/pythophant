<?php
namespace PythoPhant\Event;

/**
 * Subject
 * 
 * subject-observer pattern. 
 */
interface Subject
{
    /**
     * attach an observer
     * 
     * @param Observer $observer
     * 
     * @return Subject 
     */
    public function attach(Observer $observer);

    /**
     * detach an observer
     * 
     * @param Observer $observer
     * 
     * @return Subject
     */
    public function detach(Observer $observer);
}