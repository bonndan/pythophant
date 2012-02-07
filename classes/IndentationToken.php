<?php
/**
 * token representing indentation
 *  
 */
class IndentationToken extends CustomGenericToken
{
    /**
     * @var int 
     */
    const INDENTATION_SPACES = 4;

    /**
     * create a new indentation token
     * 
     * @param int $nestingLevel
     * @param int $line
     * 
     * @return IndentationToken 
     */
    public static function create($nestingLevel, $line = 0)
    {
        $content = str_repeat(' ', self::INDENTATION_SPACES * $nestingLevel);
        return new IndentationToken(Token::T_INDENT, $content, $line);
    }

    /**
     * set the content. indentation is computed by strlen
     * 
     * @param type $content
     * @throws InvalidArgumentException 
     */
    public function setContent($content)
    {
        parent::setContent($content);
        $level = $this->getNestingLevel();
        if (($level - (int) $level) != 0) {
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
     * 
     * @return int 
     */
    public function getNestingLevel()
    {
        return strlen($this->getContent()) / self::INDENTATION_SPACES;
    }

    public function setNestingLevel($nestingLevel)
    {
        $this->content = str_repeat(' ', self::INDENTATION_SPACES * $nestingLevel);
    }
}