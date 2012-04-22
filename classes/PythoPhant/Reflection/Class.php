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
     * names of the implemented interfaces
     * @var array(string) 
     */
    private $implements = array();
    
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
     * get the declared class vars
     * @return array 
     */
    public function getVars()
    {
        return $this->vars;
    }
    
    /**
     * set the implemented interfaces
     * 
     * @param array $interfaces
     * 
     * @return PythoPhant_Reflection_Class
     */
    public function setImplements(array $interfaces)
    {
        foreach ($interfaces as $key => $interface) {
            if ($interface instanceof StringToken) {
                $interfaces[$key] = $interface->getContent();
            }
        }
        
        $this->implements = $interfaces;
        return $this;
    }
}