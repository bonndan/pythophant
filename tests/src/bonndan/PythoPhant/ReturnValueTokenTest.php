<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test class for ReturnValueToken.
 * 
 */
class ReturnValueTokenTest extends \PHPUnit_Framework_TestCase //implements ParsedEarlyToken
{
    /**
     * @var PHPToken 
     */
    private $token;
    
    public function testContentIsEmptied()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        $whitespace = new PHPToken(Token::T_WHITESPACE, ' ', 1);
        $tokenList->pushToken($whitespace);
        $var = new StringToken(Token::T_STRING, 'myVar', 1);
        $tokenList->pushToken($var);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('', $token->getContent());
    }
    
    public function testContentIsNotEmptied()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'SomeInterface', 1);
        $tokenList->pushToken($token);
        $whitespace = new PHPToken(Token::T_WHITESPACE, ' ', 1);
        $tokenList->pushToken($whitespace);
        $var = new StringToken(Token::T_STRING, 'myVar', 1);
        $tokenList->pushToken($var);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('SomeInterface', $token->getContent());
    }
    
    /**
     * 
     */
    public function testContentIsReplacedWithArrayIfSquareBracesAreUsed()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'SomeInterface[]', 1);
        $tokenList->pushToken($token);
        $whitespace = new PHPToken(Token::T_WHITESPACE, ' ', 1);
        $tokenList->pushToken($whitespace);
        $var = new StringToken(Token::T_STRING, 'myVar', 1);
        $tokenList->pushToken($var);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('array', $token->getContent());
    }
    
    public function testWhitespaceIsEmptied()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        $wsToken = new PHPToken('T_WHITESPACE', ' ', 1);
        $tokenList->pushToken($wsToken);
        $var = new StringToken(Token::T_STRING, 'myVar', 1);
        $tokenList->pushToken($var);
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('', $wsToken->getContent(), serialize($wsToken->getContent()));
    }
    
    public function testForceContentRendering()
    {
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $this->assertEquals('', $token->getContent());
        $token->setAuxValue(true);
        $this->assertEquals('int', $token->getContent());
    }
    
    public function testTypeCheckingIsInserted()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        
        $param = new Reflection\Param('string', 'myVar');
        $token->insertTypecheckinTokenList($tokenList, $param);
        $this->assertEquals(34, $tokenList->count());
    }
    
    public function testTypeCheckingIsInsertedIfParamHasDefault()
    {
        $tokenList = new TokenList();
        $token = new ReturnValueToken(Token::T_RETURNVALUE, 'int', 1);
        $tokenList->pushToken($token);
        
        $param = new Reflection\Param('string', 'myVar', 'null');
        $token->insertTypecheckinTokenList($tokenList, $param);
        $this->assertEquals(1, $tokenList->count());
    }
}