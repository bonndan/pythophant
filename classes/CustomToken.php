<?php

interface CustomToken extends Token
{
    /**
     * manipulate the passed token list
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList);

    /**
     * set a helper value, can be anything, depends on the implementation
     * 
     * @param mixed $value
     * @return CustomToken|Null 
     */
    public function setAuxValue($value);
}