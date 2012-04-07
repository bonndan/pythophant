<?php
/**
 * ReturnValueToken
 * 
 * A token representing a return value or scalar type hint. It is not rendered.
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
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList[$ownIndex + 1];
        if (!$this->returnContent 
            && $next instanceof Token 
            && $next->getTokenName() == Token::T_WHITESPACE
        ) {
            $next->setContent("");
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
     * enable or disable content rendering
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
