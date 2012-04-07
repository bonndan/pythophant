<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the ExclamationToken
 * 
 *  
 */
class ExclamationTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ExclamationToken 
     */
    private $token;
    
    /**
     * 
     */
    public function setup()
    {
        $this->token = new ExclamationToken(Token::T_EXCLAMATION, '!', 0);
    }
    
    public function testTokenAfterClosingBraceIsPlaceHolder()
    {
        $tokenList = new TokenList;
        $moved    = new PHPToken(Token::T_STRING, 'test', 0);
        $func    = new PHPToken(Token::T_STRING, 'myFunc', 0);
        $opening = new PHPToken(Token::T_OPEN_BRACE, '(', 0);
        $closing = new PHPToken(Token::T_CLOSE_BRACE, ')', 0);
        
        $tokenList->pushToken($moved);
        $tokenList->pushToken($func);
        $tokenList->pushToken($opening);
        $tokenList->pushToken($closing);
        $tokenList->pushToken($this->token);
        
        $this->token->affectTokenList($tokenList);
        $this->assertEquals('', $this->token->getContent());
        $index = $tokenList->getTokenIndex($opening);
        $next = $tokenList->offsetGet($index+1);
        $this->assertEquals($next, $moved);
    }
    
    public function testTokenAfterCommaWithArgAndClosingBraceIsPlaceHolder()
    {
        $tokenList = new TokenList;
        $moved    = new PHPToken(Token::T_STRING, 'test', 0);
        $func    = new PHPToken(Token::T_STRING, 'myFunc', 0);
        $opening = new PHPToken(Token::T_OPEN_BRACE, '(', 0);
        $arg = new PHPToken(Token::T_CONSTANT_ENCAPSED_STRING, '"test"', 0);
        $closing = new PHPToken(Token::T_CLOSE_BRACE, ')', 0);
        
        $tokenList->pushToken($moved);
        $tokenList->pushToken($func);
        $tokenList->pushToken($opening);
        $tokenList->pushToken($arg);
        $tokenList->pushToken($closing);
        $tokenList->pushToken($this->token);
        
        $this->token->affectTokenList($tokenList);
        $this->assertEquals('', $this->token->getContent());
        $index = $tokenList->getTokenIndex($arg);
        $comma = $tokenList->offsetGet($index+1);
        $this->assertEquals(Token::T_COMMA, $comma->getTokenName());
        
        $index = $tokenList->getTokenIndex($comma);
        $next = $tokenList->offsetGet($index+1);
        $this->assertEquals($next, $moved);
    }
    
    public function testTokenElsewhereIsNegation()
    {
        $tokenList = new TokenList;
        $if    = new PHPToken(Token::T_IF, 'if', 0);
        $var    = new PHPToken(Token::T_STRING, 'someVar', 0);
        
        $tokenList->pushToken($if);
        $tokenList->pushToken($this->token);
        $tokenList->pushToken($var);
        
        $this->token->affectTokenList($tokenList);
        
        $this->assertEquals('T_NOT', $this->token->getTokenName());
        $this->assertEquals('!', $this->token->getContent());
    }
}