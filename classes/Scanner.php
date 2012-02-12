<?php
/**
 * File scanner interface
 * 
 *  
 */
interface Scanner
{
    /**
     * parses a string
     * 
     * @param string $source
     * 
     * @return void 
     */
    public function scanSource($source);

    /**
     * get the token list
     * 
     * @return TokenList 
     */
    public function getTokenList();
}