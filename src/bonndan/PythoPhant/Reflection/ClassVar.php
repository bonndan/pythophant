<?php
namespace PythoPhant\Reflection;

use PythoPhant\DocCommentToken as DocCommentToken;
use PythoPhant\ReturnValueToken as ReturnValueToken;

/**
 * ClassVar
 * 
 * representation of a class variable
 */
class ClassVar extends MemberAbstract
{
    /**
     * constructor requires a name and the doc comment as token
     * 
     * @param string          $name
     * @param DocCommentToken $docComment 
     */
    public function __construct($name, DocCommentToken $docComment)
    {
        parent::__construct($name, $docComment);
        $this->detectType();
    }
    
    /**
     * detects the variable type based on the doc comment
     * 
     * @return void 
     */
    private function detectType()
    {
        $types = $this->docComment->getAnnotation('var');
        if ($types !== null && !empty($types)) {
            $this->type = new ReturnValueToken('T_RETURNVALUE', current($types), 0);
        }
    }
    
    /**
     * check if the variable is marked as property, returns the property type
     * 
     * @return string|null 
     */
    public function isProperty()
    {
        return $this->docComment->getAnnotation('property')
            || $this->docComment->getAnnotation('property-read')
            || $this->docComment->getAnnotation('property-write');
    }
}
