<?php

class OpenArrayToken extends CustomGenericToken
{
    /**
     *
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $tokenName = $tokenList->getPreviousNonWhitespace($this)->getTokenName();
        if (in_array($tokenName, array('T_ASSIGN', 'T_JSON_ASSIGN', 'T_IN'))) {
            $this->tokenName = 'T_JSON_OPEN_ARRAY';
            $this->content = 'array(';
        }
    }
}