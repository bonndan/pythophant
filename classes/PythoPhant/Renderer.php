<?php
/**
 * generic token renderer
 *  
 */
class PythoPhant_Renderer implements Renderer
{
    /**
     * list of tokens of a file
     * @var TokenList 
     */
    private $tokenList;
    
    /**
     * debug mode
     * @var bool 
     */
    private $debug = false;
    
    /**
     * inject the lines
     * 
     * @param array $lines 
     */
    public function setTokenList(TokenList $tokenList)
    {
        $this->tokenList = $tokenList;
    }
    
    /**
     * trigger debug
     * 
     * @param bool $debug
     * 
     * @return PythoPhant_Renderer 
     */
    public function enableDebugging($debug)
    {
        $this->debug = (bool)$debug;
        return $this;
    }
    
    /**
     * add a watermark text
     * 
     * @param string $watermarkText 
     * 
     * @return void
     */
    public function addWaterMark($watermarkText)
    {
        $openTag = $this->tokenList[0];
        $openTag->setContent(trim($openTag->getContent()) . " /** $watermarkText */" . PHP_EOL);
        
        return $this;
    }
    
    /**
     * turns all tokens into their php equivalent
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $buffer = "";
        foreach ($this->tokenList as $token) {
            if ($this->debug) {
                $buffer .= $token->getTokenName();
            }
            $buffer .= $token->getContent();
        }
        
        return $buffer;
    }
}