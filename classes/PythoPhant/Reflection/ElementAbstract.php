<?php
/**
 * PythoPhant_Reflection_ElementAbstract
 * 
 * abstract class implementing reflection element methods
 * 
 */
abstract class PythoPhant_Reflection_ElementAbstract
implements PythoPhant_Reflection_Element
{
    /**
     * element name
     * @var string
     */
    protected $name = null;
    
    /**
     * doc comment
     * @var DocCommentToken 
     */
    protected $docComment = null;
    
    /**
     * constructor requires at least the class name
     * 
     * @param string          $name 
     * @param DocCommentToken $docComment
     */
    public function __construct($name, DocCommentToken $docComment)
    {
        $this->name = (string)$name;
        if ($docComment !== null) {
            $this->docComment = $docComment;
        }
    }
    
    /**
     * the class name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * get the doc comment
     * 
     * @return DocCommentToken
     */
    public function getDocComment()
    {
        return $this->docComment;
    }
}
