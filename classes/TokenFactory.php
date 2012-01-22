<?php
require_once dirname(__FILE__) . '/Token.php';

/**
 * factory class to generate Tokens
 *  
 */
class TokenFactory
{

    const INDENTATION_LEVEL = 4;
    const T_ASSIGN = '=';
    const T_OPEN_BRACE = '(';
    const T_CLOSE_BRACE = ')';
    const T_THIS = 'this';
    const T_SELF = 'self';
    const T_MEMBER = '.';
    const T_NEWLINE = "\n";
    const T_RETURNVALUE = "";

    /**
     * @var array
     */
    private static $tokens = array(
        'T_ASSIGN' => self::T_ASSIGN,
        'T_MEMBER' => self::T_MEMBER,
        'T_OPEN_BRACE' => self::T_OPEN_BRACE,
        'T_CLOSE_BRACE' => self::T_CLOSE_BRACE,
        'T_SELF' => self::T_SELF,
        'T_THIS' => self::T_THIS,
        'T_RETURNVALUE' => self::T_RETURNVALUE,
    );
    
    /**
     * return values
     * 
     * @var array 
     */
    public static $returnValues = array(
        'string', 'int', 'bool', 'boolean', 'void'
    );

    /**
     * @var array
     */
    private static $implementations = array(
        'T_MEMBER' => 'MemberToken',
        'T_THIS' => 'ThisToken',
        'T_RETURNVALUE' => 'ReturnValueToken',
        'T_VARIABLE' => 'VariableToken',
        'T_NEWLINE' => 'NewLineToken',
        'T_INDENTATION' => 'IndentationToken',
    );

    /**
     * array_search did not work properly
     * 
     * @param string $string
     *
     * @return string
     */
    public function getTokenName($tokenized)
    {
        /**
         * if strings instead of arrays were tokenized 
         */
        if (is_string($tokenized)) {
            $tokenName = 'T_STRING';
            if (in_array($tokenized, self::$tokens)) {
                $flip = array_flip(self::$tokens);
                $tokenName = $flip[$tokenized];
            } elseif (in_array($tokenized, self::$returnValues)) {
                $tokenName = 'T_RETURNVALUE';
            }
            
            return $tokenName;
        }
        
        $tokenName = token_name($tokenized[0]);
        if ($tokenName == 'T_STRING') {
            if ($tmp = $this->getTokenName($tokenized[1])) {
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
                return 'T_NEWLINE';
            } else {
                return array ('T_NEWLINE', 'T_INDENTATION');
            }
        }
        
        return 'T_WHITESPACE';
    }
    
    /**
     *
     * @param type $tokenName
     * @param type $content
     * @param type $line 
     * 
     * @return Token
     */
    public function createToken($tokenName, $content, $line)
    {
        $class = 'PHPToken';
        if (array_key_exists($tokenName, self::$implementations)) {
            $class = self::$implementations[$tokenName];
        } elseif ($this->isCustomToken($tokenName)) {
            $content = constant('self::' . $tokenName);
            $class = 'CustomGenericToken';
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