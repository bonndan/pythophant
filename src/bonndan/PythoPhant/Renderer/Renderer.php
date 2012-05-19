<?php
namespace PythoPhant\Renderer;

/**
 * Interface for renderers
 *  
 */
interface Renderer
{
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