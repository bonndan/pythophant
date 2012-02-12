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
            if ($tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$controls)) {
                $this->replaceQuestionMark($tokenList);
                return;
            }
        }
    }
    
    /**
     * 
     * @param TokenList $tokenList 
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
        $this->tokenName = Token::T_OPEN_BRACE;
        $this->content = PythoPhant_Grammar::OPEN_BRACE;
        
        $moved = array_reverse($moved);
        $offset = 0;
        foreach ($moved as $token) {
            $offset++;
            $index = $tokenList->getTokenIndex($token);
            $tokenList->offsetUnset($index);
            $funcIndex = $tokenList->getTokenIndex($function);
            $tokenList->injectToken($token, $funcIndex + $offset);
        }
        $tokenList->injectToken(
            new PHPToken(Token::T_CLOSE_BRACE, ')', 0),
            $funcIndex + $offset + 1
        );
    }
}
