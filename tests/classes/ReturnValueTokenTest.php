<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for ReturnValueToken.
 * 
 */
class ReturnValueTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPToken 
     */
    private $token;
    
    public function testContentIsEmptied()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        $whitespace = new PHPToken(Token::T_WHITESPACE, ' ', 1);
        $tokenList->pushToken($whitespace);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('', $token->getContent());
    }
    
    public function testThrowsPPException()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        
        $this->setExpectedException('PythoPhant_Exception');
        $token->affectTokenList($tokenList);
    }
    
    public function testWhitespaceIsEmptied()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        $wsToken = new PHPToken('T_WHITESPACE', ' ', 1);
        $tokenList->pushToken($wsToken);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('', $wsToken->getContent());
    }
    
    public function testForceContentRendering()
    {
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $this->assertEquals('', $token->getContent());
        $token->setAuxValue(true);
        $this->assertEquals('int', $token->getContent());
    }
}