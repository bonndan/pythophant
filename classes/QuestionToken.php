<?php
/**
 * Token to write single-argument function calls as question. Is processed early
 * to move preceding tokens before they are processed
 * 
 * <code>
 * isFile = 'path/to/file.ext' is_file?
 * </code> 
 */
class QuestionToken extends CustomGenericToken implements ParsedEarlyToken
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
        $token = $this;
        while ($token = $tokenList->getNextNonWhitespace($token)) {
            if ($token->getTokenName() == 'T_COLON') {
                $this->tokenName = Token::T_SHORT_IF;
                return;
            }
        }
        
        $token = $this;
        while ($token = $tokenList->getPreviousNonWhitespace($token)) {
            $indicators = array_merge(
                PythoPhant_Grammar::$controls,
                array(Token::T_RETURN,  'T_ASSIGN')
            );
            if ($tokenList->isTokenIncluded(array($token), $indicators)) {
                $this->replaceQuestionMark($tokenList);
                return;
            }
        }
    }
    
    /**
     * replace the question mark with previous tokens of list and add braces
     * 
     * @param TokenList $tokenList 
     * 
     * @todo remove one whitespace
     */
    private function replaceQuestionMark(TokenList $tokenList)
    {
        $function = $tokenList->getPreviousNonWhitespace($this);
        
        $moved = array();
        $prev = $tokenList->getPreviousNonWhitespace($function);
        $stop = false;
        while($prev instanceof Token && !$stop) {
            
            $stop = $tokenList->isTokenIncluded(array($prev), PythoPhant_Grammar::$stopsQuestionSubject);
            if (!$stop) {
                $moved[] = $prev;
            }
            $prev = $tokenList->getPreviousNonWhitespace($prev);
        }
        
        /**
         * write 
         */
        $this->content = '';
        
        $moved = array_reverse($moved);
        $tokenList->injectToken(
            new PHPToken(Token::T_OPEN_BRACE, PythoPhant_Grammar::T_OPEN_BRACE, $this->getLine()),
            $tokenList->getTokenIndex($this)
        );
        
        $lastIndex = $tokenList->moveTokensBefore($moved, $this);
        $tokenList->injectToken(
            new PHPToken(Token::T_CLOSE_BRACE, PythoPhant_Grammar::T_CLOSE_BRACE, $this->getLine()),
            $lastIndex+1
        );
        $tokenList->offsetUnset($tokenList->getTokenIndex($this)); //remove question
        $tokenList->offsetUnset($tokenList->getTokenIndex($function)-1);//remove whitespace
    }
}
