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
        $parser = new Parser(new TokenFactory);
        
        if ($this->isConstant()) {
            return;
        }
        
        /**
         * token before 
         */
        $previous = $tokenList->getPreviousNonWhitespace($this);
        $preVariableIndicators = array(
            Token::T_RETURNVALUE,
            'T_STATIC',
            'T_PRIVATE',
            'T_PROTECTED',
            'T_ASSIGN',
            'T_DOUBLE_COLON',
            'T_COMMA',
            'T_STRING',
            'T_OPEN_BRACE',
            'T_DOUBLE_ARROW',
            'T_AS',
            'T_ECHO',
            'T_BOOLEAN_AND',
            'T_BOOLEAN_OR',
        );
        $preVariableIndicators = array_merge($preVariableIndicators, Parser::$controls);
        $preCondition = $parser->isTokenIncluded(array($previous), $preVariableIndicators);
        
        /**
         * token after 
         */
        $next = $tokenList->getNextNonWhitespace($this);
        $postVariableIndicators = array(
            'T_CLOSE_BRACE',
            'T_CLOSE_ARRAY',
            'T_COMMA',
            'T_ASSIGN',
            'T_MEMBER',
            'T_DOUBLE_COLON',
            'T_AS',
            'T_DOUBLE_ARROW',
            'T_OPEN_ARRAY',
            'T_BOOLEAN_AND',
            'T_BOOLEAN_OR',
        );
        $postCondition = $parser->isTokenIncluded(array($next), $postVariableIndicators);
       
        if(
            ($preCondition && $postCondition) 
            || (is_null($previous) && $postCondition)
            || ($preCondition && is_null($next))
        ) {
            $this->tokenName = 'T_VARIABLE';
            $this->content = '$'.$this->content;
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
