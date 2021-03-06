<?php 
require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Reflection_Class.
 */
class PythoPhant_Reflection_ClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * create an instance
     * 
     * @return PythoPhant_Class 
     */
    protected function getClass()
    {
        $content = "/**
 * Test Class 
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        return new PythoPhant_Reflection_Class('TestClass', $doc);
    }
    
    /**
     * ensures setting of the name 
     */
    public function testConstructor()
    {
        $class = $this->getClass();
        $this->assertAttributeEquals('TestClass', 'name', $class);
    }
    
    /**
     * test class var addition 
     */
    public function testAddVar()
    {
        $content = "/**
 * some thing
 * @var string
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        $var = new PythoPhant_Reflection_ClassVar('myVar', $doc);
        
        $class = $this->getClass();
        $class->addVar($var);
        $this->assertContains($var, $class->getVars());
    }

    /**
     * test setter of implemented interfaces 
     */
    public function testSetImplements()
    {
        $class = $this->getClass();
        $interfaces = array(
            'MyInterface1',
            new StringToken('T_STRING', 'MyInterface2', 0)
        );
        $class->setImplements($interfaces);
        
        $impl = $class->getImplements();
        $this->assertContains('MyInterface1', $impl);
        $this->assertContains('MyInterface2', $impl);
    }
    
    /**
     * 
     */
    public function testParseListAffections()
    {
        $content = "/**
 * some thing
 * @var string
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        
        $class = $this->getClass();
        $var = new PythoPhant_Reflection_ClassVar('myVar', $doc);
        $class->addVar($var);
        $method = new PythoPhant_Reflection_Function('myFunc', $doc);
        $class->addMethod($method);
        
        $parser = $this->getMock('Parser');
        $parser->expects($this->exactly(2))
            ->method('processTokenList');
        $class->parseListAffections($parser);
    }
}
