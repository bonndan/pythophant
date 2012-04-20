<?php
/**
 * Macro 
 * 
 * a string containing placeholders for parameters
 */
interface Macro
{
    /**
     * set the raw, unprocessed source
     * 
     * @param string $source
     */
    public function setSource($source);
    
    /**
     * set the params to be injected
     * 
     * @param array $params 
     */
    public function setParams(array $params);
    
    /**
     * get the source as string
     * 
     * @return string 
     */
    public function getSource();
    
    /**
     * remove the php open_tag, indent if needed
     * 
     * @param TokenList $tokenList   token list to clena
     * @param int       $indentation indentation level to add
     */
    public function cleanTokenList(TokenList $tokenList, $indentation = 0);
}