<?php
/**
 * PythoPhant_Reflection_Member
 * 
 * interface for class vars and methods
 */
interface PythoPhant_Reflection_Member extends PythoPhant_Reflection_Element
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
     * @return TokenList 
     */
    public function getBodyTokenList();
}