<?php
namespace PythoPhant;

/**
 * CustomToken
 * 
 * A token which affects the surrounding tokens. Tokens are handled from left to
 * right. CustomTokens can affect any token in the list as well as remove or 
 * insert new ones.
 * 
 * 
 */
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