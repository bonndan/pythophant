<?php

/**
 * string concatenation if previous or next are T_CONSTANT_ENCAPSED_STRING
 */
class ColonToken extends CustomGenericToken
{
    /**
     * checks the tokenlist for previous tokens whether it is a colon or json assignment
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $token = $this;
        while ($token = $tokenList->getPreviousNonWhitespace($token)) {
            if (in_array($token->getTokenName(), array('T_CASE', 'T_QUESTION'))) {
                return;
            }
        }
        
        $this->tokenName = JsonToken::T_JSON_ASSIGN;
        $this->content   = '=>';
    }
}