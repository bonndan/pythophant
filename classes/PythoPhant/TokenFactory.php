<?php

/**
 * factory class to generate Tokens
 *  
 */
class PythoPhant_TokenFactory implements TokenFactory
{
    const T_ASSIGN = '=';
    const T_OPEN_BRACE = '(';
    const T_CLOSE_BRACE = ')';
    const T_OPEN_ARRAY = '[';
    const T_CLOSE_ARRAY = ']';
    const T_THIS = 'this';
    const T_THIS_MEMBER = '@';
    const T_SELF = 'self';
    const T_MEMBER = '.';
    const T_COMMA = ',';
    const T_NEWLINE = "\n";
    const T_RETURNVALUE = "";
    const T_QUESTION = "?";
    const T_COLON = ":";
    const T_NOT = "not";
    const T_EXCLAMATION = "!";
    
    const T_JSON_OPEN_OBJECT = "{";
    const T_JSON_CLOSE_OBJECT = "}";
    
    const T_ACCESSIBLE = 'accessible';
    
    /**
     * @var array
     */
    private static $tokens = array(
        'T_ASSIGN' => self::T_ASSIGN,
        'T_COMMA' => self::T_COMMA,
        'T_MEMBER' => self::T_MEMBER,
        'T_OPEN_BRACE' => self::T_OPEN_BRACE,
        'T_CLOSE_BRACE' => self::T_CLOSE_BRACE,
        'T_SELF' => self::T_SELF,
        'T_THIS' => self::T_THIS,
        'T_THIS_MEMBER' => self::T_THIS_MEMBER,
        'T_RETURNVALUE' => self::T_RETURNVALUE,
        'T_QUESTION' => self::T_QUESTION,
        'T_COLON' => self::T_COLON,
        'T_EXCLAMATION' => self::T_EXCLAMATION,
        'T_NOT' => self::T_NOT,
        
        'T_OPEN_ARRAY' => self::T_OPEN_ARRAY,
        'T_CLOSE_ARRAY' => self::T_CLOSE_ARRAY,
        'T_CLOSE_ARRAY' => self::T_CLOSE_ARRAY,
        
        'T_JSON_OPEN_OBJECT' => self::T_JSON_OPEN_OBJECT,
        'T_JSON_CLOSE_OBJECT' => self::T_JSON_CLOSE_OBJECT,
        
        'T_ACCESSIBLE' => self::T_ACCESSIBLE,
    );

    /**
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
        'T_OPEN_ARRAY' => 'OpenArrayToken',
        'T_CLOSE_ARRAY' => 'CloseArrayToken',
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
         } elseif ($tokenName == 'T_WHITESPACE') {
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
        if (strpos($string, self::T_NEWLINE) !== FALSE) {
            if ($string[strlen($string)-1] == PHP_EOL) {
                return Token::T_NEWLINE;
            } else {
                return array (Token::T_NEWLINE, Token::T_INDENT);
            }
        }
        
        return 'T_WHITESPACE';
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
        $class = 'PHPToken';
        if (array_key_exists($tokenName, self::$implementations)) {
            $class = self::$implementations[$tokenName];
        } elseif ($this->isCustomToken($tokenName)) {
            $content = constant('self::' . $tokenName);
            $class = 'CustomGenericToken';
        }
        
        if ($content === NULL && defined('self::' . $tokenName)) {
            $content = constant('self::' . $tokenName);
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
    public function isCustomToken($tokenName)
    {
        return array_key_exists($tokenName, self::$tokens);
    }

}