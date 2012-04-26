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
     * token factory
     * @var TokenFactory 
     */
    private $tokenFactory;

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
     * @var PythoPhant_Reflection_Method 
     */
    private $currentElement = null;

    /**
     * pass a filename and a token factory
     * 
     * @param TokenFactory $factory factory instance
     */
    public function __construct(TokenFactory $factory)
    {
        $this->tokenFactory = $factory;
    }

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
     * process a token list. custom tokens are processed before string tokens
     * 
     * @param TokenList $tokenList
     */
    public function processTokenList(TokenList $tokenList)
    {
        $this->setTokenList($tokenList);

        $this->parseListAffections();
        $this->findClass();
        $this->findClassElements();
        
        foreach($this->class->getConstants() as $const) {
            $this->parseBlocks($const->getBodyTokenList());
        }
        
        foreach($this->class->getMethods() as $method) {
            $this->parseBlocks($method->getBodyTokenList());
        }
        
        if (!$this->class instanceof PythoPhant_Reflection_Class) {
            return;
        }
        
        /**
         *  
         */
        foreach($this->class->getVars() as $var) {
            $this->parseBlocks($var->getBodyTokenList());
        }
        
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
            if ($firstTokenName == 'T_CLASS') {
                $type = 'PythoPhant_Reflection_Class';
                $name = $this->tokenList->getNextNonWhitespace($line[0]);
            } elseif ($firstTokenName == 'T_INTERFACE') {
                $type = 'PythoPhant_Reflection_Interface';
                $name = $this->tokenList->getNextNonWhitespace($line[0]);
            } elseif ($firstTokenName == 'T_EXTENDS') {
                $extends = $this->tokenList->getNextNonWhitespace($line[0]);
            } elseif ($firstTokenName == 'T_IMPLEMENTS') {
                foreach ($line as $i => $token) {
                    if ($i == 0) {
                        continue;
                    }
                    if ($token instanceof NewLineToken) {
                        break;
                    }
                    if ($token instanceof StringToken) {
                        $implements[] = $token;
                    }
                }
            }
        }

        if (!$name) {
            throw new PythoPhant_Exception('Could not detect class', 0);
        }

        $this->class = new $type($name->getContent(), $docComment);
        if ($extends) {
            $this->class->setExtends($extends);
        }
        if ($this->class instanceof PythoPhant_Reflection_Class) {
            $this->class->setImplements($implements);
        }
    }

    /**
     * returns the representation of the class or interface which is currently
     * built
     * 
     * @return PythoPhant_Reflection_Element
     */
    public function getReflectionElement()
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
                    $declaration = $second;
                    if ($docComment === null) {
                        throw new PythoPhant_Exception(
                            'Declaration ' . $declaration->getContent()
                            . 'must be preceded by doc comment',
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
        
        if ($docComment->getAnnotation('var') !== null) {
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
     */
    private function parseListAffections()
    {
        foreach ($this->tokenList as $token) {
            if ($token instanceof ParsedEarlyToken) {
                $token->affectTokenList($this->tokenList);
            }
        }

        foreach ($this->tokenList as $token) {
            if ($token instanceof CustomToken && !$token instanceof ParsedEarlyToken) {
                $token->affectTokenList($this->tokenList);
            }
        }
    }

    /**
     * check if a line is a block declaration
     * 
     * - injects opening and closing braces (unless "else" is used)
     * 
     * @param array     $line      Token[]
     * @param TokenList $tokenList 
     * 
     * @return boolean 
     */
    private function handleControls(array $line, TokenList $tokenList)
    {
        $found = false;
        foreach ($line as $token) {
            if (in_array($token->getTokenName(), PythoPhant_Grammar::$controls)) {
                $found = true;
                $index = $tokenList->getTokenIndex($token);

                if (in_array($token->getTokenName(), PythoPhant_Grammar::$controlsWithoutBraces)) {
                    break;
                }
                $tokenList->injectToken(
                    $this->tokenFactory->createToken(Token::T_OPEN_BRACE, '('), $index + 2
                );
                $index = $tokenList->getTokenIndex($line[count($line) - 1]);
                $tokenList->injectToken(
                    $this->tokenFactory->createToken(Token::T_CLOSE_BRACE, ')'), $index
                );
                break;
            }
        }

        return $found;
    }

    /**
     * parses blocks based on indentation
     * 
     * @param TokenList $tokenList
     */
    public function parseBlocks(TokenList $tokenList)
    {
        $this->parseLineEnds($tokenList);
        
        $lines = $tokenList->makeLines();
        
        $currentLevel = 1;
        $nestingLevel = 1;
        foreach ($lines as $count => $line) {
            $nestingLevel = 0;
            if ($line[0] instanceof IndentationToken) {
                $nestingLevel = $line[0]->getNestingLevel();
            }
            $lastToken = $line[count($line) -1];
            
            if (isset($lines[$count + 1])) {
                $nextLine = $lines[$count + 1];
            } else {
                /* close with } until level 1 reached */
                while ($currentLevel > 1) {
                    $currentLevel--;
                    $this->injectBlockClosingAfter($tokenList, $lastToken, $currentLevel);
                }
                return;
            }
            
            /*
             * determine next line indentation level, empty line is skipped
             */
            if ($nextLine[0] instanceof IndentationToken) {
                $nextLevel = $nextLine[0]->getNestingLevel();
            } else {
                continue;
            }
            
            /*
             * next line is in block
             */
            if ($currentLevel < $nextLevel) {
                $explicitlyOpened = $tokenList->isTokenIncluded(
                    array($lastToken),
                    PythoPhant_Grammar::$blockOpeners
                );
                if ($explicitlyOpened) {
                    continue;
                }
                $tokenList->injectToken(
                    new PHPToken(Token::T_OPEN_BRACE, PythoPhant_Grammar::T_OPEN_BLOCK, $currentLevel),
                    $lastToken
                );
            /*
             * inject a block closer "}" if the previous token is not a closer 
             */
            } elseif ($nestingLevel > $currentLevel) {
                $explicitlyClosed = $tokenList->isTokenIncluded(
                    array($lastToken),
                    PythoPhant_Grammar::$blockClosers
                );
                if ($explicitlyClosed) {
                    continue;
                }
                $tokenList->injectToken(
                    new PHPToken(Token::T_CLOSE_BLOCK, PythoPhant_Grammar::T_CLOSE_BLOCK, $currentLevel),
                    $lastToken
                );
            }

            $currentLevel = $nestingLevel;
        }
    }

    /**
     * - set appropriate newline token content 
     * - inject opening braces
     * - inject function declarations
     */
    private function parseLineEnds(TokenList $tokenList)
    {
        $lines = $tokenList->makeLines();
        foreach ($lines as $index => $line) {
            $newlineToken = $line[count($line) - 1];
            //can be removed?
            if (!$newlineToken instanceof NewLineToken) {
                continue;
            }

            $this->handleControls($line, $tokenList);
                
            /*
             * remove semicolons from line ends 
             */
            $currentPos = $tokenList->getTokenIndex($newlineToken);
            if ($prev = $tokenList->getPreviousNonWhitespace($newlineToken)) {
                $preventSemicolon = $tokenList->isTokenIncluded(
                    array($prev),
                    PythoPhant_Grammar::$preventSemicolon
                );
                    
                if ($preventSemicolon) {
                    $newlineToken->setAuxValue("");
                    continue;
                }
            }
        }
    }
    
    /**
     * injects indentation and curly brace
     * 
     * @param TokenList  $tokenList
     * @param Token      $token
     * @param int        $nestingLevel 
     * @param string     $content 
     * 
     * @return StringToken
     */
    private function injectBlockClosingAfter(
        TokenList $tokenList,
        Token $token,
        $nestingLevel,
        $content = NULL
    )  {
        if ($content === NULL) {
            $content = PythoPhant_Grammar::T_CLOSE_BLOCK . PHP_EOL . PHP_EOL;
        }

        $index = $tokenList->getTokenIndex($token);
        if (!$token instanceof IndentationToken) {
            $tokenList->injectToken(
                IndentationToken::create($nestingLevel), $index + 1
            );
        } else {
            $token->setNestingLevel($nestingLevel);
        }

        $close = $this->tokenFactory->createToken(Token::T_CLOSE_BLOCK, $content);
        $tokenList->injectToken($close, $index + 2);

        return $close;
    }

}
