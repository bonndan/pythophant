<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test for the ControlTokenTest
 * 
 *  
 */
class ControlTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ensures opening and closing brace are inserted 
     */
    public function testAffectTokenListInsertsBraces()
    {
        $tokenList = new TokenList();
        $token = new ControlToken('T_IF', 'if', 0);
        
        $tokenList->pushToken(IndentationToken::create(1));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new PHPToken('T_WHITESPACE', ' ', 1));
        $tokenList->pushToken(new StringToken('T_STRING', 'myVar', 1));
        $tokenList->pushToken(new PHPToken('T_EQUALS', '==', 1));
        $tokenList->pushToken(new PHPToken('T_TRUE', 'true', 1));
        $newLine = new NewLineToken('T_NEWLINE', PHP_EOL, 1);
        $tokenList->pushToken($newLine);
        
        $token->affectTokenList($tokenList);
        
        $index = $tokenList->getTokenIndex($token);
        $openBrace = $tokenList->offsetGet($index + 2);
        $this->assertInstanceOf('PHPToken', $openBrace);
        $this->assertEquals('T_OPEN_BRACE', $openBrace->getTokenName());
        
        $index = $tokenList->getTokenIndex($newLine);
        $closeBrace = $tokenList->offsetGet($index-1);
        $this->assertInstanceOf('PHPToken', $closeBrace);
        $this->assertEquals('T_CLOSE_BRACE', $closeBrace->getTokenName());
    }
    
    public function testAffectTokenListDoesNotInsertBraces()
    {
        $tokenList = new TokenList();
        $token = new ControlToken('T_ELSE', 'else', 0);
        
        $tokenList->pushToken(IndentationToken::create(1));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new StringToken('T_STRING', 'myVar', 1));
        $tokenList->pushToken(new PHPToken('T_EQUALS', '==', 1));
        $tokenList->pushToken(new PHPToken('T_TRUE', 'true', 1));
        $newLine = new NewLineToken('T_NEWLINE', PHP_EOL, 1);
        $tokenList->pushToken($newLine);
        
        $count = $tokenList->count();
        $token->affectTokenList($tokenList);
        $this->assertEquals($count, $tokenList->count());
        
    }
}