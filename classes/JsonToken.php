<?php
/**
 * array / object notation using json syntax
 * 
 *  
 */
class JsonToken extends PHPToken implements CustomToken
{
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
            $indicators = array(Token::T_ASSIGN, Token::T_JSON_TOKEN, Token::T_OPEN_BRACE, 'T_IN');
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
    
    /**
     * required method
     * @param type $value 
     */
    public function setAuxValue($value){}
}