<?php
/**
 * PythoPhant_Subject
 * 
 * subject-observer pattern. 
 */
interface PythoPhant_Subject
{
    /**
     * attach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return PythoPhant_Subject 
     */
    public function attach(PythoPhant_Observer $observer);

    /**
     * detach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return PythoPhant_Subject
     */
    public function detach(PythoPhant_Observer $observer);
}