<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the InToken
 * 
 *  
 */
class InTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IsToken 
     */
    private $token;
    /**
     * @var TokenList 
     */
    private $tokenList;
    
    public function setUp()
    {
        parent::setUp();
        $this->token = new InToken(Token::T_IN, 'in', 0);
        $this->tokenList = new TokenList();
    }
    
    public function testMissingPrecedingExpressionCausesException()
    {
        $this->tokenList->pushToken($this->token);
        $this->setExpectedException('PythoPhant_Exception');
        $this->token->affectTokenList($this->tokenList);
    }
    
    public function testMissingTrailingExpressionCausesException()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $this->setExpectedException('PythoPhant_Exception');
        $this->token->affectTokenList($this->tokenList);
    }
    
    public function testColonTokenIsInjected()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $this->tokenList->pushToken(clone $token);
        
        $this->token->affectTokenList($this->tokenList);
        $index = $this->tokenList->getTokenIndex($this->token) + 1;
        $colon = $this->tokenList->offsetGet($index);
        $this->assertInstanceOf('ColonToken', $colon);
    }
    
    public function testColonTokenIsNotInjectedIfBracePresent()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $brace = new StringToken(Token::T_STRING, '(', 0);
        $this->tokenList->pushToken($brace);
        $this->tokenList->pushToken(clone $token); //list would not end here, prevents outofboundexception
        
        $this->token->affectTokenList($this->tokenList);
        $index = $this->tokenList->getTokenIndex($this->token) + 1;
        $actual = $this->tokenList->offsetGet($index);
        $this->assertEquals($brace, $actual);
    }
    
    public function testColonTokenIsNotInjectedIfColonTokenPresent()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $brace = new ColonToken(Token::T_COLON, ':', 0);
        $this->tokenList->pushToken($brace);
        $this->tokenList->pushToken(clone $token);
        
        $this->token->affectTokenList($this->tokenList);
        $index = $this->tokenList->getTokenIndex($this->token) + 1;
        $actual = $this->tokenList->offsetGet($index);
        $this->assertEquals($brace, $actual);
    }
    
    public function testCommaIsInjected()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $brace = new ColonToken(Token::T_COLON, ':', 0);
        $this->tokenList->pushToken($brace);
        $token2 = new StringToken(Token::T_STRING, 'otherVar', 0);
        $this->tokenList->pushToken($token2);
        
        $this->token->affectTokenList($this->tokenList);
        $index = $this->tokenList->getTokenIndex($token) + 1;
        $actual = $this->tokenList->offsetGet($index);
        $this->assertInstanceOf('PHPToken', $actual);
        $this->assertEquals(', ', $actual->getContent());
    }
    
    public function testContentIsReplaced()
    {
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
        $brace = new ColonToken(Token::T_COLON, ':', 0);
        $this->tokenList->pushToken($brace);
        $token2 = new StringToken(Token::T_STRING, 'otherVar', 0);
        $this->tokenList->pushToken($token2);
        
        $this->token->affectTokenList($this->tokenList);
        $index = $this->tokenList->getTokenIndex($token) + 1;
        $this->assertEquals('in_array', $this->token->getContent());
    }
}