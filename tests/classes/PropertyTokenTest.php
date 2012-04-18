<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * PropertyTokenTest
 */
class PropertyTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyToken 
     */
    private $token;
    
    /**
     * 
     */
    public function setup()
    {
        parent::setUp();
        $this->token = new PropertyToken(PropertyToken::TOKEN_NAME, PropertyToken::TOKEN_VALUE, 1);
    }
    
    public function testGetContentIsNull()
    {
        $this->assertNull($this->token->getContent());
    }
    
    private function getTokenList()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken($this->token);
        $tokenList->pushToken(new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1));
        $tokenList->pushToken(new StringToken(Token::T_STRING, 'myVar', 1));
        
        return $tokenList;
    }
    
    public function testAffectTokenListThrowsException()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken($this->token);
        $tokenList->pushToken(new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1));
        
        $this->setExpectedException('PythoPhant_Exception');
        $this->token->affectTokenList($tokenList);
    }
    
    public function testAffectTokenList()
    {
        $tokenList = $this->getTokenList();
        
        $scanner = $this->getMockBuilder('Scanner')
            ->disableOriginalConstructor()
            ->getMock();
        $scanner->expects($this->exactly(2))
            ->method('scanSource');
        $scanner->expects($this->exactly(2))
            ->method('getTokenList')
            ->will($this->returnValue(array(IndentationToken::create(1))));
        
        $this->token->setScanner($scanner);
        $this->token->affectTokenList($tokenList);
        
        $this->assertEquals(5, $tokenList->count());
    }
}
