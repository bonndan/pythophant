<?php

/**
 * ReturnValueToken
 * 
 * A token representing a return value or scalar type hint. It is not rendered.
 * 
 * @see PythoPhant_Grammar::$returnValues
 */
class ReturnValueToken extends CustomGenericToken
{

    /**
     * whether the content shall be returned
     * @var boolean 
     */
    private $returnContent = false;

    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     * 
     * @return void
     * @throws PythoPhant_Exception
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        try {
            $next = $tokenList->offsetGet($ownIndex + 1);
        } catch (OutOfBoundsException $exc) {
            throw new PythoPhant_Exception(
                'T_RETURNVALUE is not followed by any token', $this->line
            );
        }

        if ($this->returnContent == false && $next->getTokenName() == Token::T_WHITESPACE
        ) {
            $next->setContent('');
        }
    }

    /**
     * inserts type checking and a thrown exception
     * 
     * @param TokenList $tokenList
     * @param string    $variable
     * 
     * @return void
     */
    public function insertTypecheckinTokenList(TokenList $tokenList, $variable)
    {
        if ($this->content == 'boolean') {
            $this->content = 'bool';
        }
        $function = 'is_' . $this->content;
        if (!function_exists($function)) {
            return;
        }
        
        $next = $this;
        while ($next = $tokenList->getNextNonWhitespace($next)) {
            $lastInLine = $next;
        }

        $lastIndex = $tokenList->getTokenIndex($lastInLine); #

        $indentationToken = $tokenList->offsetGet($lastIndex + 2);
        /** var $indentationToken IndentationToken */
        
        $insert = $this->createCheckTokens($indentationToken, $variable);
        
        $i = 0;
        foreach ($insert as $token) {
            $tokenList->injectToken($token, $lastIndex + 3 + $i++);
        }
    }

    /**
     * creates the list (array) of tokens to be injected
     * 
     * @param Token $indentationToken
     * @param type $variable
     * 
     * @return array(Token) 
     * @todo use macro
     */
    private function createCheckTokens(Token $indentationToken, $variable)
    {
        $line = $indentationToken->getLine();
        $function = 'is_' . $this->content;
        
        $insert = array();
        $insert[] = new PHPToken('T_IF', 'if', $line);
        $insert[] = new PHPToken('T_WHITESPACE', ' ', $line);
        $insert[] = new ExclamationToken('T_NOT', '!', $line);
        $insert[] = new PHPToken('T_WHITESPACE', ' ', $line);
        $insert[] = new PHPToken('T_STRING', $function, $line);
        $insert[] = new PHPToken('T_OPEN_BRACE', '(', $line);
        $insert[] = new StringToken('T_STRING', (string) $variable, $line);
        $insert[] = new PHPToken('T_CLOSE_BRACE', ')', $line);
        $insert[] = NewLineToken::createEmpty($line);

        $insert[] = IndentationToken::create($indentationToken->getNestingLevel() + 1, $line + 1);
        $insert[] = new PHPToken('T_STRING', 'throw new InvalidArgumentException', $line + 1);
        $insert[] = new PHPToken('T_OPEN_BRACE', '(', $line + 1);
        $insert[] = new ConstToken(
                Token::T_CONSTANT_ENCAPSED_STRING,
                '"' . $variable . ' is not of type ' . $this->content . '"',
                $line + 1
        );
        $insert[] = new PHPToken('T_CLOSE_BRACE', ')', $line + 1);
        $insert[] = NewLineToken::createEmpty($line + 1)->setAuxValue(';');

        $insert[] = IndentationToken::create($indentationToken->getNestingLevel(), $line + 2);
        $insert[] = NewLineToken::createEmpty($line + 2);
        
        $insert[] = clone $indentationToken;
        
        return $insert;
    }
    /**
     * get the content
     * 
     * @param boolean $forceOriginal return original value
     * 
     * @return string 
     */
    public function getContent($forceOriginal = false)
    {
        if ($forceOriginal == true || $this->returnContent == true) {
            return $this->content;
        }

        if (in_array($this->content, PythoPhant_Grammar::$returnValues)) {
            return "";
        }
        
        return $this->content;
    }

    /**
     * enable or disable content rendering, required for IsToken
     * 
     * @param boolean
     * 
     * @return void
     */
    public function setAuxValue($value)
    {
        $this->returnContent = (bool) $value;
    }

}
