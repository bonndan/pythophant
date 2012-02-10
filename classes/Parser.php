<?php
/**
 * 
 */
interface Parser
{
    /**
     * process a token list
     * 
     * @param TokenList $tokenList
     */
    public function processTokenList(TokenList $tokenList);
}
