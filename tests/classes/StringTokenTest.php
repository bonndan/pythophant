<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class StringTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var StringToken
     */
    private $token;
    
    public function setUp()
    {
        parent::setUp();
        $this->token = new StringToken('T_STRING', 'a', 1);
    }

    /**
     * @return TokenList
     */
    private function getTokenList()
    {
        $list = new TokenList();
        
        return $list;
    }
    
    public function testBetweenReturnValueAndNull()
    {
        $list = $this->getTokenList();
        
        $list->pushToken(new StringToken('T_RETURNVALUE', 'string', 1));
        $list->pushToken($this->token);
        
        $this->token->affectTokenList($list);
        $this->assertEquals('$a', $this->token->getContent());
    }
    
    public function testBetweenNullAndAssign()
    {
        $list = $this->getTokenList();
        $list->pushToken($this->token);
        $list->pushToken(new StringToken('T_ASSIGN', '=', 1));
        
        
        $this->token->affectTokenList($list);
        $this->assertEquals('$a', $this->token->getContent());
    }
}