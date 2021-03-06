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
    
    /**
     * toogle debugging
     * 
     * @param bool $debug 
     * @todo implement
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
        $buffer = '    ' . $this->function->getDocComment()->getContent() . PHP_EOL;
        $buffer .= '    ' . $this->function->getModifiers();
        $buffer .= ' function ' . $this->function->getName();
        $buffer .= '(';
        $params = array();
        $typeToken = null;
        $body = $this->function->getBodyTokenList();
        
        
        foreach ($this->function->getParams() as $param) {
            $typeToken = $param->getType();
            if (!$typeToken instanceof ReturnValueToken) {
                $typeToken = new ReturnValueToken(Token::T_RETURNVALUE, $param->getType(), 1);
            }
            
            $default = $param->getDefault();
            if (trim($default) != '') {
                $default = ' = ' .$default;
            }
            
            /** 
             * @todo remove explicit call to insertTypecheckinTokenList, should
             * be triggered by token
             */
            $typeToken->insertTypecheckinTokenList($body, $param);
            
            $type = $typeToken->getContent();
            if ($type != '') {
                $type .= ' ';
            }
            
            
            /* @var $param PythoPhant_Reflection_FunctionParam */
            $params[] =  $type. '$' . $param->getName() . $default;
        }
        $buffer .= implode(', ', $params);
        $buffer .= ')';
        
       
        if ($body->count() == 0) {
            $buffer .= ';' . PHP_EOL;
        } else {
            $buffer .= PHP_EOL . '    {' . PHP_EOL;
            foreach ($body as $token) {
                $buffer .= $token->getContent();
            }
            $buffer .= '    }' . PHP_EOL . PHP_EOL;
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