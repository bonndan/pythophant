<?php

/**
 * string concatenation if previous or next are T_CONSTANT_ENCAPSED_STRING
 */
class PlusToken extends CustomGenericToken
{
    /**
     *
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        if ($tokenList->getPreviousNonWhitespace($this)->getTokenName() 
            == 'T_CONSTANT_ENCAPSED_STRING') {
            $this->content = '.';
        }
        
        if ($tokenList->getNextNonWhitespace($this)->getTokenName() 
            == 'T_CONSTANT_ENCAPSED_STRING') {
            $this->content = '.';
        }
    }
}