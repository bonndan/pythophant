<?php

/**
 * factory class to generate Tokens
 *  
 * @category PythoPhant
 */
class PythoPhant_TokenFactory implements TokenFactory
{
    /**
     * class name of regular tokens 
     */
    const DEFAULT_TOKEN_CLASS = 'PHPToken';
    /**
     * class name of custom tokens for generic use 
     */
    const CUSTOM_TOKEN_CLASS = 'CustomGenericToken';
    
    /**
     * tokens which are not detected by the tokenizer but yet have special
     * meaning
     * 
     * @var array
     */
    private static $tokens = array(
        'T_ASSIGN' => PythoPhant_Grammar::T_ASSIGN,
        'T_COMMA' => PythoPhant_Grammar::T_COMMA,
        'T_MEMBER' => PythoPhant_Grammar::T_MEMBER,
        'T_OPEN_BRACE' => PythoPhant_Grammar::T_OPEN_BRACE,
        'T_CLOSE_BRACE' => PythoPhant_Grammar::T_CLOSE_BRACE,
        'T_SELF' => PythoPhant_Grammar::T_SELF,
        'T_THIS' => PythoPhant_Grammar::T_THIS,
        'T_THIS_MEMBER' => PythoPhant_Grammar::T_THIS_MEMBER,
        'T_RETURNVALUE' => PythoPhant_Grammar::T_RETURNVALUE,
        'T_QUESTION' => PythoPhant_Grammar::T_QUESTION,
        'T_COLON' => PythoPhant_Grammar::T_COLON,
        'T_EXCLAMATION' => PythoPhant_Grammar::T_EXCLAMATION,
        'T_NOT' => PythoPhant_Grammar::T_NOT,
        
        'T_OPEN_ARRAY' => PythoPhant_Grammar::T_OPEN_ARRAY,
        'T_CLOSE_ARRAY' => PythoPhant_Grammar::T_CLOSE_ARRAY,
        
        'T_JSON_OPEN_OBJECT' => PythoPhant_Grammar::T_JSON_OPEN_OBJECT,
        'T_JSON_CLOSE_OBJECT' => PythoPhant_Grammar::T_JSON_CLOSE_OBJECT,
        
        'T_ACCESSIBLE' => PythoPhant_Grammar::T_ACCESSIBLE,
        
        'T_BIT_AND' => PythoPhant_Grammar::T_BIT_AND,
        'T_BIT_OR' => PythoPhant_Grammar::T_BIT_OR,
        'T_BIT_XOR' => PythoPhant_Grammar::T_BIT_XOR,
        'T_BIT_NOT' => PythoPhant_Grammar::T_BIT_NOT,
        'T_BIT_SHIFTLEFT' => PythoPhant_Grammar::T_BIT_SHIFTLEFT,
        'T_BIT_SHIFTRIGHT' => PythoPhant_Grammar::T_BIT_SHIFTRIGHT,
    );

    /**
     * classes that implement special token behaviour
     * @var array
     */
    private static $implementations = array(
        'T_MEMBER' => 'MemberToken',
        'T_THIS' => 'ThisToken',
        'T_THIS_MEMBER' => 'ThisMemberToken',
        'T_RETURNVALUE' => 'ReturnValueToken',
        'T_NEWLINE' => 'NewLineToken',
        'T_INDENT' => 'IndentationToken',
        'T_STRING' => 'StringToken',
        'T_IN' => 'InToken',
        'T_OPEN_ARRAY' => 'JsonToken',
        'T_CLOSE_ARRAY' => 'JsonToken',
        'T_COLON' => 'ColonToken',
        'T_JSON_OPEN_OBJECT' => 'JsonToken',
        'T_JSON_CLOSE_OBJECT' => 'JsonToken',
        'T_ACCESSIBLE' => 'AccessibleToken',
        'T_QUESTION' => 'QuestionToken',
        'T_CONST' => 'ConstToken',
        'T_CONSTANT_ENCAPSED_STRING' => 'ConstToken',
        'T_EXCLAMATION' => 'ExclamationToken',
        'T_NOT' => 'ExclamationToken',
    );

    /**
     * array_search did not work properly
     * 
     * @param array|string $tokenized
     *
     * @return string
     */
    public function getTokenName($tokenized)
    {
        /**
         * if strings instead of arrays were tokenized 
         */
        if (is_string($tokenized)) {
            $tokenName = Token::T_STRING;
            if (in_array($tokenized, self::$tokens)) {
                $flip = array_flip(self::$tokens);
                $tokenName = $flip[$tokenized];
            } elseif (in_array($tokenized, PythoPhant_Grammar::$returnValues)) {
                $tokenName = Token::T_RETURNVALUE;
            }
            
            return $tokenName;
        }
        
        $tokenName = token_name($tokenized[0]);
        if ($tokenName == Token::T_STRING) {
            if ($this->isConstant($tokenized)) {
                $tokenName = Token::T_CONST;
            } elseif ($tmp = $this->getTokenName($tokenized[1])) {
                $tokenName = $tmp;
            }
         } elseif ($tokenName == Token::T_WHITESPACE) {
            $tokenName = $this->getWhitespaceToken($tokenized[1]);
            if (is_array($tokenName)) {
                return $tokenName;
            }
        }
        
        return $tokenName;
    }
    
    /**
     * checks if the content might be a constant
     * 
     * @return boolean 
     */
    public function isConstant(array $tokenized)
    {
        $chars = str_replace('_', '', $tokenized[1]);
        return defined($tokenized[1]) || ctype_upper($chars);
    }

    /**
     * get the indentation level of a whitespace string
     * 
     * @param string $string whitespace
     *
     * @return string
     */
    private function getWhitespaceToken($string)
    {
        if (strpos($string, PythoPhant_Grammar::T_NEWLINE) !== FALSE) {
            if ($string[strlen($string)-1] == PHP_EOL) {
                return Token::T_NEWLINE;
            } else {
                return array (Token::T_NEWLINE, Token::T_INDENT);
            }
        }
        
        return Token::T_WHITESPACE;
    }
    
    /**
     * create a token by passing its tokenName. content and line number are optional.
     * 
     * @param string $tokenName
     * @param string $content
     * @param int    $line 
     * 
     * @return Token
     */
    public function createToken($tokenName, $content = NULL, $line = 0)
    {
        $class = self::DEFAULT_TOKEN_CLASS;
        if (array_key_exists($tokenName, self::$implementations)) {
            $class = self::$implementations[$tokenName];
        } elseif ($this->isCustomToken($tokenName)) {
            $content = constant('PythoPhant_Grammar::' . $tokenName);
            $class = self::CUSTOM_TOKEN_CLASS;
        }
 
        if ($content === NULL && defined('PythoPhant_Grammar::' . $tokenName)) {
            $content = constant('PythoPhant_Grammar::' . $tokenName);
        }
        
        return new $class($tokenName, $content, $line);
    }
    
    /**
     * check if a token name is custom
     * 
     * @param string $tokenName
     * 
     * @return boolean 
     */
    private function isCustomToken($tokenName)
    {
        return array_key_exists($tokenName, self::$tokens);
    }

}