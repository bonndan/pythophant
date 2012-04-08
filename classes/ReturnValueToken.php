<?php
/**
 * ReturnValueToken
 * 
 * A token representing a return value or scalar type hint. It is not rendered.
 * 
 * @see PythoPhant_Grammar::$returnValues
 */
class ReturnValueToken extends CustomGenericToken
{
    /**
     * whether the content shall be returned
     * @var boolean 
     */
    private $returnContent = false;
    
    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     * @throws PythoPhant_Exception
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        try {
            $next = $tokenList->offsetGet($ownIndex + 1);
        } catch (OutOfBoundsException $exc) {
            throw new PythoPhant_Exception(
                'T_RETURNVALUE is not followed by any token', $this->line
            );
        }
        
        if (!$this->returnContent && $next->getTokenName() == Token::T_WHITESPACE
        ) {
            $next->setContent('');
        }
    }

    /**
     * get the content
     * 
     * @param boolean $forceOriginal return original value
     * 
     * @return string 
     */
    public function getContent($forceOriginal = false)
    {
        if ($forceOriginal == true || $this->returnContent == true) {
            return $this->content;
        }
        
        return "";
    }

    /**
     * enable or disable content rendering, required for IsToken
     * 
     * @param boolean
     * 
     * @return void
     */
    public function setAuxValue($value)
    {
        $this->returnContent = (bool)$value;
    }
}
