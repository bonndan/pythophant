<?php
namespace PythoPhant\Core;

use PythoPhant\TokenList;

/**
 * Parser interface
 * 
 * @package PythoPhant
 */
interface Parser
{
    /**
     * find the class or structure
     * 
     * @param TokenList $tokenList
     */
    public function parseElement(TokenList $tokenList);
    
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
    public function getElement();
}
