<?php
/**
 * PythoPhant_Reflection_Interface
 */
class PythoPhant_Reflection_Interface extends PythoPhant_Reflection_ElementAbstract
{
    /**
     * add a method
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