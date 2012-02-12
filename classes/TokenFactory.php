<?php
/**
 * TokenFactory interface
 * 
 * @package PythoPhant 
 */
interface TokenFactory
{
    /**
     * get the name of a token or tokenized array
     * 
     * @param array|string $tokenized
     *
     * @return string
     */
    public function getTokenName($tokenized);
    
    /**
     * create a token by passing its tokenName. content and line number are optional.
     * 
     * @param string $tokenName
     * @param string $content
     * @param int    $line 
     * 
     * @return Token
     */
    public function createToken($tokenName, $content = NULL, $line = 0);
}