<?php
namespace PythoPhant;

use PythoPhant\Core\Scanner as Scanner;

/**
 * PropertyToken
 * 
 * token that triggers getter-setter generation on class members
 * 
 * <code>
 * class TestClass
 * 
 *     private property int myProperty
 * </code>
 * 
 * @package PythoPhant 
 */
class PropertyToken extends CustomGenericToken implements MacroConsumer
{
    /**
     * @var string 
     */
    const TOKEN_NAME  = 'T_PROPERTY';
    const TOKEN_VALUE = 'property';

    /**
     * scanner instance
     * @var Scanner 
     */
    private $getterScanner;
    /**
     * scanner instance
     * @var Scanner 
     */
    private $setterScanner;
    
    /**
     * is not rendered
     * 
     * @return null 
     */
    public function getContent()
    {
        return NULL;
    }
    
    /**
     * this generic implementation does not affect the token list
     * 
     * @param TokenList $tokenList
     * 
     * @return void
     * @throws Exception
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $typeToken = $tokenList->getNextNonWhitespace($this);
        
        $nextToken = $tokenList->getNextNonWhitespace($typeToken);
        if (!$nextToken instanceof StringToken) {
            throw new Exception (
                self::TOKEN_NAME . ' must be followed by return type and then variable name, not ' 
                . serialize($nextToken),
                $this->line
            );
        }
        
        $varName = $nextToken->getContent();
        $typeToken->setContent('');
        $type = $typeToken->getContent(true);
        
        $this->generateGetter($tokenList, $type, $varName);
        $this->generateSetter($tokenList, $type, $varName);
        
    }
    
    /**
     * pushes a complete getter function with type casting to end of the token list
     * 
     * @param TokenList $tokenList
     * @param string    $type
     * @param string    $varName 
     */
    private function generateGetter(TokenList $tokenList, $type, $varName)
    {
        $file = new \SplFileObject(
            PATH_PYTHOPHANT_MACROS . DIRECTORY_SEPARATOR . 'propertyGetter.pp'
        );
        $this->addMacro($file, array($type, $varName), $tokenList, $this->getterScanner);
    }
    
    /**
     * pushes a complete getter function with type casting to end of the token list
     * 
     * @param TokenList $tokenList
     * @param string    $type
     * @param string    $varName 
     */
    private function generateSetter(TokenList $tokenList, $type, $varName)
    {
        $file = new \SplFileObject(
            PATH_PYTHOPHANT_MACROS . DIRECTORY_SEPARATOR . 'propertySetter.pp'
        );
        $this->addMacro($file, array($type, $varName), $tokenList, $this->setterScanner);
        
    }
    
    /**
     * read the macro, scan it, add the tokens
     * 
     * @param SplFileObject $file
     * @param array         $params
     * @param TokenList     $tokenList
     * @param Scanner       $scanner 
     */
    private function addMacro(
        \SplFileObject $file,
        array $params,
        TokenList $tokenList,
        Scanner $scanner
    ) {
        $macro = new TemplateMacro($file);
        $macro->setParams($params);
        $scanner->scanSource($macro->getSource());
        $macroTokens = $scanner->getTokenList();
        $macro->cleanTokenList($macroTokens, 1);
        foreach ($macroTokens as $token) {
            $tokenList->pushToken($token);
        }
    }
    
    /**
     * scanner injection
     * 
     * @param Scanner $scanner 
     * 
     * @return void
     */
    public function setScanner(Scanner $scanner)
    {
        $this->getterScanner = $scanner;
        $this->setterScanner = clone $scanner;
    }
}
