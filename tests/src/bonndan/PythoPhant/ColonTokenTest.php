<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test for the ExclamationToken
 * 
 *  
 */
class ColonTokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ConstTColonTokenoken 
     */
    private $token;

    public function setup()
    {
        parent::setup();
        $this->token = new ColonToken(Token::T_COLON, ':', 1);
    }

    public function testIsRegularColonWithCase()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new PHPToken('T_CASE', 'case', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals(':', $this->token->getContent());
    }

    public function testIsRegularColonWithShortIf()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new PHPToken(Token::T_SHORT_IF, '?', 1));
        $tokenList->pushToken(new StringToken(Token::T_STRING, 'true', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals(':', $this->token->getContent());
    }

    public function testIsJsonIfConstantStringPreceeds()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new ConstToken(Token::T_CONSTANT_ENCAPSED_STRING, 'myString', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals('=>', $this->token->getContent());
    }

    public function testIsJsonIfArrayIsOpenedBefore()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new PHPToken(Token::T_OPEN_ARRAY, '[', 1));
        $tokenList->pushToken(new PHPToken(Token::T_CLOSE_BRACE, ']', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals('=>', $this->token->getContent());
    }

    public function testFunctionCallBracesAreInsertedIfPreviousIsFunction()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new PHPToken('T_FUNCTION', 'strtolower', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals('(', $this->token->getContent());
        $nextIndex = $tokenList->getTokenIndex($this->token) + 1;
        $closeBrace = $tokenList->offsetGet($nextIndex);
        $this->assertInstanceOf("PythoPhant\PHPToken", $closeBrace);
        $this->assertEquals(')', $closeBrace->getContent());
    }

    public function testFunctionCallBracesAreInsertedIfPreviousIsControl()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new PHPToken('T_IF', 'if', 1));
        $tokenList->pushToken($this->token);

        $this->token->affectTokenList($tokenList);
        $this->assertEquals('(', $this->token->getContent());
        $nextIndex = $tokenList->getTokenIndex($this->token) + 1;
        $closeBrace = $tokenList->offsetGet($nextIndex);
        $this->assertInstanceOf("PythoPhant\PHPToken", $closeBrace);
        $this->assertEquals(')', $closeBrace->getContent());
    }

}