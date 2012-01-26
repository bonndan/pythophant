<?php

class CloseArrayToken extends CustomGenericToken
{
    public function affectTokenList(TokenList $tokenList)
    {
        $next = $tokenList->getNextNonWhitespace($this);
        if (!$next || $next->getTokenName() != 'T_ASSIGN') {
            $this->content = ')';
        }
    }
}