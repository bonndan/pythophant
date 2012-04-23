<?php
/**
 * PythoPhant_Renderer_Class
 * 
 * A renderer for reflection classes.
 * 
 * 
 */
class PythoPhant_Renderer_Class implements PythoPhant_Renderer
{
    /**
     * ref class
     * @var PythoPhant_Reflection_Class 
     */
    private $class;
    
    /**
     * nested renderers
     * @var array 
     */
    private $renderers = array();
    
    /**
     * constructor requires a reflection class
     * 
     * @param PythoPhant_Reflection_Class $class 
     */
    public function setReflectionElement(PythoPhant_Reflection_Element $element)
    {
        $this->class = $element;
        
        
        foreach ($this->class->getConstants() as $const) {
            $renderer = new PythoPhant_Renderer_ClassConst();
            $renderer->setReflectionElement($const);
            $this->renderers[] = $renderer;
        }
        
        foreach ($this->class->getVars() as $var) {
            $renderer = new PythoPhant_Renderer_ClassVar();
            $renderer->setReflectionElement($var);
            $this->renderers[] = $renderer;
        }
        
        
        foreach ($this->class->getMethods() as $method) {
            $renderer = new PythoPhant_Renderer_Function();
            $renderer->setReflectionElement($method);
            $this->renderers[] = $renderer;
        }
    }
    
    /**
     * enable or disable debugging
     * 
     * @param boolean $debug 
     * 
     * @return void
     */
    public function enableDebugging($debug)
    {
        foreach ($this->renderers as $renderer) {
            $renderer->enableDebuggin($debug);
        }
    }

    /**
     * returns the php code
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $buffer = '<?php' . PHP_EOL;
        
        $buffer .= $this->class->getDocComment()->getContent();
        $buffer .= 'class ' . $this->class->getName();
        $buffer .= PHP_EOL . PythoPhant_Grammar::T_OPEN_BLOCK . PHP_EOL;
        
        foreach ($this->renderers as $renderer) {
            $buffer .= $renderer->getPHPSource();
        }
        
        $buffer .= PythoPhant_Grammar::T_CLOSE_BLOCK . PHP_EOL . PHP_EOL;
        return $buffer;
    }
}
