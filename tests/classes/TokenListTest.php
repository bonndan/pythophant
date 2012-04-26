<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for TokenList.
 * Generated by PHPUnit on 2012-01-23 at 20:09:33.
 */
class TokenListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TokenList
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TokenList;
    }

    private function getTokenMock()
    {
        return $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
        /**
     * returns a token list filled with various tokens, containing the passed
     * token in its middle
     * 
     * @param Token $token
     * @return TokenList 
     */
    public function getFilledTokenListContaining(Token $token)
    {
        $tokenList = new TokenList();
        
        $tokenList->pushToken(new NewLineToken(Token::T_NEWLINE, PHP_EOL, 1));
        $tokenList->pushToken(new CustomGenericToken('T_LOGICAL_AND', 'and', 1));
        $tokenList->pushToken(new StringToken('T_STRING', 'myVar', 1));
        $tokenList->pushToken(new ColonToken('T_COLON', ':', 1));
        $tokenList->pushToken(new PHPToken(Token::T_WHITESPACE, 'whitespaceBefore', 0));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new PHPToken(Token::T_WHITESPACE, 'whitespaceAfter', 0));
        $tokenList->pushToken(new CustomGenericToken('start', 'test', 0));
        $tokenList->pushToken(new StringToken('T_STRING', 'myVar2', 1));
        $tokenList->pushToken(new NewLineToken(Token::T_NEWLINE, PHP_EOL, 1));
        
        return $tokenList;
    }
    
    /**
     */
    public function testPushTokenIsInsertedAtEnd()
    {
        $tokenMock = $this->getTokenMock();
        $this->assertFalse($this->object->offsetExists(0));
        
        $this->object->pushToken($tokenMock);
        $this->assertTrue($this->object->offsetExists(0));
        $this->assertEquals($tokenMock, $this->object->offsetGet(0));
    }

    public function testInjectTokenWithIntPosition()
    {
        $tokenMock = $this->getTokenMock();
        $this->assertFalse($this->object->offsetExists(0));
        $this->object->pushToken($tokenMock);
        
        $tokenMock2 = $this->getTokenMock();
        $this->object->injectToken($tokenMock2, 0);
        
        $this->assertEquals($tokenMock2, $this->object->offsetGet(0));
        $this->assertEquals($tokenMock, $this->object->offsetGet(1));
    }
    
    public function testInjectTokenWithTokenPosition()
    {
        $tokenMock = $this->getTokenMock();
        $this->assertFalse($this->object->offsetExists(0));
        $this->object->pushToken($tokenMock);
        
        $tokenMock2 = $this->getTokenMock();
        $this->object->injectToken($tokenMock2, $tokenMock);
        
        $this->assertEquals($tokenMock2, $this->object->offsetGet(0));
        $this->assertEquals($tokenMock, $this->object->offsetGet(1));
    }

    public function testGetNextNonWhitespace1()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $this->object->pushToken($token);
        $w = new PHPToken('T_WHITESPACE', "whitespace", 1);
        $this->object->pushToken($w);
        $string = new StringToken('T_STRING', "needle", 1);
        $this->object->pushToken($string);
        
        $res = $this->object->getNextNonWhitespace($token);
        $this->assertEquals($string, $res);
    }
    
    public function testGetNextNonWhitespaceNull()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $this->object->pushToken($token);
        $w = new NewLineToken('T_NEWLINE', PHP_EOL, 1);
        $this->object->pushToken($w);
        $string = new StringToken('T_STRING', "needle", 1);
        $this->object->pushToken($string);
        
        $res = $this->object->getNextNonWhitespace($token);
        $this->assertNull($res);
    }


    public function testIsTokenIncluded()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $res = $this->object->isTokenIncluded(array($token), array('T_STRING'));
        $this->assertTrue($res);
    }
    
    public function testTokenIsNotIncluded()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $res = $this->object->isTokenIncluded(array($token), array('T_ASSIGN'));
        $this->assertFalse($res);
    }
    
    public function testGetPreviousExpressionUntilNull()
    {
        $tokenList = new TokenList();
        
        $token1 = new StringToken('T_STRING', 'myFunc', 1);
        $token2 = new ColonToken('T_COLON', ':', 1);
        $token3 = new StringToken('T_STRING', 'myVar', 1);
        $startToken = new CustomGenericToken('start', 'test', 0);
        
        $tokenList->pushToken($token1);
        $tokenList->pushToken($token2);
        $tokenList->pushToken($token3);
        $tokenList->pushToken($startToken);
        
        $res = $tokenList->getPreviousExpression($startToken);
        $this->assertInternalType('array', $res);
        $this->assertContains($token1, $res);
        $this->assertContains($token2, $res);
        $this->assertContains($token3, $res);
    }
    
    public function testGetPreviousExpressionUntilDelimiter()
    {
        $tokenList = new TokenList();
        
        $token1 = new CustomGenericToken('T_LOGICAL_AND', 'and', 1);
        $token2 = new StringToken('T_STRING', 'myVar', 1);
        $token3 = new ColonToken('T_COLON', ':', 1);
        
        $startToken = new CustomGenericToken('start', 'test', 0);
        
        $tokenList->pushToken($token1);
        $tokenList->pushToken($token2);
        $tokenList->pushToken($token3);
        $tokenList->pushToken($startToken);
        
        $res = $tokenList->getPreviousExpression($startToken);
        $this->assertInternalType('array', $res);
        $this->assertNotContains($token1, $res);
        $this->assertContains($token2, $res);
        $this->assertContains($token3, $res);
    }
    

    
    public function testGetAdjacentTokenWithPositiveOffset()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->assertEquals('myVar2', $tokenList->getAdjacentToken($token, 3, false)->getContent());
    }
    
    public function testGetAdjacentTokenWithNegativeOffset()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->assertEquals('myVar', $tokenList->getAdjacentToken($token, -3, false)->getContent());
    }
    
    public function testGetAdjacentTokenNotExistingReturnsNull()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->assertNull($tokenList->getAdjacentToken($token, 333, false));
    }
    
    public function testGetAdjacentTokenWithZeroThrowsException()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->setExpectedException('InvalidArgumentException');
        $tokenList->getAdjacentToken($token, 0);
    }
    
    public function testGetAdjacentNonWhitespaceTokenWithPositiveOffset()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->assertEquals('myVar', $tokenList->getAdjacentToken($token, -2)->getContent());
    }
    
    public function testGetAdjacentNonWhitespaceTokenWithNegativeOffset()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $this->assertEquals('myVar2', $tokenList->getAdjacentToken($token, 2)->getContent());
    }
    
    public function testMakeLines()
    {
        $token = new NewLineToken('T_NEWLINE', PHP_EOL, 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $res = $tokenList->makeLines();
        $this->assertEquals(3, count($res));
    }
    
    public function testGetLineIndentationToken()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        $expected = new IndentationToken('T_INDENT', '    ', 0);
        $tokenList->injectToken($expected, 1);
        
        $res = $tokenList->getLineIndentationToken($token);
        $this->assertEquals($expected, $res);
    }
    
    public function testGetLineIndentationTokenNotFound()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = $this->getFilledTokenListContaining($token);
        
        $res = $tokenList->getLineIndentationToken($token);
        $this->assertNull($res);
    }
    
    public function testGetLineIndentationTokenReachesStartOfLists()
    {
        $token = new PHPToken('test', 'test', 1);
        $tokenList = new TokenList();
        $tokenList->pushToken($token);
        
        $res = $tokenList->getLineIndentationToken($token);
        $this->assertNull($res);
    }
}

