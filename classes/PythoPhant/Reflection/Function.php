<?php
/**
 * PythoPhant_Function
 * 
 * representation of a class method
 */
class PythoPhant_Reflection_Function extends PythoPhant_Reflection_ElementAbstract
{
    /**
     * modifiers, visibility
     * @var string
     */
    private $modifiers = 'public';
    
    /**
     * function params
     * @var PythoPhant_FunctionParam[] 
     */
    private $params = array();
    
    /**
     * constructor requires a name and a doc comment
     * 
     * @param string          $name
     * @param DocCommentToken $docComment
     * @param string          $modifiers
     */
    public function __construct($name, DocCommentToken $docComment, $modifiers = null)
    {
        parent::__construct($name, $docComment);
        $this->setSignatureFromDocComment($docComment);
        if ($modifiers !== null) {
            $this->modifiers  = $modifiers;
        }
    }
    
    /**
     * recognize the signature of the function by the doc comments
     * 
     * @param DocCommentToken $docComment 
     */
    private function setSignatureFromDocComment(DocCommentToken $docComment)
    {
        $this->docComment = $docComment;
        
        $params = $this->docComment->getParams();
        foreach ($params as $variable => $data) {
            $this->params[$variable] = new PythoPhant_Reflection_FunctionParam($data[0], $variable);
        }
    }
    
    /**
     * returns all function params
     * 
     * @return array 
     */
    public function getParams()
    {
        return $this->params;
    }
}
