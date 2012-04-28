<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the NewlineToken
 * 
 *  
 */
class NewlineTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testGetContent()
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL, 1);
        $res = $token->getContent();
        $this->assertEquals(PHP_EOL, $res);
    }
    
    public function testGetContentWithTwoNLs()
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL . PHP_EOL, 1);
        $token->setAuxValue(';');
        $res = $token->getContent();
        $this->assertEquals(';'.PHP_EOL . PHP_EOL, $res);
    }
    
    public function testGetContentPreservesWhitespaces()
    {
        $token = new NewLineToken(Token::T_NEWLINE, " " . PHP_EOL, 1);
        $res = $token->getContent();
        $this->assertEquals(' '.PHP_EOL, $res);
    }
    
    public function testCreateEmpty()
    {
        $token = NewLineToken::createEmpty(1);
        $this->assertInstanceOf('NewLineToken', $token);
        $this->assertEquals(Token::T_NEWLINE, $token->getTokenName());
        $this->assertEquals(PHP_EOL, $token->getContent());
        $this->assertEquals(1, $token->getLine());
    }
    
    /**
     * regular lines require semicolons 
     */
    public function testAffectTokenlistInsertsSemicolonInRegularLines()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('test', 'test',1));
        $tokenlist->pushToken($token);
        $tokenlist->pushToken(new NewLineToken('T_NEWLINE', PHP_EOL, 1));
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('test2', 'test2',1));
        $token->affectTokenList($tokenlist);
        $this->assertContains(';', $token->getContent());
    }
    
    public function testAffectTokenlistInsertsOpenBraceIfNextLineIndentedDeeper()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('test', 'test',1));
        $tokenlist->pushToken($token);
        $tokenlist->pushToken(IndentationToken::create(2));
        $tokenlist->pushToken(new StringToken('test2', 'test2',1));
        $tokenlist->pushToken(new NewLineToken('T_NEWLINE', PHP_EOL, 1));
        $token->affectTokenList($tokenlist);
        
        $this->assertContains('{', $token->getContent(), serialize($token->getContent()));
    }
    
    public function testAffectTokenlistInsertsCloseBraceIfNextLineIndentedLess()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(2));
        $tokenlist->pushToken(new StringToken('test', 'test',1));
        $tokenlist->pushToken($token);
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('test2', 'test2',1));
        $token->affectTokenList($tokenlist);
        
        $res = $token->getContent();
        $this->assertContains('}', $res, $res);
    }
    
    /**
     * ensures that blank lines have no semicolon
     */
    public function testAffectTokenlistInsertsNothingIfLineEmpty()
    {
        $token = NewLineToken::createEmpty(1);
        $tokenlist = new TokenList;
        $tokenlist->pushToken(new NewLineToken('T_NEWLINE', PHP_EOL, 1));
        $tokenlist->pushToken($token);
        $token->affectTokenList($tokenlist);
        
        $this->assertEquals('', trim($token->getContent()));
        $this->assertNotContains(';', $token->getContent());
    }
    
    /**
     * ensures that blank lines have no semicolon
     */
    public function testAffectTokenlistInsertsNothingIfLineHasOnlyIndentation()
    {
        $token = NewLineToken::createEmpty(1);
        $tokenlist = new TokenList;
        $tokenlist->pushToken(new NewLineToken('T_NEWLINE', PHP_EOL, 1));
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken($token);
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('T_STRING', 'test', 1));
        $token->affectTokenList($tokenlist);
        
        $this->assertEquals(PHP_EOL, $token->getContent());
    }
    
    public function testAffectTokenlistInsertsClosingBracesAtListEnd()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(3));
        $tokenlist->pushToken($token);
        $token->affectTokenList($tokenlist);
        
        $this->assertContains('}', $token->getContent(), serialize($tokenlist));
    }
}