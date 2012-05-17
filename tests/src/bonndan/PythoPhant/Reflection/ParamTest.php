<?php
namespace PythoPhant\Reflection;

require_once dirname(__FILE__) . '/bootstrap.php';


/**
 * Test class for Param.
 * 
 * 
 */
class ParamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ensures the constructor stores all params 
     */
    public function testConstructor()
    {
        $param = new Param('string', 'myVar', 'array()');
        $this->assertEquals('string', $param->getType());
        $this->assertEquals('myVar', $param->getName());
        $this->assertAttributeEquals('array()', 'default', $param);
    }
    
    /**
     * 
     */
    public function testTokenListWithoutDefault()
    {
        $param = new Param('string', 'myVar');
        $res = $param->toTokenList();
        $this->assertInstanceOf("PythoPhant\TokenList", $res);
        $this->assertEquals(3, $res->count());
        $this->assertInstanceOf("PythoPhant\ReturnValueToken", $res[0]);
        $this->assertInstanceOf("PythoPhant\StringToken", $res[2]);
    }
    
    /**
     * 
     */
    public function testTokenListWithDefault()
    {
        $param = new Param('string', 'myVar', 'array()');
        $res = $param->toTokenList();
        $this->assertInstanceOf("PythoPhant\TokenList", $res);
        $this->assertEquals(7, $res->count());
        $this->assertEquals('=', $res[4]->getContent());
        $this->assertEquals('array()', $res[6]->getContent());
    }
}