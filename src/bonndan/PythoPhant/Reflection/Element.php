<?php
namespace PythoPhant\Reflection;

use PythoPhant\DocCommentToken as DocCommentToken;

/**
 * interface for elements which are built or are a part of those 
 * 
 * 
 */
interface Element
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