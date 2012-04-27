<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the NewlineToken
 * 
 *  
 */
class NewlineTokenTest extends PHPUnit_Framework_TestCase
{
    public function testGetContent()
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL, 1);
        $res = $token->getContent();
        $this->assertEquals(';'.PHP_EOL, $res);
    }
    
    public function testGetContentWithTwoNLs()
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL . PHP_EOL, 1);
        $res = $token->getContent();
        $this->assertEquals(';'.PHP_EOL . PHP_EOL, $res);
    }
    
    public function testGetContentWithWhitespaces()
    {
        $token = new NewLineToken(Token::T_NEWLINE, " " . PHP_EOL, 1);
        $res = $token->getContent();
        $this->assertEquals(' ;'.PHP_EOL, $res);
    }
    
    public function testCreateEmpty()
    {
        $token = NewLineToken::createEmpty(1);
        $this->assertInstanceOf('NewLineToken', $token);
        $this->assertEquals(Token::T_NEWLINE, $token->getTokenName());
        $this->assertEquals(PHP_EOL, $token->getContent());
        $this->assertEquals(1, $token->getLine());
    }
}