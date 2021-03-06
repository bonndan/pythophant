<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Function.
 * 
 * 
 */
class PythoPhant_Reflection_FunctionTest extends PHPUnit_Framework_TestCase
{
    /**
     * ensures the constructor stores all params 
     */
    public function testConstructor()
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
        
        $function = new PythoPhant_Reflection_Function('testFunction', $doc);
        
        $this->assertEquals('testFunction', $function->getName());
        
        $res = $function->getParams();
        $this->assertInternalType('array', $res);
        $this->assertEquals(2, count($res));
        $this->assertEquals('test', $res['test']->getName());
        $this->assertEquals('string', $res['test']->getType());
        $this->assertEquals('test2', $res['test2']->getName());
        $this->assertEquals('SomeInterface[]', $res['test2']->getType());
    }
    
    /**
     * ensures that tokens can be added to the list of body tokens 
     */
    public function testAddBodyTokens()
    {
        $content = 
"/**
 * test
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        
        $function = new PythoPhant_Reflection_Function('testFunction', $doc);
        
        $token = new StringToken('T_STRING', 'myString', 0);
        $tokens = array($token);
        $function->addBodyTokens($tokens);
        $this->assertAttributeContains($token, "bodyTokens", $function);
    }
    
    public function testSetTypeWithReturnValueToken()
    {
        $content = 
"/**
 * test
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        
        $function = new PythoPhant_Reflection_Function('testFunction', $doc);
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'string', 0);
        $function->setType($token);
        $this->assertAttributeEquals($token, 'type', $function);
    }
    
    /**
     * ensures read signature is read properly
     */
    public function testSetSignatureFromDocComment()
    {
                $content = 
"/**
  * test
  * @param string myVariable
  * @param string myVar = null
  * @return string
  */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        
        $function = new PythoPhant_Reflection_Function('testFunction', $doc);
        
        $params = $function->getParams();
        $this->assertArrayHasKey('myVariable', $params);
        $this->assertArrayHasKey('myVar', $params);
        $this->assertInstanceOf('PythoPhant_Reflection_FunctionParam', $params['myVar']);
        $this->assertEquals('null', $params['myVar']->getDefault());
    }
}
    