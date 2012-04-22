<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Reflection_FunctionParamTest.
 * 
 * 
 */
class PythoPhant_Reflection_FunctionParamTest extends PHPUnit_Framework_TestCase
{
    /**
     * ensures the constructor stores all params 
     */
    public function testConstructor()
    {
        $param = new PythoPhant_Reflection_FunctionParam('string', 'myVar', 'array()');
        $this->assertEquals('string', $param->getType());
        $this->assertEquals('myVar', $param->getName());
        $this->assertAttributeEquals('array()', 'default', $param);
    }
    
    /**
     * 
     */
    public function testTokenListWithoutDefault()
    {
        $param = new PythoPhant_Reflection_FunctionParam('string', 'myVar');
        $res = $param->toTokenList();
        $this->assertInstanceOf('TokenList', $res);
        $this->assertEquals(3, $res->count());
        $this->assertInstanceOf('ReturnValueToken', $res[0]);
        $this->assertInstanceOf('StringToken', $res[2]);
    }
    
    /**
     * 
     */
    public function testTokenListWithDefault()
    {
        $param = new PythoPhant_Reflection_FunctionParam('string', 'myVar', 'array()');
        $res = $param->toTokenList();
        $this->assertInstanceOf('TokenList', $res);
        $this->assertEquals(7, $res->count());
        $this->assertEquals('=', $res[4]->getContent());
        $this->assertEquals('array()', $res[6]->getContent());
    }
}