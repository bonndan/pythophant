<?php
namespace PythoPhant\Reflection;

use PythoPhant\DocCommentToken as DocCommentToken;

/**
 * Method
 * 
 * representation of a class method
 */
class Method extends MemberAbstract
{
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
     */
    public function __construct($name, DocCommentToken $docComment)
    {
        parent::__construct($name, $docComment);
        $this->setSignatureFromDocComment($docComment);
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
            $this->params[$variable] = new Param(
                $data[0],
                $variable,
                $data[2]
            );
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
