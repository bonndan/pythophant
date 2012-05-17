<?php
namespace PythoPhant;

/**
 * ControlToken
 * 
 * a token for if, elseif, switch etc. which injects braces
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class ControlToken extends CustomGenericToken
{

    /**
     * injects braces. open brace after whitespace, close before newline
     * 
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $skipBraces = $tokenList->isTokenIncluded(
            array($this), Grammar::$controlsWithoutBraces
        );
        if ($skipBraces) {
            return;
        }

        $openBrace = new PHPToken(Token::T_OPEN_BRACE, '(', $this->line);
        $tokenList->injectToken($openBrace, $tokenList->getTokenIndex($this) + 2);
        
        $newLine = $tokenList->getNextTokenOfType('NewLineToken', $this);
        $closeBrace = new PHPToken(Token::T_CLOSE_BRACE, ')', $this->line);
        $tokenList->injectToken($closeBrace, $tokenList->getTokenIndex($newLine));
    }

}