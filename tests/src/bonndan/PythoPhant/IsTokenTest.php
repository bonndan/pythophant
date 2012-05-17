<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test for the IsToken
 * 
 *  
 */
class IsTokenTest extends \PHPUnit_Framework_TestCase
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
        $token = new StringToken(Token::T_STRING, 'myVar', 0);
        $this->token = new IsToken(Token::T_IS, 'is', 0);
        $this->tokenList = new TokenList();
        $this->tokenList->pushToken($token);
        $this->tokenList->pushToken($this->token);
    }
    
    public function testIsFollowedByNothingThrowsException()
    {
        $this->setExpectedException("PythoPhant\Exception");
        $this->token->affectTokenList($this->tokenList);
    }
    
    public function testIsPrecededByNothingThrowsException()
    {
        $this->tokenList = new TokenList();
        $this->tokenList->pushToken($this->token);
        
        $tokenMock = new StringToken(Token::T_STRING, 'null', 0);
        $this->tokenList->pushToken($tokenMock);
        
        $this->setExpectedException("PythoPhant\Exception");
        $this->token->affectTokenList($this->tokenList);
    }
    
    public function testRemovesItself()
    {
        $tokenMock = new StringToken(Token::T_STRING, 'null', 0);
        
        $this->tokenList->pushToken($tokenMock);
        
        $this->token->affectTokenList($this->tokenList);
        $this->assertNull($this->token->getContent());
    }
    
    public function testIsFollowedByNativeFunctionStringNullToken()
    {
        $nextToken = new StringToken(Token::T_STRING, 'null', 0);
        
        $this->tokenList->pushToken($nextToken);
        
        $this->token->affectTokenList($this->tokenList);
        $this->assertEquals('is_null', $nextToken->getContent());
    }
    
    public function testIsFollowedByNativeFunctionStringToken()
    {
        $nextToken = new ReturnValueToken('T_RETURNVALUE', 'string', 0);
        
        $this->tokenList->pushToken($nextToken);
        
        $this->token->affectTokenList($this->tokenList);
        $this->assertEquals('is_string', $nextToken->getContent());
    }
    
    public function testIsFollowedByNotAndReturnValueToken()
    {
        $this->tokenList = new TokenList();
        
        $varToken = new StringToken('T_STRING', 'data', 0);
        $notToken = new ExclamationToken('T_NOT', 'not', 0);
        $nextToken = new ReturnValueToken(Token::T_RETURNVALUE, 'object', 0);
        
        $this->tokenList->pushToken($varToken);
        $this->tokenList->pushToken($this->token);
        $this->tokenList->pushToken($notToken);
        $this->tokenList->pushToken($nextToken);
        
        $this->token->affectTokenList($this->tokenList);
        $this->assertEquals('is_object', $nextToken->getContent());
        
        $index = $this->tokenList->getTokenIndex($nextToken);
        $expected = $this->tokenList->offsetGet($index-1);
        $this->assertEquals($expected, $varToken);
    }
    
    public function testIsFollowedByOtherToken()
    {
        $nextToken = new PHPToken('T_EQUALS', '==', 0);
        $this->tokenList->pushToken($nextToken);
        
        $this->token->affectTokenList($this->tokenList);
        $this->assertEquals('==', $nextToken->getContent());
        
        $this->assertNull($this->token->getContent());
    }

}