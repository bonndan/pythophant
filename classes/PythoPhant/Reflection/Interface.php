<?php
/**
 * PythoPhant_Reflection_Interface
 * 
 * 
 * 
 */
class PythoPhant_Reflection_Interface extends PythoPhant_Reflection_ElementAbstract
{
    /**
     * name of the class or interface which is extended
     * @var string 
     */
    protected $extends = null;
    
    /**
     * all class constants
     * @var array
     */
    protected $const = array();
    
    /**
     * all class methods
     * @var array
     */
    protected $methods = array();
    
    /**
     * token list which is rendered before the actual element
     * @var TokenList 
     */
    protected $preamble = null;
    
    /**
     * set a token list which is rendered before the actual element
     * 
     * @param TokenList $tokenList 
     */
    public function setPreamble(TokenList $tokenList)
    {
        $this->preamble = $tokenList;
    }
    
    /**
     * 
     * @return type 
     */
    public function getPreamble()
    {
        return $this->preamble;
    }
    
    /**
     * add a class constant
     * 
     * @param PythoPhant_Reflection_ClassConst $const
     * 
     * @return PythoPhant_Reflection_Class 
     */
    public function addConstant(PythoPhant_Reflection_ClassConst $const)
    {
        $this->const[$const->getName()] = $const;
        return $this;
    }
    
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
    public function getConstants()
    {
        return $this->const;
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
    
    /**
     * set the parent
     * 
     * @param string|StringToken $parent 
     * 
     * @return PythoPhant_Reflection_Class
     */
    public function setExtends($parent)
    {
        if ($parent instanceof StringToken) {
            $parent = $parent->getContent();
        }
        
        $this->extends = $parent;
        return $this;
    }
    
    /**
     * get the name of the parent class
     * 
     * @return string|null
     */
    public function getExtends()
    {
        return $this->extends;
    }
}