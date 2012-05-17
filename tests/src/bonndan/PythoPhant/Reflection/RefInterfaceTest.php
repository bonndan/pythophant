<?php 
namespace PythoPhant\Reflection;

require_once dirname(__FILE__) . '/bootstrap.php';

use PythoPhant\DocCommentToken;
use PythoPhant\TokenList;
use PythoPhant\StringToken;


/**
 * Test class for PythoPhant_Reflection_Interface.
 */
class InterfaceTest extends \PHPUnit_Framework_TestCase
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
        return new RefInterface('TestInterface', $doc);
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
        $method = new Method('myFunction', $doc);
        
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
        $const = new ClassConst('MY_CONST', $doc);
        
        $class = $this->getClass();
        $class->addConstant($const);
        $this->assertContains($const, $class->getConstants());
    }
    
    public function testSetExtendsWithString()
    {
        $class = $this->getClass();
        $class->setExtends('MyClass');
        $this->assertEquals('MyClass', $class->getExtends());
    }
    
    public function testSetExtendsWithStringToken()
    {
        $class = $this->getClass();
        $class->setExtends(new StringToken('T_STRING', 'MyClass', 0));
        $this->assertEquals('MyClass', $class->getExtends());
    }
}
