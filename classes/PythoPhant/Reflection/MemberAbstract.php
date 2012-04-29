<?php
/**
 * PythoPhant_Reflection_ElementAbstract
 * 
 * abstract class implementing reflection element methods
 * 
 */
abstract class PythoPhant_Reflection_MemberAbstract
extends PythoPhant_Reflection_ElementAbstract
implements PythoPhant_Reflection_Member
{   
     /**
     * modifiers, visibility
     * @var string
     */
    protected $modifiers = 'public';
    
    /**
     * return type
     * @var string 
     */
    protected $type = null;
    
    /**
     * array of body tokens
     * @var array 
     */
    protected $bodyTokens = array();
    
    /**
     * the own token list
     * @var type 
     */
    protected $tokenList = null;
    
    /**
     * set modifiers like visibility, static, final
     * 
     * @param array $modifiers 
     */
    public function setModifiers(array $modifiers)
    {
        foreach ($modifiers as $key => $mod) {
            if ($mod instanceof Token) {
                $modifiers[$key] = $mod->getContent();
            }
        }
        
        $this->modifiers = implode(' ', $modifiers);
    }
    
    /**
     * returns the modifiers (visibility, abstract etc)
     * 
     * @return string
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }
    
    /**
     * set the return true, force original content form ReturnValueToken
     * 
     * @param string|Tkoen $type 
     */
    public function setType($type)
    {
        if ($type instanceof Token) {
            $type = $type->getContent(true);
        }
        $this->type = $type;
    }
    
        /**
     * add a collection of tokens to the body
     * 
     * @param array $tokens 
     * 
     * @return void
     */
    public function addBodyTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            if ($token instanceof Token) {
                array_push($this->bodyTokens, $token);
            }
        }
    }
    
    /**
     * returns all body tokens in a tokenlist
     * 
     * @return TokenList 
     */
    public function getBodyTokenList()
    {
        if ($this->tokenList === null) {
            $this->tokenList = new TokenList();
            foreach ($this->bodyTokens as $token) {
                $this->tokenList->pushToken($token);
            }
        }
        
        return $this->tokenList;
    }
    
    /**
     * returns the variable type
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
}