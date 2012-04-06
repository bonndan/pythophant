<?php

/**
 * replacement for in_array
 * 
 * <code>
 * if myVar is in ['a', 'b']
 * if ['a', 'b'] contains myVar
 * 
 * if someVar is emtpy?
 * if someVar is null?
 * =>
 * 
 * in_array($myVar, array('a', 'b'))
 * </code>
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
