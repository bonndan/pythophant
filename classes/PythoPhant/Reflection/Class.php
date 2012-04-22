<?php
/**
 * PythoPhant_Class
 * 
 * representation of a class, like ReflectionClass
 */
class PythoPhant_Reflection_Class
extends PythoPhant_Reflection_Interface
{
    
    /**
     * class scope
     * @var array 
     */
    private $vars = array();
    
    /**
     * class methods
     * @var PythoPhant_Function[] 
     */
    private $methods = array();
    
    /**
     * add a class variable
     * 
     * @param PythoPhant_Reflection_ClassVar $var
     * 
     * @return PythoPhant_Reflection_Class 
     */
    public function addVar(PythoPhant_Reflection_ClassVar $var)
    {
        $this->vars[$var->getName()] = $var;
        return $this;
    }
    

}