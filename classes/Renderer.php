<?php
/**
 * generic token renderer
 *  
 */
class Renderer
{
    /**
     * inject the lines
     * 
     * @param array $lines 
     */
    public function __construct(TokenList $tokenList)
    {
        foreach ($tokenList as $token) {
            echo $token->getContent();
        }
        
        echo PHP_EOL;
    }
    
    /**
     *
     * @param array $tokens 
     */
    public function renderLine(array $tokens)
    {
        foreach($tokens as $token) {
            $this->renderToken(key($token), current($token));
        }
    }
    
    /**
     *
     * @param string $name
     * @param string $content 
     */
    public function renderToken($name, $content)
    {
        if (CustomToken::isCustomToken($name)) {
            echo CustomToken::render($name);
        } else {
            if ($name == 'T_STRING')
                echo "($name)";
            echo $content;
        }
    }
}