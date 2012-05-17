<?php
namespace PythoPhant;

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
     * @var array(varname => array(string type, string description, string default)) 
     */
    private $param = array();

    /**
     * annontation
     * @var array 
     */
    private $annotations = array();

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
        if (!isset($this->annotations[$tag])) {
            $this->annotations[$tag] = array();
        }
        $this->annotations[$tag][] = trim(implode(' ', $matches));
    }

    /**
     * set a param
     * 
     * @param array $words 
     * 
     * @return void
     */
    public function setParam(array $words)
    {
        if (!isset($words[0])) {
            return;
        }
        $type = $words[0];
        $var = $words[1];
        unset($words[0]);
        unset($words[1]);
        $default = null;
        if (isset($words[2]) && $words[2] == '=') {
            $default = $words[3];
            unset($words[2]);
            unset($words[3]);
        }
        $description = implode(' ', $words);
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
        if (!isset($this->annotations[$name])) {
            return null;
        }

        return $this->annotations[$name];
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
     * @param int $indentation 
     * 
     * @todo rebuild the whole content, insert missing dollar signs for vars 
     */
    public function getRebuiltContent($indentation = 0)
    {
        $indentToken = IndentationToken::create($indentation);
        $blanks = $indentToken->getContent();
        $linePrefix = ' * ';
        
        $content  = $blanks . '/**' . PHP_EOL;
        $content .= $blanks . $linePrefix . $this->shortDesc . PHP_EOL;
        $content .= $blanks . $linePrefix . PHP_EOL;
        
        foreach ($this->param as $varname => $values) {
            $tmp = implode(' ', array('@param', $values[0], '$' . $varname, $values[1]));
            $content .= $blanks . $linePrefix . $tmp . PHP_EOL; 
        }
        
        if (!empty($this->return)) {
            $content .= $blanks . $linePrefix . '@return '. $this->getReturnType() . PHP_EOL;            
        }
        
        foreach ($this->annotations as $var => $values) {
            foreach ($values as $annotation) {
                $content .= $blanks . $linePrefix . '@'. $var. ' ' . $annotation . PHP_EOL;  
            }
        }
        
        
        $content .= $blanks . ' */' .PHP_EOL;
        
        return $content;
    }

}