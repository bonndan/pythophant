<?php
/**
 * Interface for renderers
 *  
 * @package PythoPhant
 */
interface PythoPhant_Renderer
{
    /**
     * inject the tokens
     * 
     * @param PythoPhant_Reflection_Element $element
     */
    public function setReflectionElement(PythoPhant_Reflection_Element $element);
    
    /**
     * enable or disable debugging mode
     * 
     * @param bool $debug 
     * 
     * @return Renderer
     */
    public function enableDebugging($debug);
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource();
    
}