<?php
/**
 * a token which is not a standard token, so it is a custom one, but used generically
 * 
 * 
 *  
 */
class CustomGenericToken extends PHPToken implements CustomToken
{

    /**
     * helper value
     * @var mixed 
     */
    protected $auxValue = NULL;

    /**
     * this generic implementation does not affect the token list
     * 
     * @param TokenList $tokenList
     * 
     * @return void 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        return;
    }

    /**
     *
     * @param mixed $value
     * 
     * @return CustomGenericToken 
     */
    public function setAuxValue($value)
    {
        $this->auxValue = $value;
        return $this;
    }

}