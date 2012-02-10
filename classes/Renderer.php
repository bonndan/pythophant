<?php
/**
 * tokenlist renderer
 *  
 * @package PythoPhant
 */
interface Renderer
{
    /**
     * inject the tokens
     * 
     * @param TokenList $tokenList 
     */
    public function setTokenList(TokenList $tokenList);
    
    /**
     * enable or disable debugging mode
     * 
     * @param bool $debug 
     * 
     * @return Renderer
     */
    public function enableDebugging($debug);
    
    /**
     * add a watermark text
     * 
     * @param string $watermarkText 
     * 
     * @return Renderer
     */
    public function addWaterMark($watermarkText);
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource();
    
}