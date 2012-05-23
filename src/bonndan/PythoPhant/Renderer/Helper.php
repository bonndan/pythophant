<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;

/**
 * Helper
 * 
 * generic token renderer
 */
class Helper implements ReflectionRenderer
{
    /**
     * element to render
     * @var PythoPhant\Reflection\Element 
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
     * @param Element $element
     */
    public function setReflectionElement(Element $element)
    {
        $this->ref = $element;
    }
    
    /**
     * trigger debug
     * 
     * @param bool $debug
     * 
     * @return Helper 
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
        $this->ref->getDocComment()->setAnnotation('ppWatermark', array($watermarkText));
        return $this;
    }
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $renderer = new ClassOrInterface();
        $class = get_class($this->ref);
        if($class != 'PythoPhant\Reflection\RefClass')throw new \Exception($class);
        $renderer->setReflectionElement($this->ref);
        return $renderer->getPHPSource();
    }
}