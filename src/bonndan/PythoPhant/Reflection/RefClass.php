<?php
namespace PythoPhant\Reflection;

use PythoPhant\Token as Token;
use PythoPhant\TokenList as TokenList;
use PythoPhant\Core\Parser as Parser;

/**
 * PythoPhant_Class
 * 
 * representation of a class, like ReflectionClass
 */
class RefClass extends RefInterface
{

    /**
     * class scope
     * @var array 
     */
    private $vars = array();

    /**
     * names of the implemented interfaces
     * @var array(string) 
     */
    private $implements = array();

    /**
     * add a class variable
     * 
     * @param ClassVar $var
     * 
     * @return RefClass 
     */
    public function addVar(ClassVar $var)
    {
        $this->vars[$var->getName()] = $var;
        return $this;
    }

    /**
     * get the declared class vars
     * @return array 
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * set the implemented interfaces
     * 
     * @param array $interfaces
     * 
     * @return RefClass
     */
    public function setImplements(array $interfaces)
    {
        foreach ($interfaces as $content) {
            if ($content instanceof Token) {
                $content = $content->getContent();
            }
            $this->implements[] = $content;
        }
        
        $this->implements = array_unique($this->implements);
        return $this;
    }

    /**
     * get all implemented interfaces
     * 
     * @return array 
     */
    public function getImplements()
    {
        return $this->implements;
    }
    
    /**
     * have all necessary parts (token lists for these parts) parsed
     * 
     * @param Parser $parser
     */
    public function parseListAffections(Parser $parser)
    {
        if ($this->preamble instanceof TokenList) {
            $parser->processTokenList($this->preamble);
        }
        
        $members = array_merge($this->getVars(), $this->getConstants(), $this->getMethods());
        
        foreach ($members as $var) {
            $tokenList = $var->getBodyTokenList();
            $parser->processTokenList($tokenList);
        }
    }

}