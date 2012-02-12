<?php
/**
 * token that triggers getter-setter generation on class members
 * 
 * @package PythoPhant 
 */
class AccessibleToken extends CustomGenericToken
{
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
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $typeToken = $tokenList->getNextNonWhitespace($this);
        
        $nextToken = $tokenList->getNextNonWhitespace($typeToken);
        if (!$nextToken instanceof StringToken) {
            throw new LogicException (
                'accessible must be followed by  return type and then variable name, not ' . $nextToken->getContent()
            );
        }
        
        $varName = $nextToken->getContent();
        $typeToken->setContent('');
        $type = $typeToken->getContent(true);
        
        $this->generateGetterAndSetter($tokenList, $type, $varName);
    }
    
    /**
     * pushes a complete getter function with type casting to end of the token list
     * 
     * @param TokenList $tokenList
     * @param string    $type
     * @param string    $varName 
     */
    private function generateGetterAndSetter(TokenList $tokenList, $type, $varName)
    {
        $tokenFactory = new PythoPhant_TokenFactory();
        
        $getter = array(
            IndentationToken::create(1),
            $tokenFactory->createToken('T_DOC_COMMENT', "/** autogenerated getter @return $type*/". PHP_EOL),
            IndentationToken::create(1),
            $tokenFactory->createToken('T_PUBLIC', 'public '),
            $tokenFactory->createToken('T_FUNCTION', 'function '),
            $tokenFactory->createToken('T_FUNCTION', 'get'.ucfirst($varName)),
            $tokenFactory->createToken(Token::T_OPEN_BRACE),
            $tokenFactory->createToken(Token::T_CLOSE_BRACE),
            $tokenFactory->createToken('T_NEWLINE'),
            IndentationToken::create(2),
            $tokenFactory->createToken('T_RETURN', 'return'),
            $tmp = in_array($type, PythoPhant_Grammar::$returnValues)?
                $tokenFactory->createToken('T_STRING', ' (' . $type.')'):
                $tokenFactory->createToken('T_WHITESPACE', ' ')
                ,
            $tokenFactory->createToken('T_STRING', 'this->'.$varName),
            $tokenFactory->createToken('T_NEWLINE', PHP_EOL),
        );
        foreach ($getter as $token) {
            $tokenList->pushToken($token);
        }
        
        
        $setter = array(
            IndentationToken::create(1),
            $tokenFactory->createToken('T_DOC_COMMENT', "/** autogenerated setter @return self*/". PHP_EOL),
            IndentationToken::create(1),
            $tokenFactory->createToken('T_PUBLIC', 'public '),
            $tokenFactory->createToken('T_FUNCTION', 'function '),
            $tokenFactory->createToken('T_FUNCTION', 'set'.ucfirst($varName)),
            $tokenFactory->createToken(Token::T_OPEN_BRACE),
            $tmp = !in_array($type, PythoPhant_Grammar::$returnValues)?
                $tokenFactory->createToken('T_STRING', $type.' '):
                $tokenFactory->createToken('T_WHITESPACE', '')
                ,
            $tokenFactory->createToken('T_STRING', $varName),
            $tokenFactory->createToken(Token::T_CLOSE_BRACE),
            $tokenFactory->createToken('T_NEWLINE'),
            IndentationToken::create(2),
            $tokenFactory->createToken('T_THIS', 'this->'),
            $tokenFactory->createToken('T_VARIABLE', $varName),
            $tokenFactory->createToken('T_WHITESPACE', ' '),
            $tokenFactory->createToken('T_ASSIGN', '='),
            $tokenFactory->createToken('T_WHITESPACE', ' '),
            $tmp = in_array($type, PythoPhant_Grammar::$returnValues)?
                $tokenFactory->createToken('T_STRING',  '(' . $type . ')'):
                $tokenFactory->createToken('T_WHITESPACE', '')
                ,
            $tokenFactory->createToken('T_STRING', $varName),
            $tokenFactory->createToken('T_NEWLINE', PHP_EOL),
            IndentationToken::create(2),
            $tokenFactory->createToken('T_RETURN', 'return '),
            $tokenFactory->createToken('T_STRING', 'this'),
            $tokenFactory->createToken('T_NEWLINE', PHP_EOL),
        );
        foreach ($setter as $token) {
            $tokenList->pushToken($token);
        }
    }
}
