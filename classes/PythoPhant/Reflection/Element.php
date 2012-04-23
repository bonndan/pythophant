<?php
/**
 * interface for elements which are built or are a part of those 
 * 
 * 
 */
interface PythoPhant_Reflection_Element
{
    /**
     * constructor requires a name and a doc comment
     * 
     * @param string          $name
     * @param DocCommentToken $docComment
     */
    public function __construct($name, DocCommentToken $docComment);
    
    /**
     * get the name of the element
     * 
     * @return string 
     */
    public function getName();
    
    /**
     * get the doc comment
     * 
     * @return DocCommentToken
     */
    public function getDocComment();
}