<?php
/**
 * PythoPhant_Class
 * 
 * representation of a class, like ReflectionClass
 */
class PythoPhant_Class
{
    /**
     * doc comment
     * @var DocCommentToken 
     */
    private $docComment = null;
    
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
     * constructor requires at least the class name
     * 
     * @param string          $name 
     * @param DocCommentToken $docComment = null
     */
    public function __construct($name, DocCommentToken $docComment = null)
    {
        $this->name = (string)$name;
        if ($docComment !== null) {
            $this->docComment = $docComment;
        }
    }
    
    /**
     * add a class variable
     * 
     * @param PythoPhant_ClassVar $var
     * 
     * @return \PythoPhant_Class 
     */
    public function addVar(PythoPhant_ClassVar $var)
    {
        $this->vars[$var->getName()] = $var;
        return $this;
    }
    
    /**
     *
     * @param PythoPhant_Function $method
     * @return \PythoPhant_Class 
     */
    public function addMethod(PythoPhant_Function $method)
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