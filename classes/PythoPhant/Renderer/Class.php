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
     * @var PythoPhant_Renderer_TokenList 
     */
    private $preambleRenderer;
    
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
        
        $preamble = $this->class->getPreamble();
        if ($preamble !== null) {
            $this->preambleRenderer = new PythoPhant_Renderer_TokenList($preamble);
        }
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
            $renderer->enableDebugging($debug);
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
        
        if ($this->preambleRenderer !== null) {
            $buffer .= $this->preambleRenderer->getPHPSource();
        }
        
        $buffer .= $this->class->getDocComment()->getContent() . PHP_EOL;
        $buffer .= 'class ' . $this->class->getName();
        if ($this->class->getExtends() !== null) {
            $buffer .= PHP_EOL . 'extends ' . $this->class->getExtends();
        }
        $implements = $this->class->getImplements();
        if (!empty($implements)) {
            $buffer .= PHP_EOL . 'implements ' . implode(', ', $implements);
        }
        $buffer .= PHP_EOL . PythoPhant_Grammar::T_OPEN_BLOCK . PHP_EOL;
        
        foreach ($this->renderers as $renderer) {
            $buffer .= $renderer->getPHPSource();
        }
        
        $buffer .= PythoPhant_Grammar::T_CLOSE_BLOCK . PHP_EOL . PHP_EOL;
        return $buffer;
    }
}
