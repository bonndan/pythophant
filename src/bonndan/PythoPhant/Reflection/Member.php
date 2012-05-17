<?php
namespace PythoPhant\Reflection;

use PythoPhant\Token as Token;

/**
 * Member
 * 
 * interface for class vars and methods
 */
interface Member extends Element
{
    /**
     * set visibility etc.
     * 
     * @param array $modifiers
     */
    public function setModifiers(array $modifiers);
    
    /**
     * set the return type
     * 
     * @param Token type
     */
    public function setType(Token $type);
    
    /**
     * add tokens to the body 
     */
    public function addBodyTokens(array $tokens);
    
    /**
     * returns the body tokens as TokenList
     * 
     * @return PythoPhant\TokenList 
     */
    public function getBodyTokenList();
}