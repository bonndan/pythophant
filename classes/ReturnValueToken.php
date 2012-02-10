<?php
/**
 * 
 */
class ReturnValueToken extends CustomGenericToken
{

    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList[$ownIndex + 1];
        if ($next instanceof Token && $next->getTokenName() == 'T_WHITESPACE') {
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
        if ($forceOriginal) {
            return $this->content;
        }
        
        return "";
    }

}
