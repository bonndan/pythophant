<?php

/**
 * InToken
 * 
 * The token is a nicer replacement for in_array
 * 
 * <code>
 * if myVar is in: ['a', 'b']
 * if myVar in(array('a', 'b'))
 * 
 * ==>
 * 
 * in_array($myVar, array('a', 'b'))
 * </code>
 */
class InToken extends CustomGenericToken implements ParsedEarlyToken
{
    /**
     *
     * @param TokenList $tokenList
     * 
     * @throws PythoPhant_Exception 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $prev = $tokenList->getPreviousNonWhitespace($this);
        if (is_null($prev)) {
            throw new PythoPhant_Exception(
                'T_IN requires to be preceded by an expression in line ', $this->line
            );
        }
        
        $next = $tokenList->getNextNonWhitespace($this);
        if (!$next instanceof Token) {
            throw new PythoPhant_Exception(
                'T_IN requires to be followed by an expression in line ', $this->line
            );
        }
        
        if (!$next instanceof ColonToken 
            && $next->getContent() !== PythoPhant_Grammar::T_OPEN_BRACE
        ) {
            $colon = new ColonToken(Token::T_COLON, ':', $this->line);
            $tokenList->injectToken($colon, $tokenList->getTokenIndex($next));
            $next = $colon;
        }
        
        /**
         * grab the previous expression and move it after this 
         */
        $nextIndex = $tokenList->getTokenIndex($next);
        $prevExpr = $tokenList->getPreviousExpression($this);
        $tokenList->moveTokensBefore($prevExpr, $tokenList->offsetGet($nextIndex + 1));
        
        /**
         * insert comma after last moved token
         */
        $comma = new PHPToken(Token::T_COMMA, ', ', $this->line);
        $lastOfMovedIndex = $tokenList->getTokenIndex($prevExpr[count($prevExpr)-1]);
        $tokenList->injectToken($comma, $lastOfMovedIndex + 1);
        
        $tokenList->offsetUnset($tokenList->getTokenIndex($this)-1);
       
        $this->setContent('in_array');
    }
}
