<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;
use PythoPhant\Reflection\Method as RefMethod;
use PythoPhant\ReturnValueToken;
use PythoPhant\Token;

/**
 * Method
 * 
 * A renderer for reflection methods
 * 
 * 
 */
class Method implements ReflectionRenderer
{
    /**
     * class var to render
     * @var PythoPhant\Reflection\Method 
     */
    private $method;
    
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
        $buffer = '    ' . $this->method->getDocComment()->getContent() . PHP_EOL;
        $buffer .= '    ' . $this->method->getModifiers();
        $buffer .= ' function ' . $this->method->getName();
        $buffer .= '(';
        $params = array();
        $typeToken = null;
        $body = $this->method->getBodyTokenList();
        
        
        foreach ($this->method->getParams() as $param) {
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
     * @param Element $element
     * 
     * @throws InvalidArgumentException 
     */
    public function setReflectionElement(Element $element)
    {
        if (!$element instanceof RefMethod) {
            throw new \InvalidArgumentException(
                'Function renderer requires an instance of PythoPhant\Reflection\Method'
            );
        }
        
        $this->method = $element;
    }

}