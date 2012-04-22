<?php
/**
 * PythoPhant_ClassVar
 * 
 * representation of a class variable
 */
class PythoPhant_ClassVar
{
    /**
     * variable name
     * @var string
     */
    private $name = null;
    
    /**
     * variable type
     * @var string|null
     */
    private $type = null;
    
    /**
     * doc comment
     * @var DocCommentToken 
     */
    private $docComment;
    
    /**
     * constructor requires a name and the doc comment as token
     * 
     * @param string          $name
     * @param DocCommentToken $docComment 
     */
    public function __construct($name, DocCommentToken $docComment)
    {
        $this->name = (string)$name;
        $this->docComment = $docComment;
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
            $this->type = current($types);
        }
    }
    
    /**
     * get the variable name
     * 
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * returns the variable type
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
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
