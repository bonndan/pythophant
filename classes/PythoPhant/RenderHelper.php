<?php
/**
 * PythoPhant_Renderer
 * 
 * generic token renderer
 *  
 */
class PythoPhant_RenderHelper implements PythoPhant_Renderer
{
    /**
     * element to render
     * @var PythoPhant_Reflection_Element 
     */
    private $ref;
    
    /**
     * debug mode
     * @var bool 
     */
    private $debug = false;
    
    /**
     * inject the lreflection element to render
     * 
     * @param PythoPhant_Reflection_Element $element
     */
    public function setReflectionElement(PythoPhant_Reflection_Element $element)
    {
        $this->ref = $element;
    }
    
    /**
     * trigger debug
     * 
     * @param bool $debug
     * 
     * @return PythoPhant_RenderHelper 
     */
    public function enableDebugging($debug)
    {
        $this->debug = (bool)$debug;
        return $this;
    }
    
    /**
     * add a watermark comment to the first token
     * 
     * @param string $watermarkText 
     * 
     * @return void
     */
    public function addWaterMark($watermarkText)
    {
        $this->ref->getDocComment()->appendToLongDescription($watermarkText);
        return $this;
    }
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $renderer = new PythoPhant_Renderer_Class();
        $renderer->setReflectionElement($this->ref);
        return $renderer->getPHPSource();
    }
}