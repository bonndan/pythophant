<?php

class JsonToken extends PHPToken implements CustomToken
{
    const T_JSON_OPEN_ARRAY = "T_JSON_OPEN_ARRAY";
    const T_JSON_CLOSE_ARRAY = "T_JSON_CLOSE_ARRAY";
    const T_JSON_OPEN_OBJECT = "T_JSON_OPEN_OBJECT";
    const T_JSON_CLOSE_OBJECT = "T_JSON_CLOSE_OBJECT";
    const T_JSON_ASSIGN = "T_JSON_ASSIGN";
    
    const JSON_OPEN_ARRAY = "[";
    const JSON_CLOSE_ARRAY = "]";
    const JSON_OPEN_OBJECT = "{";
    const JSON_CLOSE_OBJECT = "}";
    const JSON_ASSIGN = ":";
    
    /**
     * this generic implementation does not affect the token list
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        if ($this->content == self::JSON_OPEN_ARRAY) {
            $tokenName = $tokenList->getPreviousNonWhitespace($this)
                ->getTokenName();
            $indicators = array(Token::T_ASSIGN, Token::T_JSON_TOKEN, 'T_IN');
            if (in_array($tokenName, $indicators)) {
                $this->content = 'array(';
            }
        }
        
        if ($this->content == self::JSON_OPEN_OBJECT) {
            $tokenName = $tokenList->getPreviousNonWhitespace($this)
                ->getTokenName();
            $indicators = array(Token::T_ASSIGN, Token::T_COMMA, 'T_IN');
            if (in_array($tokenName, $indicators)) {
                $this->content = '(object)array(';
            }
        }
        
        if ($this->content == self::JSON_CLOSE_OBJECT) {
                $this->content = ')';
        }
    }
    
    public function setAuxValue($value)
    {
    }
}