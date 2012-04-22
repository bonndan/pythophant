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
}