<?php

interface Token
{
    /**
     * @param string $tokenName
     * @param string $content
     * @param int    $line
     */
    function __construct($tokenName, $content, $line);

    /**
     * get the ontent
     * 
     * @return string
     */
    function getContent();
    
    /**
     * get the name of the token
     * 
     * @return string 
     */
    function getTokenName();
    
    /**
     * set the content
     * 
     * @param string $content
     * 
     * @return Token 
     */
    function setContent($content);    
    
    /**
     * get the line number
     * @return int 
     */
    function getLine();
    
    /**
     * set the line number
     * 
     * @param int $line 
     */
    function setLine($line);
}

/**
 * a regular php token 
 * 
 * 
 */
class PHPToken implements Token
{
    /**
     * name constant
     * @var string 
     */
    protected $tokenName;
    /**
     * content
     * @var string 
     */
    protected $content;
    /**
     *
     * @var int 
     */
    protected $line;
    
    /**
     * constructor
     * 
     * @param type $tokenName
     * @param type $content
     * @param type $line 
     */
    public function __construct($tokenName, $content, $line)
    {
        $this->tokenName = (string)$tokenName;
        $this->setContent($content);
        $this->setLine((int)$line);
    }
    
    /**
     * get the name of the token
     * 
     * @return string 
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * toString returns the content
     * 
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * set the content
     * 
     * @param string $content
     * 
     * @return PHPToken 
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new InvalidArgumentException('setContent expects a string');
        }
        
        $this->content = $content;
        return $this;
    }
    
    /**
     * get the line number
     * 
     * @return int 
     */
    public function getLine()
    {
        return $this->line;
    }
    
    /**
     * get the line number
     * 
     * @return Token 
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }
}

interface CustomToken extends Token
{
    public function affectTokenList(TokenList $tokenList);
    
    public function setAuxValue($value);
}

class CustomGenericToken extends PHPToken implements CustomToken
{
    /**
     * helper value
     * @var mixed 
     */
    protected $auxValue = NULL;

    /**
     * affect the following token
     * 
     * @param Token $token
     * 
     * @return boolean 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        return false;
    }
    
    /**
     *
     * @param mixed $value
     * 
     * @return CustomGenericToken 
     */
    public function setAuxValue($value)
    {
        $this->auxValue = $value;
        return $this;
    }
}

class MemberToken extends CustomGenericToken
{
    public function getContent()
    {
        return "->";
    }
}

class ThisToken extends CustomGenericToken
{
    public function getContent()
    {
        return "$".$this->content;
    }
}

class ReturnValueToken extends CustomGenericToken
{
    /**
     * if the next token in list is a whitespace, it will be nulled
     * 
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList[$ownIndex+1];
        if ($next instanceof Token && $next->getTokenName() == 'T_WHITESPACE') {
            $next->setContent("");
        }
    }
    
    public function getContent()
    {
        return "";
    }
    
}

class NewLineToken extends CustomGenericToken
{
    protected $auxValue = ';';
    
    /**
     * returns PHP_EOL
     * 
     * @return string 
     */
    public function getContent()
    {
        $firstNL = strpos($this->content, PHP_EOL);
        $content = substr($this->content, 0, $firstNL);
        $content .= $this->auxValue;
        
        return $content
            . str_repeat(PHP_EOL, substr_count($this->content, PHP_EOL));
    }
}

class IndentationToken extends CustomGenericToken
{
    const INDENTATION_SPACES = 4;
    
    public function setContent($content)
    {
        parent::setContent($content);
        $level = $this->getNestingLevel();
        if (($level - (int)$level) != 0) {
            throw new InvalidArgumentException('Malformed indentation of ' . $level);
        }
    }
    
    /**
     * return the whitespaces after the last NL
     * 
     * @return string 
     */
    public function getContent()
    {
        if (strpos($this->content, PHP_EOL) === FALSE) {
            return $this->content;
        }
        
        $rev = strrev($this->content);
        $lastNL = strpos($rev, PHP_EOL);
        return substr($rev, 0, $lastNL);
    }
    
    /**
     * get the indentation level
     * @return int 
     */
    public function getNestingLevel()
    {
        return strlen($this->getContent()) / self::INDENTATION_SPACES;
    }
}