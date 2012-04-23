<?php

/**
 * PythoPhant_Renderer_ClassConstant
 * 
 * A renderer for reflection class constants.
 * 
 * 
 */
class PythoPhant_Renderer_ClassConst implements PythoPhant_Renderer
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
        foreach ($body as $token) {
            $buffer .= $token->getContent();
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
        if (!$element instanceof PythoPhant_Reflection_ClassConst) {
            throw new InvalidArgumentException(
                'ClassConst renderer requires an instance of PythoPhant_Reflection_ClassConst'
            );
        }

        $this->const = $element;
    }

}