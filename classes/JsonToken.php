<?php

/**
 * JsonToken
 * 
 * array / object notation using json syntax
 * 
 * stdclass or creation using curly braces, array using square braces
 * IF T_ASSIGN or T_COMMA are found before
 */
class JsonToken extends PHPToken implements CustomToken
{

    const JSON_OPEN_ARRAY = "[";
    const JSON_CLOSE_ARRAY = "]";
    const JSON_OPEN_OBJECT = "{";
    const JSON_CLOSE_OBJECT = "}";
    const JSON_ASSIGN = ":";

    /**
     * names of preceding tokens which trigger the json mode
     * 
     * @return array 
     */
    public static function getOpenIndicators()
    {
        $indicators = array(
            Token::T_ASSIGN,
            Token::T_JSON_ASSIGN,
            Token::T_COMMA,
            Token::T_OPEN_BRACE,
            Token::T_IN,
        );
        
        return $indicators;
    }

    /**
     * this token does not affect other token, but changes its content
     * 
     * @param TokenList $tokenList
     * 
     * @todo myArray[ myVar : somefunc(myVar)]
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $indicators = $this->getOpenIndicators();
        
        if ($this->content == self::JSON_OPEN_ARRAY) {
            $tokenName = $tokenList->getPreviousNonWhitespace($this)
                ->getTokenName();
            
            if (in_array($tokenName, $indicators)) {
                $this->content = 'array(';
                $this->tokenName = Token::T_JSON_OPEN_ARRAY;
            }
        }

        if ($this->content == self::JSON_OPEN_OBJECT) {
            $tokenName = $tokenList->getPreviousNonWhitespace($this)
                ->getTokenName();
            if (in_array($tokenName, $indicators)) {
                $this->content = '(object)array(';
                $this->tokenName = Token::T_JSON_OPEN_OBJECT;
            }
        }

        if ($this->content == self::JSON_CLOSE_ARRAY) {
            $previous = $this;
            while ($previous = $tokenList->getPreviousNonWhitespace($previous)) {
                $tokenName = $previous->getTokenName();
                $indicators = array(Token::T_JSON_OPEN_ARRAY, Token::T_CLOSE_BRACE);
                if ($tokenName == Token::T_OPEN_ARRAY) {
                    return;
                } elseif (in_array($tokenName, $indicators)) {
                    $this->content = ')';
                    return;
                }
            }
            
            $next = $tokenList->getNextNonWhitespace($this);
            if (!$next || $next->getTokenName() != Token::T_ASSIGN) {
                $this->content = ')';
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
    public function setAuxValue($value)
    {
        /* unused */
    }

}