<?php

class OpenArrayToken extends CustomGenericToken
{
    public function affectTokenList(TokenList $tokenList)
    {
        $tokenName = $tokenList->getPreviousNonWhitespace($this)->getTokenName();
        if (in_array($tokenName, array('T_ASSIGN', 'T_IN'))) {
            $this->content = 'array(';
        }
    }
}