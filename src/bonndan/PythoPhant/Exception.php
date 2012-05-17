<?php
namespace PythoPhant;

/**
 * PythoPhant_Exception
 * 
 * use the code to pass the line number
 * 
 */
class Exception extends \Exception
{
    /**
     * returns the code
     * 
     * @return type 
     */
    public function getSourceLine()
    {
        return $this->getCode();
    }
}
