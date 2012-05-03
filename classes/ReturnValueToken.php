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
        
        $insert = $this->createCheckTokenList($variable);
        
        $i = 1;
        foreach ($insert as $token) {
            $tokenList->injectToken($token, $i++);
        }
    }

    /**
     * creates the list of tokens to be injected
     * 
     * @param string $variable var name
     * 
     * @return TokenList
     */
    private function createCheckTokenList($variable)
    {
        $file = new SplFileObject(
            dirname(__DIR__) . DIRECTORY_SEPARATOR 
            . 'macros' .DIRECTORY_SEPARATOR. 'scalarTypeHintException.pp'
        );
        $macro = new PythoPhant_Macro($file);
        $params = array($this->content, $variable);
        $macro->setParams($params);
        $scanner = PythoPhant_Scanner::create();
        $scanner->scanSource($macro->getSource());
        $macroTokens = $scanner->getTokenList();
        $parser = new PythoPhant_Parser();
        $parser->processTokenList($macroTokens);
        $macro->cleanTokenList($macroTokens, 1);
        
        return $macroTokens;
    }
    
    /**
     * to string conversion uses the getContent method
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->getContent();
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
        
        if (strpos($this->content, '[]')) {
            return "array";
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
