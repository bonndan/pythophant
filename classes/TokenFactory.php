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
    
    /**
     * register a token name and associate content to it
     * 
     * @param string $tokenName contant-like name like T_NOT
     * @param string $content like "!"
     * 
     * @return TokenFactory
     */
    public function registerToken($tokenName, $content);
    
    /**
     * register an implementation which handles the given token type
     * 
     * @param string $tokenName
     * @param string $className
     * 
     * @return TokenFactory
     */
    public function registerImplementation($tokenName, $className);
}