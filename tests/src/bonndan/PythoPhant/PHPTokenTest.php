<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class PhpTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPToken 
     */
    private $token;
    
    public function makeToken($tokenName, $content, $line)
    {
        $this->token = new PHPToken($tokenName, $content, $line);
    }

    public function testConstructor()
    {
        $this->makeToken('T_STRING', 'a', 1);
        $this->assertInstanceOf("PythoPhant\PHPToken", $this->token);
        
        $this->assertSame('a', $this->token->getContent());
        $this->assertSame(1, $this->token->getLine());
        $this->assertSame('T_STRING', $this->token->getTokenName());
    }
    
    public function testSetContentException()
    {
        $this->makeToken('T_STRING', 'a', 1);
        $this->setExpectedException('\InvalidArgumentException');
        $this->token->setContent(array());
    }
    
}