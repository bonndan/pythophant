<?php

class ExclamationToken extends CustomGenericToken implements ParsedEarlyToken
{
    /**
     * question mark can be the regular if short form or a placeholder
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $token = $tokenList->getPreviousNonWhitespace($this);
        if ($token->getTokenName() == Token::T_CLOSE_BRACE) {
            $this->tokenName = 'T_EXCLAMATION';
            $this->replaceExclamationMark($tokenList);
        } else {
            $this->tokenName = 'T_NOT';
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
            throw new LogicException('Could not find a function call');
        }
        
        $moved = array();
        $prev = $tokenList->getPreviousNonWhitespace($function);
        $stop = false;
        while($prev instanceof Token && !$stop) {
            $stop = $tokenList->isTokenIncluded(array($prev), PythoPhant_Grammar::$stopsQuestionSubject);
            if (!$stop) {
                $moved[] = $prev;
            } else {
                break;
            }
            $prev = $tokenList->getPreviousNonWhitespace($prev);
        }
        
        $moved = array_reverse($moved);
        
        /**
         * write 
         */
        $this->content = '';
        
        if ($tokenList->getPreviousNonWhitespace($closeBrace) !== $openBrace) {
            $tokenList->injectToken(
                new PHPToken('T_COMMA', ', ', $closeBrace->getLine()),
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
