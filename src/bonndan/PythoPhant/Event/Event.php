<?php
namespace PythoPhant\Event;

/**
 * PythoPhant_Event interface
 * 
 * 
 * 
 */
interface Event
{
    /**
     * events must be convertible to string 
     */
    public function __toString();
}