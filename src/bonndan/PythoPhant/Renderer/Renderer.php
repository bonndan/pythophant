<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;

/**
 * Interface for renderers
 *  
 * @package PythoPhant
 */
interface Renderer
{
    /**
     * inject the tokens
     * 
     * @param PythoPhant\Reflection\Element $element
     */
    public function setReflectionElement(Element $element);
    
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