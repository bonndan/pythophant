<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * ThisTokenTest
 * 
 * 
 */
class ThisTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testDollarIsPrepended()
    {
        $token = new ThisToken('T_THIS', 'this', 1);
        $res = $token->getContent();
        
        $this->assertEquals('$this', $res);
    }
    
    public function testAtIsConverted()
    {
        $token = new ThisToken('T_THIS', '@', 1);
        $res = $token->getContent();
        
        $this->assertEquals('$this->', $res);
    }
}