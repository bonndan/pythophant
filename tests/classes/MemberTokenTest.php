<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * MemberTokenTest
 * 
 *  
 */
class MemberTokenTest extends PHPUnit_Framework_TestCase
{
    public function testStaysDotBetweenConstStrings()
    {
        $token = new MemberToken('T_MEMBER', '.', 1);
        
        $tokenList = new TokenList();
        $tokenList->pushToken(new StringToken('T_CONSTANT_ENCAPSED_STRING', 'abc', 0));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new StringToken('T_CONSTANT_ENCAPSED_STRING', 'abc', 0));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('.', $token->getContent());
    }
    
    public function testStaysDotBetweenStringsAndConst()
    {
        $token = new MemberToken('T_MEMBER', '.', 1);
        
        $tokenList = new TokenList();
        $tokenList->pushToken(new StringToken('T_CONSTANT_ENCAPSED_STRING', 'abc', 0));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new ConstToken(Token::T_CONST, 'MY_CONST', 0));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('.', $token->getContent());
    }
    
    public function testIsMember()
    {
        $token = new MemberToken('T_MEMBER', '.', 1);
        
        $tokenList = new TokenList();
        $tokenList->pushToken(new ThisToken('T_THIS', '$this', 0));
        $tokenList->pushToken($token);
        $tokenList->pushToken(new StringToken(Token::T_STRING, 'someFunc', 0));
        
        $token->affectTokenList($tokenList);
        $this->assertEquals('->', $token->getContent());
    }
}
