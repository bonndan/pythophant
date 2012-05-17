<?php
namespace PythoPhant;

/**
 * IsToken
 * 
 * "is" for easy readable code. is not rendered and removed from token list.
 * affects the following token xxx if a natural function "is_xxx" exists.
 * 
 * <code>
 * if someVar is emtpy? ....... empty($someVar)
 * if someVar is not array? ... !is_array($someVar)
 * if someVar is null? ........ is_null($someVar)
 * </code>
 */
class IsToken extends CustomGenericToken implements ParsedEarlyToken
{

    /**
     * checks the tokenlist for previous tokens whether it is a colon or json assignment
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     * @throws Exception
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $next = $tokenList->getNextNonWhitespace($this);

        if (is_null($next)) {
            throw new Exception('T_IS requires a trailing token.');
        } elseif ($next instanceof ExclamationToken) {
            $next = $tokenList->getNextNonWhitespace($next);
        }
        $previous = $tokenList->getPreviousExpression($this);
        if (empty($previous)) {
            throw new Exception(
                'T_IS followed by T_NOT requires a preceding expression on line '
                . $this->getLine()
            );
        }
        $tokenList->moveTokensBefore($previous, $next);

        if ($functionName = $this->isNextNativeFunction($next)) {
            $next->setContent($functionName);
        }

        $this->content = null;
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList->offsetGet($ownIndex + 1);
        if ($next instanceof Token && $next->getTokenName() == Token::T_WHITESPACE) {
            $next->setContent('');
        }
    }

    /**
     * checks if a function exists which is "is_" plus the next token's content
     * 
     * @param Token $next
     * 
     * @return null|string function name
     */
    private function isNextNativeFunction(Token $next)
    {
        if ($next instanceof ReturnValueToken) {
            $content = $next->getContent(true);
        } else {
            $content = $next->getContent();
        }

        $functionName = 'is_' . $content;
        if (function_exists($functionName)) {
            if ($next instanceof ReturnValueToken) {
                $next->setAuxValue(true); //enable returnvalue token rendering
            }
            return $functionName;
        } else {
            return null;
        }
    }

}