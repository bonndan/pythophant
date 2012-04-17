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
}