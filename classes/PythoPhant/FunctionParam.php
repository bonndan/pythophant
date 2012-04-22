<?php

/**
 * PythoPhant_FunctionParam
 * 
 * param of a method
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class PythoPhant_FunctionParam
{

    /**
     * type hint represented as token
     * @var ReturnValueToken 
     */
    private $type;

    /**
     * variable name
     * @var string
     */
    private $name;

    /**
     * deafult value of the param
     * @var string 
     */
    private $default = '';

    /**
     * constructor requires at least type and name
     * 
     * @param string $type
     * @param string $name
     * @param string $default 
     */
    public function __construct($type, $name, $default = null)
    {
        $this->type    = $type;
        $this->name    = $name;
        $this->default = $default;
    }
    
    /**
     * param type
     * @return string|null 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * param variable name
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * return the data as 
     * 
     * @return TokenList
     */
    public function toTokenList()
    {
        $tokenList = new TokenList();
        $tokenList->pushToken(new ReturnValueToken(Token::T_RETURNVALUE, $this->type, 0));
        $tokenList->pushToken(new PHPToken(Token::T_WHITESPACE, ' ', 0));
        $tokenList->pushToken(new StringToken(Token::T_STRING, $this->name, 0));
        if ($this->default !== null) {
            $tokenList->pushToken(new PHPToken(Token::T_WHITESPACE, ' ', 0));
            $tokenList->pushToken(new PHPToken(Token::T_ASSIGN, '=', 0));
            $tokenList->pushToken(new PHPToken(Token::T_WHITESPACE, ' ', 0));
            $tokenList->pushToken(new PHPToken(Token::T_STRING, $this->default, 0));
        }
        return $tokenList;
    }
}