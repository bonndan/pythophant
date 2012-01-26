<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $object;
    
    /**
     *
     */
    private $tokenFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->tokenFactory = $this->getMock('TokenFactory');
        $this->object = new Parser(
            $this->tokenFactory
        );
    }

    /**
     * testScanTokenList().
     */
    public function testprocessTokenList()
    {
        $tokenList = new TokenList();
        $tokenMock = $this->getMockBuilder('CustomGenericToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($tokenMock);
        
        $tokenMock->expects($this->once())
            ->method('affectTokenList');
        
        $this->object->processTokenList($tokenList);
    }

}

