<?php
/**
 * the content of this always T_OBJECT_OPERATOR 
 */
class MemberToken extends CustomGenericToken
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
        if ($this->checkForConcatenation($tokenList)) {
            return;
        }
        
        $this->setContent("->");
    }
    
    /**
     * check if the token is just a concatenation
     * 
     * @param TokenList $tokenList
     * @return boolean 
     */
    private function checkForConcatenation(TokenList $tokenList)
    {
        $inhibitors = array(
            Token::T_CONSTANT_ENCAPSED_STRING,
            Token::T_CONST,
        );
        $prev = $tokenList->getPreviousNonWhitespace($this);
        $next = $tokenList->getNextNonWhitespace($this);
        
        if ($tokenList->isTokenIncluded(array($prev, $next), $inhibitors)) {
            $this->tokenName = Token::T_CONCAT;
            return true;
        }
        
        return false;
    }
    
}
