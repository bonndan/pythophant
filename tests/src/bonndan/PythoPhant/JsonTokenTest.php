<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test for the JsonToken
 * 
 *  
 */
class JsonTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return PHPUnit_Framework_MockObject_MockObject 
     */
    private function getTokenListMock()
    {
        return $this->getMockBuilder("PythoPhant\TokenList")->disableOriginalConstructor()
            ->getMock();
    }
    
    /**
     * openJsonIndicatorProvider
     * @return array 
     */
    public function openJsonIndicatorProvider()
    {
        return array(
            array(Token::T_ASSIGN),
            array(Token::T_COMMA),
            array(Token::T_OPEN_BRACE),
            array(Token::T_JSON_ASSIGN),
        );
    }
    
    /**
     * @dataProvider openJsonIndicatorProvider
     */
    public function testOpenArrayWithJsonOpenArrayBeforeIndicator($indicator)
    {
        $token = new JsonToken(Token::T_JSON_OPEN_ARRAY, '[', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue($indicator));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('array(', $token->getContent());
    }
    
    /**
     * 
     */
    public function testOpenArrayWithJsonOpenArrayBeforeOther()
    {
        $token = new JsonToken(Token::T_JSON_OPEN_ARRAY, '[', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue('T_EQUALS'));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('[', $token->getContent());
    }
    
    /**
     * @dataProvider openJsonIndicatorProvider
     */
    public function testOpenObjectWithJsonOpenArrayBeforeIndicator($indicator)
    {
        $token = new JsonToken(Token::T_JSON_OPEN_OBJECT, '{', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue($indicator));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('(object)array(', $token->getContent());
    }
    
    /**
     * 
     */
    public function testOpenObjectWithJsonOpenArrayBeforeOther()
    {
        $token = new JsonToken(Token::T_JSON_OPEN_OBJECT, '{', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue('T_EQUALS'));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('{', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseArrayWithRegularOpenArrayBefore()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue(Token::T_OPEN_ARRAY));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(']', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseArrayWithJsonOpenArrayBefore()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue(Token::T_JSON_OPEN_ARRAY));
            
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(')', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseArrayAsLastElement()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue(null));
        
        $tokenList->expects($this->once())
            ->method('getNextNonWhitespace')
            ->will($this->returnValue(null));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(')', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseArrayBeforeElementWhichIsNotAssign()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue('anythingButNotAssign'));
        
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue(null));
        
        $tokenList->expects($this->once())
            ->method('getNextNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(')', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseArrayBeforeAssign()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_ARRAY, ']', 1);
        
        $tokenMock = $this->getMock("PythoPhant\Token");
        $tokenMock->expects($this->once())
            ->method('getTokenName')
            ->will($this->returnValue(Token::T_ASSIGN));
        
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->once())
            ->method('getPreviousNonWhitespace')
            ->will($this->returnValue(null));
        
        $tokenList->expects($this->once())
            ->method('getNextNonWhitespace')
            ->will($this->returnValue($tokenMock));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(']', $token->getContent());
    }
    
    /**
     * 
     */
    public function testCloseObjectIsAlwaysCurlyBrace()
    {
        $token = new JsonToken(Token::T_JSON_CLOSE_OBJECT, '}', 1);
        
        $tokenList = $this->getTokenListMock();
        $tokenList->expects($this->never())
            ->method('getPreviousNonWhitespace');
        
        $tokenList->expects($this->never())
            ->method('getNextNonWhitespace');
        
        $token->affectTokenList($tokenList);
        $this->assertEquals(')', $token->getContent());
    }
}