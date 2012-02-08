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