<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * PropertyTokenTest
 */
class PropertyTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyToken 
     */
    private $token;
    
    /**
     * 
     */
    public function setup()
    {
        parent::setUp();
        $this->token = new PropertyToken(PropertyToken::TOKEN_NAME, PropertyToken::TOKEN_VALUE, 1);
    }
    
    public function testGetContentIsNull()
    {
        $this->assertNull($this->token->getContent());
    }
    
    private function getTokenList()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken($this->token);
        $tokenList->pushToken(new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1));
        $tokenList->pushToken(new StringToken(Token::T_STRING, 'myVar', 1));
        
        return $tokenList;
    }
    
    /**
     * ensures that invalid use throws an exception 
     */
    public function testAffectTokenListThrowsException()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken($this->token);
        $tokenList->pushToken(new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1));
        
        $this->setExpectedException("PythoPhant\Exception");
        $this->token->affectTokenList($tokenList);
    }
    
    public function testAffectTokenList()
    {
        $tokenList = $this->getTokenList();
        $count = $tokenList->count();
        $scanner = $this->getMockBuilder("PythoPhant\Core\Scanner")
            ->disableOriginalConstructor()
            ->getMock();
        $scanner->expects($this->exactly(2))
            ->method('scanSource');
        $macroTokens = new TokenList();
        $macroTokens->pushToken(new StringToken('T_STRING', 'test', 1));
        $scanner->expects($this->exactly(2))
            ->method('getTokenList')
            ->will($this->returnValue($macroTokens));
        
        $this->token->setScanner($scanner);
        $this->token->affectTokenList($tokenList);
        
        //2 test + 3 indent
        $extra = 2 * 1 + 3;
        $this->assertEquals($count + $extra, $tokenList->count());
    }
}
