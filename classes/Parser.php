<?php
/**
 * Parser interface
 * 
 * @package PythoPhant
 */
interface Parser
{
    /**
     * process a token list
     * 
     * @param TokenList $tokenList
     */
    public function processTokenList(TokenList $tokenList);
    
    /**
     * returns the representation of the class or interface which is currently
     * built
     * 
     * @return PythoPhant_Reflection_Element
     */
    public function getReflectionElement();
}
