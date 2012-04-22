<?php
/**
 * PythoPhant_Class
 * 
 * representation of a class, like ReflectionClass
 */
class PythoPhant_Reflection_Class
extends PythoPhant_Reflection_ElementAbstract
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
    
    /**
     *
     * @param PythoPhant_Reflection_Function $method
     * @return PythoPhant_Reflection_Class 
     */
    public function addMethod(PythoPhant_Reflection_Function $method)
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }
    
    /**
     * return all methods
     * 
     * @return array 
     */
    public function getMethods()
    {
        return $this->methods;
    }
}