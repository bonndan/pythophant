<?php
/**
 * PythoPhant_Renderer_Function
 * 
 * A renderer for reflection functions
 * 
 * 
 */
class PythoPhant_Renderer_Function implements PythoPhant_Renderer
{
    /**
     * class var to render
     * @var PythoPhant_Reflection_Function 
     */
    private $function;
    
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
        $buffer = '    ' . $this->function->getDocComment()->getContent() . PHP_EOL;
        $buffer .= '    public function ' . $this->function->getName();
        $buffer .= '(';
        $params = array();
        foreach ($this->function->getParams() as $param) {
            /* @var $param PythoPhant_Reflection_FunctionParam */
            $params[] = $param->getType() . ' $'.$param->getName();
        }
        $buffer .= implode(', ', $params);
        $buffer .= ')';
        
        $body = $this->function->getBodyTokenList();
        if ($body->count() == 0) {
            $buffer .= ';' . PHP_EOL;
        } else {
            
            $buffer .= PHP_EOL . '    {' . PHP_EOL;
            foreach ($body as $token) {
                $buffer .= $token->getContent();
            }
            $buffer .= PHP_EOL . '    }' . PHP_EOL . PHP_EOL;
        }
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
        if (!$element instanceof PythoPhant_Reflection_Function) {
            throw new InvalidArgumentException(
                'Function renderer requires an instance of PythoPhant_Reflection_Function'
            );
        }
        
        $this->function = $element;
    }

}