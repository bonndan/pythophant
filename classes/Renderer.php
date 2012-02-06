<?php
/**
 * generic token renderer
 *  
 */
class Renderer
{
    /**
     * list of tokens of a file
     * @var TokenList 
     */
    private $tokenList;
    
    /**
     * inject the lines
     * 
     * @param array $lines 
     */
    public function __construct(TokenList $tokenList)
    {
        $this->tokenList = $tokenList;
    }
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource($debugNames = false)
    {
        $buffer = "";
        foreach ($this->tokenList as $token) {
            if ($debugNames) {
                $buffer .= $token->getTokenName();
            }
            $buffer .= $token->getContent();
        }
        
        return $buffer;
    }
}