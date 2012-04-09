<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Renderer
 * 
 * .
 */
class PythoPhant_RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * sut
     * @var PythoPhant_Renderer 
     */
    private $renderer;
    
    public function setup()
    {
        parent::setUp();
        $this->renderer = new PythoPhant_Renderer();
    }
    
    public function testSetTokenList()
    {
        $tokenList = $this->getMock('TokenList');
        $this->renderer->setTokenList($tokenList);
        $this->assertAttributeEquals($tokenList, 'tokenList', $this->renderer);
    }
    
    public function testEnableDebugging()
    {
        $res = $this->renderer->enableDebugging(true);
        $this->assertAttributeEquals(true, 'debug', $this->renderer);
        $this->assertSame($this->renderer, $res);
    }
    
    public function testAddWatermark()
    {
        $tokenList = new TokenList();
        $token = $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
        $token->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('<?php'));
        $token->expects($this->once())
            ->method('setContent')
            ->with('<?php /** test */' . PHP_EOL);
        
        $tokenList->pushToken($token);
        
        $this->renderer->setTokenList($tokenList);
        $this->renderer->addWaterMark('test');
    }
    
    public function testGetPHPSource()
    {
        $tokenList = new TokenList();
        $token = $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
        $token->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('<?php'));
        $token2 = $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
        $token2->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(' die();'));
        
        $tokenList->pushToken($token);
        $tokenList->pushToken($token2);
        
        $this->renderer->setTokenList($tokenList);
        $res = $this->renderer->getPHPSource();
        $this->assertEquals('<?php die();', $res);
    }
    
    public function testGetPHPSourceDebug()
    {
        $tokenList = new TokenList();
        $token = $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
        $token->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue('T_STRING'));
        $token->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('<?php'));
        
        $tokenList->pushToken($token);
        
        $this->renderer->setTokenList($tokenList);
        $res = $this->renderer->enableDebugging(true);
        $res = $this->renderer->getPHPSource();
        $this->assertEquals('T_STRING<?php', $res);
    }
}
   