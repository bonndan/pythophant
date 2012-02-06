<?php

class CloseArrayToken extends CustomGenericToken
{
    /**
     * close array: will not turn into json notation if T_OPEN_ARRAY is on same line
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $previous = $this;
        while($previous = $tokenList->getPreviousNonWhitespace($previous)) {
            if ($previous->getTokenName() == 'T_OPEN_ARRAY') {
                return;
            }
        }
        $next = $tokenList->getNextNonWhitespace($this);
        if (!$next || $next->getTokenName() != 'T_ASSIGN') {
            $this->tokenName = 'T_JSON_CLOSE_ARRAY';
            $this->content = ')';
        }
    }
}