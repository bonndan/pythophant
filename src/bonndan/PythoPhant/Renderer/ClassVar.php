<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;
use PythoPhant\Reflection\ClassVar as ReflectionClassVar;

/**
 * PythoPhant_Renderer_ClassVar
 * 
 * A renderer for reflection classes.
 * 
 * 
 */
class ClassVar implements ReflectionRenderer
{
    /**
     * class var to render
     * @var PythoPhant_Reflection_ClassVar 
     */
    private $classVar;
    
    /**
     * @todo implement
     * @param bool $debug 
     */
    public function enableDebugging($debug)
    {
        
    }

    /**
     * to php
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $buffer = '    ' . $this->classVar->getDocComment()->getContent() . PHP_EOL;
        $buffer .= '    ' . $this->classVar->getModifiers() . ' $' . $this->classVar->getName();
        
        $body = $this->classVar->getBodyTokenList();
        if ($body->count() > 0) {
            foreach ($body as $token) {
                $buffer .= $token->getContent();
            }
        } else {
            $buffer .= ';';
        }
       
        $buffer .= PHP_EOL . PHP_EOL;
        return $buffer;
    }

    /**
     * set the classVar to render
     * 
     * @param Element $element
     * 
     * @throws InvalidArgumentException 
     */
    public function setReflectionElement(Element $element)
    {
        if (!$element instanceof ReflectionClassVar) {
            throw new \InvalidArgumentException(
                'ClassVar renderer requires an instance of PythoPhant\Reflection\ClassVar'
            );
        }
        
        $this->classVar = $element;
    }

}