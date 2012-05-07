<?php
/**
 * PythoPhant_Renderer_TokenList
 * 
 */
class PythoPhant_Renderer_TokenList
{
    /**
     *
     * @var type 
     */
    private $tokenList;
    
    /**
     * cosntructor
     * 
     * @param TokenList $tokenlist 
     */
    public function __construct(TokenList $tokenlist)
    {
        $this->tokenList = $tokenlist;
    }
    
    /**
     * to php
     * 
     * @return string 
     */
    public function getPHPSource()
    {
        $buffer = '';
        $body = $this->tokenList;
        if ($body->count() > 0) {
            foreach ($body as $token) {
                $buffer .= $token->getContent();
            }
        }
       
        return $buffer;
    }
}