<?php

class OpenArrayToken extends CustomGenericToken
{
    /**
     *
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $jsonIndicators = array(Token::T_ASSIGN, JsonToken::T_JSON_ASSIGN);
        $tokenName = $tokenList->getPreviousNonWhitespace($this)->getTokenName();
        if (in_array($tokenName, $jsonIndicators)) {
            $this->tokenName = JsonToken::JSON_OPEN_ARRAY;
            $this->content = 'array(';
        }
    }
}