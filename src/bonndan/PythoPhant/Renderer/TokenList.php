<?php
namespace PythoPhant\Renderer;

use PythoPhant\TokenList as Tokens;

/**
 * TokenList
 * 
 * A renderer for token lists.
 */
class TokenList implements Renderer
{
    /**
     *
     * @var type 
     */
    private $tokenList;
    
    /**
     * constructor requires a token list
     * 
     * @param PythoPhant\TokenList $tokenlist 
     */
    public function __construct(Tokens $tokenlist)
    {
        $this->tokenList = $tokenlist;
    }
    
    /**
     * enable or disable debugging mode
     * 
     * @param bool $debug 
     * 
     * @return Renderer
     */
    public function enableDebugging($debug)
    {
        
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