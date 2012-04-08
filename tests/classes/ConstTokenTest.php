<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the ExclamationToken
 * 
 *  
 */
class ConstTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ConstToken 
     */
    private $token;
    
    public function setup()
    {
        parent::setup();
        $this->token = new ConstToken(Token::T_CONSTANT_ENCAPSED_STRING, '"something"', 1);
    }
    
    /**
     *
     * @return \TokenList 
     */
    public function getTokenList()
    {
        return new TokenList();
    }
    
    public function testConcatenationIsInjectedBeforeIfIndicated()
    {
        $tokenList = $this->getTokenList();
        $first = new ConstToken(Token::T_CONSTANT_ENCAPSED_STRING, '"first"', 1);
        $tokenList->pushToken($first);
        $tokenList->pushToken($this->token);
        
        $this->token->affectTokenList($tokenList);
        $index = $tokenList->getTokenIndex($this->token) - 1;
        $concat = $tokenList->offsetGet($index);
        $this->assertInstanceOf('StringToken', $concat);
        $this->assertEquals('T_CONCAT', $concat->getTokenName());
        $this->assertEquals('. ', $concat->getContent());
    }
    
    public function testConcatenationIsNotInjectedBeforeIfNotIndicated()
    {
        $tokenList = $this->getTokenList();
        $first = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        $tokenList->pushToken($first);
        $tokenList->pushToken($this->token);
        
        $this->token->affectTokenList($tokenList);
        $index = $tokenList->getTokenIndex($this->token) - 1;
        $concat = $tokenList->offsetGet($index);
        $this->assertEquals(2, $tokenList->count());
        $this->assertContains($first, $tokenList);
        $this->assertContains($this->token, $tokenList);
    }
    
    public function testConcatenationIsInjectedAfterIfIndicated()
    {
        $tokenList = $this->getTokenList();
        $tokenList->pushToken($this->token);
        $first = new ConstToken(Token::T_CONSTANT_ENCAPSED_STRING, '"first"', 1);
        $tokenList->pushToken($first);
        
        
        $this->token->affectTokenList($tokenList);
        $index = $tokenList->getTokenIndex($this->token) + 1;
        $concat = $tokenList->offsetGet($index);
        $this->assertInstanceOf('StringToken', $concat);
        $this->assertEquals('T_CONCAT', $concat->getTokenName());
        $this->assertEquals('. ', $concat->getContent());
    }
    
    public function testConcatenationIsNotInjectedAfterIfNotIndicated()
    {
        $tokenList = $this->getTokenList();
        $tokenList->pushToken($this->token);
        $first = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        $tokenList->pushToken($first);
        
        $this->token->affectTokenList($tokenList);
        $this->assertEquals(2, $tokenList->count());
        $this->assertContains($first, $tokenList);
        $this->assertContains($this->token, $tokenList);
    }
}