<?php

/**
 * replacement for in_array
 * 
 *  
 */
class InToken extends CustomGenericToken
{
    public function affectTokenList(TokenList $tokenList)
    {
        $prev = $tokenList->getPreviousNonWhitespace($this);
        $next = $tokenList->getNextNonWhitespace($this);
        
        if (is_null($prev) || is_null($next)) {
            throw new LogicException('In requires to be surrounded');
        }
        if ($next instanceof CustomGenericToken) {
            $next->affectTokenList($tokenList);
        }
        $tokenList->offsetUnset($tokenList->getTokenIndex($prev));
        $tokenList->offsetUnset($tokenList->getTokenIndex($this)-1);
        
        $this->content = "in_array(" 
            . $prev->getContent() 
            . "," 
            . $next->getContent().")";
        
    }
}
