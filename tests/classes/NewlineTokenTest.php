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
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken(new StringToken('test2', 'test2',1));
        $token->affectTokenList($tokenlist);
        $this->assertAttributeEquals('STATE_REGULAR_LINE', 'state', $token);
        $this->assertContains(';', $token->getContent());
    }
    
    /**
     * regular lines require semicolons 
     */
    public function testAffectTokenlistIngnoresEmptyLine()
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
        $this->assertAttributeEquals('STATE_REGULAR_LINE', 'state', $token);
        $this->assertContains(';', $token->getContent());
    }
    
    /**
     * ensures a new token is injected after the newline token 
     */
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
        
        $index = $tokenlist->getTokenIndex($token) - 1;
        $openBrace = $tokenlist->offsetGet($index);
        $this->assertEquals('T_OPEN_BLOCK', $openBrace->getTokenName());
        $this->assertEquals(' {', $openBrace->getContent());
    }
    
    public function testAffectTokenlistInsertsCloseBraceIfNextLineIndentedLess()
    {
        $token = NewLineToken::createEmpty(1);
        $target = new StringToken('test2', 'test2',1);
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(2));
        $tokenlist->pushToken(new StringToken('test', 'test',1));
        $tokenlist->pushToken($token);
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken($target);
        $token->affectTokenList($tokenlist);
        
        $index = $tokenlist->getTokenIndex($target) - 1;
        $closeBrace = $tokenlist->offsetGet($index);
        
        $this->assertEquals('T_CLOSE_BLOCK', $closeBrace->getTokenName());
        $this->assertContains('}', $closeBrace->getContent());
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
        $tokenlist->pushToken(new StringToken('T_STRING', 'test', 1));
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
        $tokenlist->pushToken(new StringToken('T_STRING', 'test', 1));
        $tokenlist->pushToken($token);
        
        $token->affectTokenList($tokenlist);
        $this->assertAttributeEquals('STATE_LAST_LINE', 'state', $token);
        $this->assertContains(';', $token->getContent());
        $this->assertInstanceOf('PhpToken', $tokenlist->getNextNonWhitespace($token));
        $close = $tokenlist->getNextNonWhitespace($token);
        $this->assertEquals('T_CLOSE_BLOCK', $close->getTokenName());
        $this->assertNull($tokenlist->getNextNonWhitespace($close));
    }
    
    /**
     * ensure no brace 
     */
    public function testAffectTokenlistInsertsNoClosingBracesAtListEnd()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(IndentationToken::create(2));
        $tokenlist->pushToken(new StringToken('T_STRING', 'test', 1));
        $tokenlist->pushToken($token);
        $token->affectTokenList($tokenlist);
        
        $this->assertAttributeEquals('STATE_LAST_LINE', 'state', $token);
        $this->assertContains(';', $token->getContent());
        $this->assertEquals(3, count($tokenlist));
    }
    
    public function testIsLineEmpty()
    {
        $token = NewLineToken::createEmpty(1);
        $tokenlist = new TokenList;
        $tokenlist->pushToken(NewLineToken::createEmpty(1));
        $tokenlist->pushToken($token);
        
        $this->assertTrue($token->isLineEmpty($tokenlist));
    }
    
    public function testIsLineEmptyWithIndentation()
    {
        $token = NewLineToken::createEmpty(1);
        
        $tokenlist = new TokenList;
        $tokenlist->pushToken(NewLineToken::createEmpty(1));
        $tokenlist->pushToken(IndentationToken::create(1));
        $tokenlist->pushToken($token);
        
        $this->assertTrue($token->isLineEmpty($tokenlist));
    }
}