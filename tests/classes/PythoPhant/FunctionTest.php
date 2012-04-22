<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Function.
 * 
 * 
 */
class PythoPhant_FunctionTest extends PHPUnit_Framework_TestCase
{
    /**
     * ensures the constructor stores all params 
     */
    public function testConstructor()
    {
        $function = new PythoPhant_Function('testFunction');
        $this->assertEquals('testFunction', $function->getName());
    }
    
    /**
     * 
     */
    public function testSetSignatureFromDocComment()
    {
        $content = 
"/**
 * test
 *
 * @param string          test some var
 * @param SomeInterface[] test2 = array() some other var
 *
 * @return void
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        
        $function = new PythoPhant_Function('testFunction');
        $function->setSignatureFromDocComment($doc);
        $res = $function->getParams();
        $this->assertInternalType('array', $res);
        $this->assertEquals(2, count($res));
        $this->assertEquals('test', $res['test']->getName());
        $this->assertEquals('string', $res['test']->getType());
        $this->assertEquals('test2', $res['test2']->getName());
        $this->assertEquals('SomeInterface[]', $res['test2']->getType());
    }
}
    