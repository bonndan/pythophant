<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\ClassConst;

/**
 * PythoPhant_Renderer_ClassConstant
 * 
 * A renderer for reflection class constants.
 * 
 * 
 */
class ClassConst implements ReflectionElement
{

    /**
     * class var to render
     * @var PythoPhant_Reflection_ClassConst
     */
    private $const;

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
        $buffer = '    ' . $this->const->getDocComment()->getContent() . PHP_EOL;
        $buffer .= '    const ' . $this->const->getName();

        $body = $this->const->getBodyTokenList();
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
     * @param PythoPhant_Reflection_Element $element
     * 
     * @throws InvalidArgumentException 
     */
    public function setReflectionElement(Element $element)
    {
        if (!$element instanceof ClassConst) {
            throw new InvalidArgumentException(
                'ClassConst renderer requires an instance of PythoPhant\Reflection\ClassConst'
            );
        }

        $this->const = $element;
    }

}