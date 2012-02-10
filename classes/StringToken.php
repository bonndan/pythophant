<?php
/**
 * StringToken can render itself as a variable with leading dollar sign
 * 
 * 
 */
class StringToken extends CustomGenericToken
{
    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    public function affectTokenList(TokenList $tokenList)
    {
        if ($this->getTokenName() == 'T_STRING') {
            $this->checkIfIsVariable($tokenList);
        }
    }

    /**
     * check if it is a string
     * 
     * @param TokenList $tokenList 
     */
    private function checkIfIsVariable(TokenList $tokenList)
    {
        if ($this->isConstant()) {
            return;
        }
        
        /**
         * token before 
         */
        $previous = $tokenList->getPreviousNonWhitespace($this);
        $preVariableIndicators = array_merge(
            PythoPhant_Grammar::$preVariableIndicators,
            PythoPhant_Grammar::$controls
        );
        $preCondition = $tokenList->isTokenIncluded(array($previous), $preVariableIndicators);
        
        /**
         * token after 
         */
        $next = $tokenList->getNextNonWhitespace($this);
        
        $postCondition = $tokenList->isTokenIncluded(
            array($next),
            PythoPhant_Grammar::$postVariableIndicators
        );
       
        if(
            ($preCondition && $postCondition) 
            || (is_null($previous) && $postCondition)
            || ($preCondition && is_null($next))
        ) {
            $this->tokenName = 'T_VARIABLE';
            $this->content = '$'.$this->content;
        }
        
        /**
         * class var declaration 
         */
        if (!$previous || $this->tokenName != 'T_VARIABLE') {
            return;
        }
        
        /**
         * return type cant be rendered 
         */
        $preprevious = $tokenList->getPreviousNonWhitespace($previous);
        if ($previous instanceof StringToken 
            && $preprevious instanceof Token
            && in_array($preprevious->getTokenName(), PythoPhant_Grammar::$modifiers)
        ) {
            $preprevious->setContent('');
        }
    }
    
    /**
     * checks if the content might be a constant
     * @return boolean 
     */
    public function isConstant()
    {
        if (defined($this->content)) {
            return true;
        }
        
        if ($this->isUppercase($this->content)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * checks if a string has only uppercase chars except underscores
     * 
     * @param string $string 
     * 
     * @return boolean
     */
    private function isUppercase($string)
    {
        return ctype_upper(str_replace('_', '', $string));
    }
}
