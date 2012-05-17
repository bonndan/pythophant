<?php
namespace PythoPhant;

/**
 * ColonToken
 * 
 * colon is assignment in json context OR replacement for a pair of braces
 * 
 * <code>
 * myVar = strlen: "aString"
 * </code>
 */
class ColonToken extends CustomGenericToken implements ParsedEarlyToken
{
    /**
     * checks the tokenlist for previous tokens whether it is a colon or json assignment
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $token = $this;
        while ($token = $tokenList->getPreviousNonWhitespace($token)) {
            if (in_array($token->getTokenName(), array('T_CASE', Token::T_SHORT_IF))) {
                return;
            }
        }
        
        /**
         * clearly json if previous is string 
         */
        $prev = $tokenList->getPreviousNonWhitespace($this);
        if ($prev->getTokenName() == Token::T_CONSTANT_ENCAPSED_STRING) {
            return $this->makeJsonAssign();
        }
        
        $firstPrev = $prev;
        $jsonIndicators = array(Token::T_JSON_OPEN_ARRAY, Token::T_OPEN_ARRAY);
        while($prev = $tokenList->getPreviousNonWhitespace($prev)) {
            if (in_array($prev->getTokenName(), $jsonIndicators)) {
                return $this->makeJsonAssign();
            }
        }
        
        if ($this->isPreviousFunctionOrControl($firstPrev, $tokenList)) {
            return $this->makeFunctionCallBraces($tokenList);
        }
        
        /**
         * fallback to json 
         */
        $this->makeJsonAssign();
    }
    
    /**
     * turns into a json assignment (array notation) 
     */
    private function makeJsonAssign()
    {
        $this->tokenName = Token::T_JSON_ASSIGN;
        $this->content   = '=>';
    }
    
    /**
     * check if a token is a function or a control symbol
     * 
     * @param Token     $previous
     * @param TokenList $tokenList
     * 
     * @return boolean
     */
    private function isPreviousFunctionOrControl(Token $previous, TokenList $tokenList)
    {
        $indicators = array(Token::T_STRING);
        $indicators = array_merge(
            $indicators,
            Grammar::$controls,
            Grammar::$constructsWithBraces
        );
        return $tokenList->isTokenIncluded(array($previous), $indicators)
            ||
            function_exists($previous->getContent());
    }
    
    /**
     * replaces the colon with open brace and inserts closing brace at eol
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    private function makeFunctionCallBraces(TokenList $tokenList)
    {
        $this->setContent(Grammar::T_OPEN_BRACE);
        $this->tokenName = Token::T_OPEN_BRACE;
        
        $token = $this;
        while ($token = $tokenList->getNextNonWhitespace($token)) {
            $lastToken = $token;
        }
        
        $closeBrace = new PHPToken(
            Token::T_CLOSE_BRACE,
            Grammar::T_CLOSE_BRACE,
            $this->getLine()
        );
        
        if (!isset($lastToken)) {
            $lastToken = $this;
        } else {
            $whiteSpace = $tokenList->offsetGet($tokenList->getTokenIndex($this)+1);
            $whiteSpace->setContent('');
        }
        
        $tokenList->injectToken($closeBrace, $tokenList->getTokenIndex($lastToken) +1);
    }
}