<?php
/**
 * PythoPhant_Function
 * 
 * representation of a class method
 */
class PythoPhant_Function
{
    /**
     * doc comment
     * @var DocCommentToken 
     */
    private $docComment;
    
    /**
     * modifiers, visibility
     * @var string
     */
    private $modifiers = 'public';
    
    /**
     * function name
     * @var string 
     */
    private $name = '';
    
    /**
     *
     * @var PythoPhant_FunctionParam[] 
     */
    private $params = array();
    
    /**
     * constructor requires a name
     * 
     * @param string $name
     * @param string $modifiers
     */
    public function __construct($name, $modifiers = null)
    {
        $this->name       = $name;
        if ($modifiers !== null) {
            $this->modifiers  = $modifiers;
        }
    }
    
    /**
     * returns the method/function name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * recognize the signature of the function by the doc comments
     * 
     * @param DocCommentToken $docComment 
     */
    public function setSignatureFromDocComment(DocCommentToken $docComment)
    {
        $this->docComment = $docComment;
        
        $params = $this->docComment->getParams();
        foreach ($params as $variable => $data) {
            $this->params[$variable] = new PythoPhant_FunctionParam($data[0], $variable);
        }
    }
    
    /**
     * returns all function params
     * @return array 
     */
    public function getParams()
    {
        return $this->params;
    }
}
