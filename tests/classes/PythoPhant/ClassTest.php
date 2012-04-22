<?php 

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Class.
 */
class PythoPhant_ClassTest extends PHPUnit_Framework_TestCase
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
        return new PythoPhant_Class('TestClass', $doc);
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
        $var = new PythoPhant_ClassVar('myVar', $doc);
        
        $class = $this->getClass();
        $class->addVar($var);
        $this->assertAttributeContains($var, 'vars', $class);
    }
    
    /**
     * test class var addition 
     */
    public function testAddMethod()
    {
        $content = "/**
 * some thing
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        $method = new PythoPhant_Function('myFunction', $doc);
        
        $class = $this->getClass();
        $class->addMethod($method);
        $this->assertContains($method, $class->getMethods());
    }
}
