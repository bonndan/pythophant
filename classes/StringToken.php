<?php
/**
 * StringToken can render itself as a variable with leading dollar sign
 * 
 * 
 */
class StringToken extends CustomGenericToken
{
    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    public function affectTokenList(TokenList $tokenList)
    {
        if ($this->getTokenName() == 'T_STRING') {
            $this->checkIfIsVariable($tokenList);
        }
    }

    /**
     * check if it is a string
     * 
     * @param TokenList $tokenList 
     */
    private function checkIfIsVariable(TokenList $tokenList)
    {        
        /**
         * token before 
         */
        $previous = $tokenList->getPreviousNonWhitespace($this);
        $preVariableIndicators = array_merge(
            PythoPhant_Grammar::$preVariableIndicators,
            PythoPhant_Grammar::$controls
        );
        $preCondition = $tokenList->isTokenIncluded(array($previous), $preVariableIndicators);
        
        /**
         * token after 
         */
        $next = $tokenList->getNextNonWhitespace($this);
        
        $postCondition = $tokenList->isTokenIncluded(
            array($next),
            PythoPhant_Grammar::$postVariableIndicators
        );

        if(
            ($preCondition && $postCondition) 
            || (is_null($previous) && $postCondition)
            || ($preCondition && is_null($next))
        ) {
            $this->tokenName = 'T_VARIABLE';
            if (!$previous || $previous->getTokenName() != Token::T_MEMBER) {
                $this->content = '$'.$this->content;
            }
        }
        
        /**
         * class var declaration 
         */
        if (!$previous || $this->tokenName != 'T_VARIABLE') {
            return;
        }
        
        /**
         * return type cant be rendered 
         */
        $preprevious = $tokenList->getPreviousNonWhitespace($previous);
        if ($previous instanceof StringToken 
            && $preprevious instanceof Token
            && in_array($preprevious->getTokenName(), PythoPhant_Grammar::$modifiers)
        ) {
            $preprevious->setContent('');
        }
    }
}
