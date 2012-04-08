<?php
/**
 * Token for constants / strings, provides string concatenation 
 */
class ConstToken extends CustomGenericToken
{
    /**
     * this generic implementation does not affect the token list
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $prevIndicators = array(
            Token::T_CLOSE_BRACE,
            Token::T_CONSTANT_ENCAPSED_STRING,
            Token::T_NEWLINE,
            Token::T_VARIABLE,
            Token::T_STRING, /** @todo $this->var ... var is T_STRING*/
        );
        $prev = $tokenList->getPreviousNonWhitespace($this);
        $precondition = $tokenList->isTokenIncluded(array($prev), $prevIndicators);
        
        if ($precondition) {
            $this->injectConcatenation($tokenList);
        }
        
        $nextIndicators = array(
            Token::T_OPEN_BRACE,
            Token::T_CONSTANT_ENCAPSED_STRING,
            Token::T_STRING,
            'T_SELF',
        );
        $next = $tokenList->getNextNonWhitespace($this);
        $postcondition = $tokenList->isTokenIncluded(array($next), $nextIndicators);
        if ($postcondition) {
            $this->injectConcatenation($tokenList, 1);
        }
        return;
    }
    
    /**
     * inject a StringToken T_CONCAT (".")
     * 
     * @param TokenList $tokenList
     * @param int       $offset 
     * 
     * @return void
     */
    private function injectConcatenation(TokenList $tokenList, $offset = 0)
    {
        $tokenList->injectToken(
            new StringToken('T_CONCAT', '. ', $this->getLine()),
            $tokenList->getTokenIndex($this) + $offset
        );
    }
}
