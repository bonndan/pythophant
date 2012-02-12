<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for CustomToken.
 * Generated by PHPUnit on 2012-01-20 at 21:38:12.
 */
class TokenFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TokenFactory
     */
    protected $factory;

    public function setup()
    {
        parent::setup();
        $this->factory = new PythoPhant_TokenFactory();
    }
    
    /**
     * @dataProvider customTokenProvider
     */
    public function testGetTokenName($string, $expected)
    {
        $token = $this->factory->getTokenName($string);
        $this->assertEquals($expected, $token);
    }
    
    public function customTokenProvider()
    {
        return array(
            array(',', 'T_COMMA'),
            array('.', 'T_MEMBER'),
            array('"."', 'T_STRING'),
            array('(', 'T_OPEN_BRACE'),
            array(')', 'T_CLOSE_BRACE'),
            array('=', 'T_ASSIGN'),
            array('this', 'T_THIS'),
            array('self', 'T_SELF'),
            array('string', 'T_RETURNVALUE'),
            array('bool', 'T_RETURNVALUE'),
            array('{', 'T_JSON_OPEN_OBJECT'),
            array('}', 'T_JSON_CLOSE_OBJECT'),
            array(':', 'T_COLON'),
            array('boolean', 'T_RETURNVALUE'),
            
            array(array(T_STRING, "this", 3), 'T_THIS'),
            array(array(T_WHITESPACE, " ", 3), 'T_WHITESPACE'),
        );
    }
    
    /**
     *
     * @param type $tokenName
     * @param type $content
     * @param type $line
     * @param type $expected 
     * 
     * @dataProvider createTokenProvider
     */
    public function testCreateToken($tokenName, $expected)
    {
        $res = $this->factory->createToken($tokenName, "content", 1);
        $this->assertInstanceOf($expected, $res);
    }
    
    public function createTokenProvider()
    {
        return array(
            array('T_STRING', 'PHPToken'),
            array('T_ASSIGN', 'CustomGenericToken'),
            array('T_MEMBER', 'MemberToken'),
            array('T_THIS', 'ThisToken'),
            array('T_RETURNVALUE', 'ReturnValueToken'),
        );
    }
    
    public function testDetectConstant()
    {
        $string = '<?php 
            echo MY_CONST
            ';
        $tokens = token_get_all($string);
        $last = $tokens[count($tokens)-2];
        $tokenName = $this->factory->getTokenName($last);
        $this->assertEquals('T_CONST', $tokenName);
    }
}
