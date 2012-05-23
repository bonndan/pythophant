<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;

/**
 * renderer for reflection elements
 * 
 *  
 */
interface ReflectionRenderer extends Renderer
{
    /**
     * inject a reflection element
     * 
     * @param PythoPhant\Reflection\Element $element
     */
    public function setReflectionElement(Element $element);
}