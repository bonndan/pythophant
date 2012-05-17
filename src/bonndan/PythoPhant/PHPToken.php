<?php
namespace PythoPhant;

/**
 * PHPToken
 * 
 * a regular php token 
 * 
 * @package PythoPhant
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
     * line number
     * @var int 
     */
    protected $line;

    /**
     * constructor
     * 
     * @param string $tokenName
     * @param string $content
     * @param int    $line 
     */
    public function __construct($tokenName, $content, $line)
    {
        $this->tokenName = (string) $tokenName;
        if (trim($this->tokenName) == '') {
            throw new \InvalidArgumentException('Token name cannot be emtpy.');
        }
        $this->setContent($content);
        $this->setLine((int) $line);
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
            throw new \InvalidArgumentException('setContent expects a string');
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