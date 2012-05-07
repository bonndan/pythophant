<?php 
require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Reflection_Interface.
 */
class PythoPhant_Reflection_InterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * create an instance
     * 
     * @return PythoPhant_Class 
     */
    protected function getClass()
    {
        $content = "/**
 * Test Internface 
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        return new PythoPhant_Reflection_Interface('TestInterface', $doc);
    }
    
    /**
     * 
     */
    public function testSetPreamble()
    {
        $tokenList = new TokenList();
        $class = $this->getClass();
        $class->setPreamble($tokenList);
        $this->assertAttributeEquals($tokenList, 'preamble', $class);
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
        $method = new PythoPhant_Reflection_Function('myFunction', $doc);
        
        $class = $this->getClass();
        $class->addMethod($method);
        $this->assertContains($method, $class->getMethods());
    }
    
    /**
     * test class var addition 
     */
    public function testAddConstant()
    {
        $content = "/**
 * some thing
 * @var string
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        $const = new PythoPhant_Reflection_ClassConst('MY_CONST', $doc);
        
        $class = $this->getClass();
        $class->addConstant($const);
        $this->assertContains($const, $class->getConstants());
    }
    
    public function testSetExtendsWithString()
    {
        $class = $this->getClass();
        $class->setExtends('MyClass');
        $this->assertAttributeEquals('MyClass', 'extends', $class);
    }
    
    public function testSetExtendsWithStringToken()
    {
        $class = $this->getClass();
        $class->setExtends(new StringToken('T_STRING', 'MyClass', 0));
        $this->assertAttributeEquals('MyClass', 'extends', $class);
    }
}
