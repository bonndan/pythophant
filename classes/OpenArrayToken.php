<?php
/**
 * OpenArrayToken
 * 
 * "[" can be used to declare arrays usign json notation. an precending 
 * assignment is required
 * 
 */
class OpenArrayToken extends CustomGenericToken
{
    /**
     * turns into "array(" if predecing assignment is found
     * 
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $jsonIndicators = array(Token::T_ASSIGN, JsonToken::T_JSON_ASSIGN);
        $tokenName = $tokenList->getPreviousNonWhitespace($this)->getTokenName();
        if (in_array($tokenName, $jsonIndicators)) {
            $this->tokenName = Token::T_JSON_OPEN_ARRAY;
            $this->content = 'array(';
        }
    }
}