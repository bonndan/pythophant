<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for QuestionToken
 * 
 * 
 */
class QuestionTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var QuestionToken
     */
    private $token;
    
    public function setUp()
    {
        parent::setUp();
        $this->token = new QuestionToken('T_STRING', '?', 1);
    }
    
    public function testIsShortIf()
    {
        $factory = new PythoPhant_TokenFactory();
        $tokenList = new TokenList();
        $tokenList->pushToken($this->token);
        $tokenList->pushToken($factory->createToken('T_COLON', ':'));
        
        $this->token->affectTokenList($tokenList);
        $this->assertEquals('T_SHORT_IF', $this->token->getTokenName());
    }
    
    public function testAffectedTokenListIfControlTokenOnSameLine()
    {
        $factory = new PythoPhant_TokenFactory();
        $tokenList = new TokenList();
        $tokenList->pushToken($factory->createToken('T_IF', 'if'));
        $tokenList->pushToken($factory->createToken('T_STRING', 'someVar'));
        $tokenList->pushToken($factory->createToken('T_STRING', 'is_null'));
        $tokenList->pushToken($this->token);
        
        
        $this->token->affectTokenList($tokenList);
        $this->assertEquals('', $this->token->getContent());
        $this->assertEquals('(', $tokenList->offsetGet(1)->getContent());
        $this->assertEquals('someVar', $tokenList->offsetGet(2)->getContent());
        $this->assertEquals(')', $tokenList->offsetGet(3)->getContent());
    }
}