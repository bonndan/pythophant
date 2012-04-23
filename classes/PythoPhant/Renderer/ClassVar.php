<?php
/**
 * PythoPhant_Renderer_ClassVar
 * 
 * A renderer for reflection classes.
 * 
 * 
 */
class PythoPhant_Renderer_ClassVar implements PythoPhant_Renderer
{
    /**
     * class var to render
     * @var PythoPhant_Reflection_ClassVar 
     */
    private $classVar;
    
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
        $buffer .= '    public $' . $this->classVar->getName();
        
        $body = $this->classVar->getBodyTokenList();
        if($body->count() > 0) {
            foreach ($body as $token) {
                $buffer .= $token->getContent();
            }
        }
        $buffer .= ';' . PHP_EOL;
        return $buffer;
    }

    /**
     * set the classVar to render
     * 
     * @param PythoPhant_Reflection_Element $element
     * 
     * @throws InvalidArgumentException 
     */
    public function setReflectionElement(PythoPhant_Reflection_Element $element)
    {
        if (!$element instanceof PythoPhant_Reflection_ClassVar) {
            throw new InvalidArgumentException(
                'ClassVar renderer requires an instance of PythoPhant_Reflection_ClassVar'
            );
        }
        
        $this->classVar = $element;
    }

}