<?php
namespace PythoPhant;

use PythoPhant\Reflection\Param;

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
     * @param Param     $variable
     * 
     * @return void
     */
    public function insertTypecheckinTokenList(
        TokenList $tokenList,
        Param $param
    ){
        if ($param->getDefault() != null) {
            return;
        }
        
        $variable = $param->getName();
        if ($this->content == 'boolean') {
            $this->content = 'bool';
        }
        $function = 'is_' . $this->content;
        if (!function_exists($function)) {
            return;
        }
        
        $insert = $this->createCheckTokenList($variable);
        
        $i = 0;
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
        $file = new \SplFileObject(
            dirname(__DIR__) . DIRECTORY_SEPARATOR 
            . 'macros' .DIRECTORY_SEPARATOR. 'scalarTypeHintException.pp'
        );
        $macro = new Macro($file);
        $params = array($this->content, $variable);
        $macro->setParams($params);
        $scanner = PythoPhant\Core\TokenFactoryScanner::create();
        $scanner->scanSource($macro->getSource());
        $macroTokens = $scanner->getTokenList();
        $parser = new \PythoPhant\Core\ReflectionParser();
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

        if (in_array($this->content, Grammar::$returnValues)) {
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
