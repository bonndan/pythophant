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
            'T_OPEN_BRACE'
        );
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
}
