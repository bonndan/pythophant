<?php
/**
 * ExclamationToken
 * 
 * exclamation mark used as placeholder for a previous expression
 * 
 * <code>
 * myVar = 'aString' strtolower()! ucfirst()!
 * </code>
 */
class ExclamationToken extends CustomGenericToken implements ParsedEarlyToken
{
    /**
     * question mark can be the regular if short form or a placeholder if it
     * trails a closing brace
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $token = $tokenList->getPreviousNonWhitespace($this);
        if ($token->getTokenName() == Token::T_CLOSE_BRACE) {
            $this->tokenName = Token::T_EXCLAMATION;
            $this->replaceExclamationMark($tokenList);
        } else {
            $this->tokenName = Token::T_NOT;
            $this->content = '!';
        }
    }
    
    /**
     * replace the question mark with previous tokens of list and add braces
     * 
     * @param TokenList $tokenList 
     * 
     * @todo remove one whitespace
     */
    private function replaceExclamationMark(TokenList $tokenList)
    {
        $token = $this;
        $openbraceFound = false;
        $functionFound = false;
        while ($token && !$functionFound){
            $token = $tokenList->getPreviousNonWhitespace($token);
            
            if ($token->getTokenName() == Token::T_OPEN_BRACE) {
                $openbraceFound = true;
                $openBrace = $token;
            } elseif ($token->getTokenName() == Token::T_CLOSE_BRACE) {
                $closeBrace = $token;
            } 
            if ($openbraceFound && $token->getTokenName() == Token::T_STRING) {
                $function = $token;
                $functionFound = true;
            }
        }
        
        if (!$function) {
            throw new PythoPhant_Exception('Could not find a function call token.');
        }
        
        $moved = $tokenList->getPreviousExpression($function);
        
        /**
         * write 
         */
        $this->content = '';
        
        if ($tokenList->getPreviousNonWhitespace($closeBrace) !== $openBrace) {
            $tokenList->injectToken(
                new PHPToken(Token::T_COMMA, ', ', $closeBrace->getLine()),
                $tokenList->getTokenIndex($closeBrace)
            );
        }
        $tokenList->moveTokensBefore(
            $moved,
            $closeBrace
        );
        
        $tokenList->offsetUnset($tokenList->getTokenIndex($this)); //remove question
        $tokenList->offsetUnset($tokenList->getTokenIndex($function)-1);//remove whitespace
    }
}
