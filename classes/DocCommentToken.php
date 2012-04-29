<?php

/**
 * DocCommentToken
 * 
 * token representing a doc comment
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class DocCommentToken extends PHPToken
{

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
     * @var array(array(string type, string description, string default)) 
     */
    private $param = array();

    /**
     * excptions noted in \@throws
     * @var array 
     */
    private $throws = array();

    /**
     * params noted in \@author
     * @var array 
     */
    private $author = array();

    /**
     * return value
     * @var array(string type, string description)
     */
    private $return = array();

    /**
     * overrides setContent, parse the doc block
     * 
     * @param string $content doc block
     * 
     * @return DocCommentToken
     */
    public function setContent($content)
    {
        parent::setContent($content);
        $this->processPHPDoc();
        return $this;
    }

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

            if ($line[0] != '@') {
                if ($this->shortDesc === '') {
                    $this->shortDesc = $line;
                    continue;
                }
                $this->appendToLongDescription($line . PHP_EOL);
            } else {
                $this->parseAnnotationLine($line);
            }
        }
    }

    /**
     * parse a line
     * 
     * @param string $line 
     */
    private function parseAnnotationLine($line)
    {
        $matches = explode(' ', $line);
        foreach ($matches as $key => $string) {
            if ($string == '') {
                unset($matches[$key]);
            }
        }

        $tag = substr(array_shift($matches), 1);

        $matches = array_values($matches);
        $setter = 'set' . ucfirst($tag);
        if (method_exists($this, $setter)) {
            $this->$setter($matches);
        } else {
            $this->setAnnotation($tag, $matches);
        }
    }

    /**
     * add an annotation
     * 
     * @param string $tag     name of the tag
     * @param array  $matches all words including $tag
     * 
     * @return void
     */
    public function setAnnotation($tag, array $matches)
    {
        if (!isset($this->$tag)) {
            $this->$tag = array();
        }
        array_push($this->$tag, trim(implode(' ', $matches)));
    }

    /**
     * set a param
     * 
     * @param array $matches 
     * 
     * @return void
     */
    public function setParam(array $matches)
    {
        if (!isset($matches[0])) {
            return;
        }
        $type = $matches[0];
        $var = $matches[1];
        unset($matches[0]);
        unset($matches[1]);
        $default = null;
        if (isset($matches[2]) && $matches[2] == '=') {
            $default = $matches[3];
            unset($matches[2]);
            unset($matches[3]);
        }
        $description = implode(' ', $matches);
        $this->param[$var] = array($type, $description, $default);
    }

    /**
     * set the return annotation values
     * 
     * @param array $matches return annotation words
     * 
     * @return void
     */
    public function setReturn(array $matches)
    {
        if (!isset($matches[0])) {
            return;
        }
        $type = $matches[0];
        unset($matches[0]);
        $description = implode(' ', $matches);
        $this->return = array($type, $description);
    }

    /**
     * returns the short description
     * 
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDesc;
    }

    /**
     * returns the long description
     * 
     * @return string 
     */
    public function getLongDescription()
    {
        return $this->longDesc;
    }

    /**
     * public function append something to the long description
     * 
     * @param string $text text
     */
    public function appendToLongDescription($text)
    {
        $this->longDesc .= $text;
    }

    /**
     * get the found param annotations
     * 
     * @return array 
     */
    public function getParams()
    {
        return $this->param;
    }

    /**
     * type of the return value
     * 
     * @return string 
     */
    public function getReturnType()
    {
        return $this->return[0];
    }

    /**
     * get any annotation
     * 
     * @param string $name
     * 
     * @return array 
     */
    public function getAnnotation($name)
    {
        if (!isset($this->$name)) {
            return null;
        }

        return $this->$name;
    }

    /**
     * check if the comment is for a class method. Determined by the presence
     * of a "var" annotation
     * 
     * @return boolean
     */
    public function isMethodComment()
    {
        return count($this->getAnnotation('var')) == 0;
    }

    /**
     * rebuild the content by the parsed content
     * 
     * @todo rebuild the whole content, insert missing dollar signs for vars 
     */
    public function getRebuiltContent()
    {
        
    }

}