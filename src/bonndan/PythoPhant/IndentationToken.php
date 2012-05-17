<?php
namespace PythoPhant;

/**
 * IndentationToken
 * 
 * token representing indentation. Indentation depth is determined by number of
 * spaces. If eol occur, the number of whitespace after the last eol is used.
 *  
 */
class IndentationToken extends CustomGenericToken
{
    /**
     * @var int 
     */
    const INDENTATION_SPACES = 4;

    /**
     * @var int 
     */
    const SPACE = ' ';
    
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
        $content = str_repeat(self::SPACE, self::INDENTATION_SPACES * $nestingLevel);
        return new IndentationToken(Token::T_INDENT, $content, $line);
    }

    /**
     * set the content. indentation is computed by strlen. tabs are replaced by 
     * indentation spaces
     * 
     * @param type $content
     * @throws InvalidArgumentException 
     */
    public function setContent($content)
    {
        $content = str_replace("\t", str_repeat(self::SPACE, self::INDENTATION_SPACES), $content);
        parent::setContent($content);
        
        $level = $this->getNestingLevel();
        if (($level - (int) $level) != 0) {
            throw new Exception('Malformed indentation of ' . $level);
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
        $spaces = substr_count($this->getContent(), self::SPACE);
        return $spaces / self::INDENTATION_SPACES;
    }

    /**
     * set indentation depth
     * 
     * @param int $nestingLevel
     * 
     * @return void
     */
    public function setNestingLevel($nestingLevel)
    {
        $this->content = str_repeat(self::SPACE, self::INDENTATION_SPACES * (int)$nestingLevel);
    }
}