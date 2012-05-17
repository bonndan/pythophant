<?php
namespace PythoPhant;

/**
 * StringToken 
 * 
 * can render itself as a variable with leading dollar sign
 * 
 */
class StringToken extends CustomGenericToken
{

    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     */
    public function affectTokenList(TokenList $tokenList)
    {
        if ($this->getTokenName() == Token::T_STRING) {
            $this->checkIfIsVariable($tokenList);
        }
    }

    /**
     * check if it is a string
     * 
     * @param TokenList $tokenList 
     */
    private function checkIfIsVariable(TokenList $tokenList)
    {
        /**
         * check for implements, then its just a class name
         * @todo remove special "implements" rule
         */
        $first = $this;
        while ($token = $tokenList->getPreviousNonWhitespace($first)) {
            $first = $token;
        }
        if ($first->getTokenName() == 'T_IMPLEMENTS') {
            return;
        }

        /*
         * class members 
         */
        $preCondition = false;
        $indent = $tokenList->getLineIndentationToken($this);
        if ($indent !== null && $indent->getNestingLevel() == 1) {
            $newline = $tokenList->getPreviousTokenOfType('NewLineToken', $this);
            $docComment = $tokenList->getAdjacentToken($newline, -1);
            if ($docComment instanceof DocCommentToken) {
                if ($docComment->isMethodComment()) {
                    return;
                } else {
                    $preCondition = true;
                }
            }
        }

        /**
         * token before 
         */
        $previous = $tokenList->getPreviousNonWhitespace($this);
        $preVariableIndicators = array_merge(
            Grammar::$preVariableIndicators, Grammar::$controls, Grammar::$casts
        );
        $preCondition = $tokenList->isTokenIncluded(array($previous), $preVariableIndicators);


        /**
         * token after 
         */
        $next = $tokenList->getNextNonWhitespace($this);

        $postCondition = $tokenList->isTokenIncluded(
            array($next), Grammar::$postVariableIndicators
        );

        if (
            ($preCondition && $postCondition)
            || (is_null($previous) && $postCondition)
            || ($preCondition && is_null($next))
        ) {
            $this->tokenName = Token::T_VARIABLE;
            if (!$previous || $previous->getTokenName() != Token::T_MEMBER) {
                $this->content = '$' . $this->content;
            }
        }

        /**
         * class var declaration 
         */
        if (!$previous || $this->tokenName != Token::T_VARIABLE) {
            return;
        }

        /**
         * return type cant be rendered 
         */
        $preprevious = $tokenList->getPreviousNonWhitespace($previous);
        if ($previous instanceof StringToken
            && $preprevious instanceof Token
            && in_array($preprevious->getTokenName(), Grammar::$modifiers)
        ) {
            $preprevious->setContent('');
        }
    }

}
