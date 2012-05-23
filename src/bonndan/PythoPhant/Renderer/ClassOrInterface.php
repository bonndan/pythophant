<?php
namespace PythoPhant\Renderer;

use PythoPhant\Reflection\Element;
use PythoPhant\Reflection\ClassConst as RefClassConst;
use PythoPhant\Reflection\ClassVar as RefClassVar;
use PythoPhant\Reflection\Method as RefMethod;

use PythoPhant\Grammar;

/**
 * ClassOrInterface
 * 
 * A renderer for reflection classes and interfaces.
 * 
 * 
 */
class ClassOrInterface implements ReflectionRenderer
{
    /**
     * ref class
     * @var PythoPhant\Reflection\Class 
     */
    private $class;
    
    /**
     * @var PythoPhant\Renderer\TokenList 
     */
    private $preambleRenderer;
    
    /**
     * nested renderers
     * @var array Renderer[]
     */
    private $renderers = array();
    
    /**
     * constructor requires a reflection class
     * 
     * @param PythoPhant\Reflection\Element $class 
     */
    public function setReflectionElement(Element $element)
    {
        $this->class = $element;
        
        $preamble = $this->class->getPreamble();
        if ($preamble !== null) {
            $this->preambleRenderer = new TokenList($preamble);
        }
        
        foreach ($this->class->getConstants() as $const) {
            $renderer = new ClassConst();
            $renderer->setReflectionElement($const);
            $this->renderers[] = $renderer;
        }
        
        foreach ($this->class->getVars() as $var) {
            $renderer = new ClassVar();
            $renderer->setReflectionElement($var);
            $this->renderers[] = $renderer;
        }
        
        
        foreach ($this->class->getMethods() as $method) {
            $renderer = new Method();
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
        
        $buffer .= $this->class->getDocComment()->getRebuiltContent();
        $buffer .= 'class ' . $this->class->getName();
        if ($this->class->getExtends() !== null) {
            $buffer .= PHP_EOL . 'extends ' . $this->class->getExtends();
        }
        $implements = $this->class->getImplements();
        if (!empty($implements)) {
            $buffer .= PHP_EOL . 'implements ' . implode(', ', $implements);
        }
        $buffer .= PHP_EOL . Grammar::T_OPEN_BLOCK . PHP_EOL;
        
        foreach ($this->renderers as $renderer) {
            $buffer .= $renderer->getPHPSource();
        }
        
        $buffer .= Grammar::T_CLOSE_BLOCK . PHP_EOL . PHP_EOL;
        return $buffer;
    }
}
