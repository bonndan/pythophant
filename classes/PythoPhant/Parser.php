<?php

/**
 * PythoPhant_Parser
 * 
 * @package PythoPhant
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 */
class PythoPhant_Parser implements Parser
{
    /**
     * @var TokenList
     */
    private $tokenList;

    /**
     * class or interface that is built
     * @var PythoPhant_Reflection_Class|PythoPhant_Reflection_Interface
     */
    private $class;

    /**
     * lines
     * @var array 
     */
    private $lines = array();

    /**
     * currently processed method
     * @var PythoPhant_Reflection_MemberAbstract 
     */
    private $currentElement = null;

    /**
     * preamble
     * @var TokenList
     */
    private $preamble = null;
    
    /**
     * set the tokenlist to use
     * 
     * @param TokenList $tokenList
     * 
     * @return \PythoPhant_Parser 
     */
    public function setTokenList(TokenList $tokenList)
    {
        $this->tokenList = $tokenList;
        return $this;
    }

    /**
     * parse the token list for class or interface declaration
     * 
     * @param TokenList $tokenList
     */
    public function parseElement(TokenList $tokenList)
    {
        $this->setTokenList($tokenList);

        $this->findClass();
        $this->findPreamble();
        $this->findClassElements();
        //class processTokenList recursively for each elemtn of class
        $this->class->parseListAffections($this);
    }
    
    /**
     * finds any tokens before class/interface declaration
     * 
     * 
     */
    private function findPreamble()
    {
        $lines = $this->tokenList->makeLines();
        $this->preamble = new TokenList();
        $declarations = PythoPhant_Grammar::$declarations;
        foreach ($lines as $line) {
            if ($line[0] instanceof IndentationToken) {
                continue;
            }

            if ($line[0] instanceof DocCommentToken) {
                continue;
            }
            
            if ($line[0]->getTokenName() == 'T_OPEN_TAG') {
                continue;
            }
            
            if (in_array($line[0]->getTokenName(), $declarations)) {
                break;
            }
            
            foreach ($line as $token) {
                $this->preamble->pushToken($token);
            }
        }
        
        $this->class->setPreamble($this->preamble);
    }

    /**
     * find the class declaration and instantiate a reflection class
     * 
     * @throws PythoPhant_Exception
     */
    private function findClass()
    {
        $type = null;
        $extends = null;
        $implements = array();
        $docComment = new DocCommentToken('T_DOC_COMMENT', '', 0);

        $this->lines = $this->tokenList->makeLines();
        foreach ($this->lines as $line) {
            if ($line[0] instanceof IndentationToken) {
                continue;
            }

            if ($line[0] instanceof DocCommentToken) {
                $docComment = $line[0];
                continue;
            }


            $firstTokenName = $line[0]->getTokenName();
            if ($firstTokenName == 'T_CLASS' ) {
                $type = 'PythoPhant_Reflection_Class';
                $name = $this->tokenList->getNextNonWhitespace($line[0]);
            } elseif ($firstTokenName == 'T_INTERFACE') {
                $type = 'PythoPhant_Reflection_Interface';
                $name = $this->tokenList->getNextNonWhitespace($line[0]);
            } else {
                continue;
            }
            
            $this->class = new $type($name->getContent(), $docComment);
            $this->findExtendsAndImplements($line[0]);
            return;
        }
    }
    
    /**
     * finds and reads implements, extends and their class names
     * 
     * @param Token $token
     * 
     * @return void
     */
    private function findExtendsAndImplements(Token $token)
    {
        $mode = null;
        $index = $this->tokenList->getTokenIndex($token);
        while ($index++) {
            if (!$this->tokenList->offsetExists($index)) {
                break;
            }
            
            $token = $this->tokenList->offsetGet($index);
            if ($token instanceof IndentationToken && $token->getNestingLevel() > 0) {
                break;
            }
            $tokenName = $token->getTokenName();
            
            if ($tokenName == 'T_IMPLEMENTS') {
                $mode = 'implements';
            } elseif ($tokenName == 'T_EXTENDS') {
                $mode = 'extends';
            }
            
            if ($token instanceof StringToken) {
                if ($mode == 'extends') {
                    $this->class->setExtends($token);
                } elseif ($mode == 'implements') {
                    $this->class->setImplements(array($token->getContent() => $token));
                }
            }
        }
    }

    /**
     * returns the representation of the class or interface which is currently
     * built
     * 
     * @return PythoPhant_Reflection_Element
     */
    public function getElement()
    {
        return $this->class;
    }

    /**
     * searches the source code lines for things on the first indentation level 
     */
    private function findClassElements()
    {
        $this->lines = $this->tokenList->makeLines();
        $docComment = null;
        $declaration = null;
        
        foreach ($this->lines as $line) {
            $first = $line[0];
            if (!$first instanceof IndentationToken) {
                continue;
            }
            if ($first->getNestingLevel() == 1) {
                $second = $line[1];
                if ($second instanceof DocCommentToken) {
                    $docComment = $second; 
                } else {
                    $closers = array(
                        PythoPhant_Grammar::T_CLOSE_BLOCK,
                        PythoPhant_Grammar::T_CLOSE_BRACE,
                    );
                    if (in_array($second->getContent(), $closers)) {
                        continue;
                    }
                    $declaration = $second;
                    if ($docComment === null) {
                        throw new PythoPhant_Exception(
                            'Declaration: ' . $declaration->getContent()
                            . ' must be preceded by doc comment',
                            $first->getLine()
                        );
                    }
                    $this->handleElementDeclaration($line, $docComment);
                    $docComment = null;
                }
            } else {
                $this->currentElement->addBodyTokens($line);
            }
        }
    }

    /**
     * set the corresponding currentElement
     * 
     * @param array           $line       tokens of the declaration line
     * @param DocCommentToken $docComment doc comment token
     * 
     * @return void
     */
    private function handleElementDeclaration(array $line, DocCommentToken $docComment)
    {
        $modifiers = array();
        $returnValue = null;
        $body = array();
        
        if (!$docComment->isMethodComment()) {
            if ($line[1]->getContent() == 'const') {
                $type = 'PythoPhant_Reflection_ClassConst';
                $setter = 'addConstant';
                $name = $line[3]->getContent(); /* @todo not nice */
            } else {
                $type = 'PythoPhant_Reflection_ClassVar';
                $setter = 'addVar';
            }
            
        } else {
            $type = 'PythoPhant_Reflection_Function';
            $setter = 'addMethod';
        }
        
        foreach ($line as $i => $token) {
            if ($this->tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$modifiers)) {
                $modifiers[] = $token;
            }
            if ($token instanceof ReturnValueToken) {
                $returnValue = $token;
            }
            
            if ($token instanceof StringToken) {
                $name = str_replace('$', '', $token->getContent());
            }
            
            /*
             * break if body assignment is found (vars and consts) 
             */
            if ($token->getContent() == PythoPhant_Grammar::T_ASSIGN) {
                for($j = $i - 1; $j<count($line); $j++) {
                    $body[] = $line[$j];
                }
                break;
            }
        }
        
        /*
         * create new element
         */
        /* @var $element PythoPhant_Reflection_Member */
        $element = new $type($name, $docComment);
        if ($returnValue !== null) {
            $element->setType($returnValue);
        }
        if (count($modifiers) > 0) {
            $element->setModifiers($modifiers);
        }
        if (count($body) > 0) {
            $element->addBodyTokens($body);
        }
        
        $this->class->$setter($element);
        $this->currentElement = $element;
    }
    
     /**
     * the "magic". First the "parsed early" tokens are processed, beginning with
     * the first token in the list. The second pass treats all other tokens which
     * could affect the list.
     * 
     * @param TokenList $tokenList
     * 
     * @return void
     */
    public function processTokenList(TokenList $tokenList)
    {
        foreach ($tokenList as $token) {
            if ($token instanceof ParsedEarlyToken) {
                $token->affectTokenList($tokenList);
            }
        }

        foreach ($tokenList as $token) {
            if ($token instanceof CustomToken && !$token instanceof ParsedEarlyToken) {
                $token->affectTokenList($tokenList);
            }
        }
    }
}
