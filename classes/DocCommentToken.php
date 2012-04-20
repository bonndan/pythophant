<?php

/**
 * DocCommentToken
 * 
 * token representing a doc comment
 */
class DocCommentToken extends PHPToken
{
    /**
     * tags which appear more than once
     * @var type 
     */
    private $multiOccurence = array(
        'param',
        'throws',
        'author',
    );
    
    /**
     * short description
     * @var string 
     */
    private $shortDesc = '';
    
    /**
     * long description
     * @var string 
     */
    private $longDesc = '';
    
    /**
     * params noted in \@param
     * @var array 
     */
    private $param = array();
    
    /**
     * params noted in \@throws
     * @var array 
     */
    private $throws = array();
    
    /**
     * params noted in \@author
     * @var array 
     */
    private $author = array();
    
    /**
     * indent the whole doc block
     * 
     * @param int $level level of indentation
     */
    public function indent($level)
    {
        $indentationToken = IndentationToken::create($level);
        $whiteSpace = $indentationToken->getContent();

        $this->content = str_replace(PHP_EOL, PHP_EOL . $whiteSpace, $this->content);
        $this->content = rtrim($whiteSpace . $this->content);
    }

    /**
     * parse the doc comment
     * 
     * @return null|array
     * @link   http://css.dzone.com/news/reflection-over-phpdoc-php
     */
    public function processPHPDoc()
    {
        $docComment = $this->getContent();
        if (trim($docComment) == '') {
            return null;
        }
        
        $docComment = str_replace("\r\n", PHP_EOL, $docComment);
        $docComment = str_replace(array('/**', '*/'), '', $docComment);
        
        $lines = explode(PHP_EOL, $docComment);
        foreach ($lines as $line) {
            $line = trim(ltrim($line, " *"));
            if ($line == '') {
                continue;
            }
            
            if ($this->shortDesc === '') {
                $this->shortDesc = $line;
                continue;
            }
            
            if ($line[0] != '@') {
                $this->longDesc .= $line . PHP_EOL;
            } else {
                $matches = array();
                preg_match('#^@(\w+)\s+([\w|\\\]+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $line, $matches);
                $tag  = strtolower($matches[1]);
                $type = isset($matches[2]) ? $matches[2] : null;
                $name = isset($matches[3]) ? $matches[3] : null;
                $desc = isset($matches[4]) ? $matches[4] : null;
                $this->setTag($tag, $type, $name, $desc);
            }
        }
    }

    /**
     * set or add a tag
     * 
     * @param string $tag  name of the tag
     * @param string $type type or any first word after the tag
     * @param string $name variable name or any second word
     * @param string $desc optional third word
     * 
     * @return void
     */
    public function setTag($tag, $type, $name = null, $desc = null)
    {
        if (in_array($tag, $this->multiOccurence)) {
            array_push($this->$tag, array($type, $name, $desc));
        } else {
            $this->$tag = array($type, $name, $desc);
        }
    }
   
}