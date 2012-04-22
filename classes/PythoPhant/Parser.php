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
     * return value for explicitly declared blocks 
     * @var int
     */

    const EXPLICITLY_OPENED = 2;

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
     * @var PythoPhant_Class|PythoPhant_Interface
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

        $this->findClass();
        $this->findClassElements();
        $this->parseListAffections();
        
        foreach($this->class->getMethods() as $method) {
            $this->parseBlocks($method->getBodyTokenList());
        }
        
        if (!$this->class instanceof PythoPhant_Reflection_Class) {
            return;
        }
        foreach($this->class->getVars() as $var) {
            $this->parseBlocks($var->getBodyTokenList());
        }
        
    }

    /**
     * make lines based on newline tokens
     * 
     * @return void
     * @todo return array, remove $this->lines class var
     */
    private function makeLines()
    {
        $currentLine = 1;
        $this->lines = array();
        foreach ($this->tokenList as $token) {
            $this->lines[$currentLine][] = $token;
            if ($token instanceof NewLineToken) {
                $currentLine++;
            }
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

        $this->makeLines();
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
     * @return PythoPhant_Reflection_Class|PythoPhant_Reflection_Interface|null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * searches the source code lines for things on the first indentation level 
     */
    private function findClassElements()
    {
        $this->makeLines();
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
     * @param DocCommentToken $docComment 
     */
    private function handleElementDeclaration(array $line, DocCommentToken $docComment)
    {
        $modifiers = array();
        $returnValue = null;

        if ($this->tokenList->isTokenIncluded($line, array(Token::T_CONST))) {
            //const
            return;
        }

        if ($docComment->getAnnotation('var') !== null) {
            $type = 'PythoPhant_Reflection_ClassVar';
            $setter = 'addVar';
        } else {
            $type = 'PythoPhant_Reflection_Function';
            $setter = 'addMethod';
        }
        
        foreach ($line as $token) {
            if ($this->tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$modifiers)) {
                $modifiers[] = $token;
            }
            if ($token instanceof ReturnValueToken) {
                $returnValue = $token;
            }
            if ($token instanceof StringToken) {
                $name = $token->getContent();
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
     * - set appropriate newline token content 
     * - inject opening braces
     * - inject function declarations
     */
    public function parseLineEnds()
    {
        $this->makeLines();
        foreach ($this->lines as $index => $line) {
            $newlineToken = $line[count($line) - 1];
            if (!$newlineToken instanceof NewLineToken) {
                continue;
            }

            /**
             * remove semicolons from line ends 
             */
            $currentPos = $this->tokenList->getTokenIndex($newlineToken);
            if ($prev = $this->tokenList->getPreviousNonWhitespace($newlineToken)) {
                if ($this->tokenList->isTokenIncluded(
                        array($prev), PythoPhant_Grammar::$preventSemicolon)
                ) {
                    $newlineToken->setAuxValue("");
                    continue;
                }
            }

            $opened = $this->isDeclarationOpened($line);
            if ($opened != false) {
                $this->handleDeclaration($index, $opened);
            } elseif ($this->isBlockOpened($line)) {
                $newlineToken->setAuxValue(' ' . PythoPhant_Grammar::T_OPEN_BLOCK);
            }
        }
    }

    /**
     * get the nesting level (indentation depth) of a line
     * 
     * @param array $line
     * 
     * @return int 
     */
    private function getLineNestingLevel(array $line)
    {
        $nestingLevel = 0;
        $indentToken = $line[0];
        if ($indentToken instanceof IndentationToken) {
            $nestingLevel = $indentToken->getNestingLevel();
        }

        return $nestingLevel;
    }

    /**
     * indents a token
     * 
     * @param int   $nestingLevel
     * @param Token $token
     * 
     * @return void 
     */
    private function injectIndentationBefore($nestingLevel, Token $token)
    {
        if ($nestingLevel == 0) {
            return;
        }

        $injected = IndentationToken::create($nestingLevel, $token->getLine());
        $this->tokenList->injectToken($injected, $token);
    }

    /**
     * inject "function" for function declaration
     * 
     * @param array $line array of tokens on the same line
     * 
     * @return void
     */
    private function injectFunctionInLine(array $line)
    {
        $visibilitySet = false;

        foreach ($line as $token) {
            if ($this->tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$modifiers)) {
                $visibilitySet = in_array($token->getTokenName(), PythoPhant_Grammar::$visibilities);
            }

            /**
             * return value is a clear sign to inject the function token
             */
            if (in_array(trim($token->getContent()), PythoPhant_Grammar::$returnValues)) {
                $function = $this->tokenFactory->createToken('T_FUNCTION', 'function ', $token->getLine());
                $currPos = $this->tokenList->getTokenIndex($token);
                $this->tokenList->injectToken($function, $currPos + 1);
                break;
            }

            /**
             * T_STRING is the function name 
             */
            if ($next = $this->tokenList->getNextNonWhitespace($token)) {
                $function = $this->tokenFactory->createToken('T_FUNCTION', 'function ', $next->getLine());
                if ($next->getTokenName() == Token::T_STRING) {
                    $this->tokenList->injectToken($function, $next);
                    break;
                }
            }
        }

        /**
         * inject visibility as token 
         * @todo handle exception
         */
        try {
            $functionIndex = $this->tokenList->getTokenIndex($function);
            if (!$visibilitySet) {
                $public = new PHPToken('T_PUBLIC', 'public ', $function->getLine());
                $this->tokenList->injectToken($public, $functionIndex);
            }
        } catch (OutOfBoundsException $exc) {
            
        }
    }

    /**
     * check if a line is a declaration
     * 
     * - it must contain a modifier or be indented by one
     * - must contain opening and closing braces
     * 
     * @param array $line Token[]
     * 
     * @return boolean 
     */
    private function isDeclarationOpened(array $line)
    {
        if ($this->tokenList->isTokenIncluded($line, PythoPhant_Grammar::$declarations)) {
            return self::EXPLICITLY_OPENED;
        }

        /**
         * indications: second (with indentation) token has a lead and braces found 
         */
        $hasLead = in_array($line[1]->getTokenName(), PythoPhant_Grammar::$modifiers);
        if (!$hasLead && $line[0] instanceof IndentationToken) {
            $hasLead = $line[0]->getNestingLevel() == 1
                &&
                !$this->tokenList->isTokenIncluded(array($line[1]), PythoPhant_Grammar::$controls);
        }
        $openBrace = $this->tokenList->isTokenIncluded($line, array(Token::T_OPEN_BRACE));
        $closeBrace = $this->tokenList->isTokenIncluded($line, array(Token::T_CLOSE_BRACE));

        return $hasLead && $openBrace && $closeBrace;
    }

    /**
     * check if a line is a block declaration
     * 
     * - injects opening and closing braces (unless "else" is used)
     * 
     * @param array $line Token[]
     * 
     * @return boolean 
     */
    private function isBlockOpened(array $line)
    {
        $found = false;
        foreach ($line as $token) {
            if (in_array($token->getTokenName(), PythoPhant_Grammar::$controls)) {
                $found = true;
                $index = $this->tokenList->getTokenIndex($token);

                if (in_array($token->getTokenName(), PythoPhant_Grammar::$controlsWithoutBraces)) {
                    break;
                }
                $this->tokenList->injectToken(
                    $this->tokenFactory->createToken(Token::T_OPEN_BRACE, '('), $index + 2
                );
                $index = $this->tokenList->getTokenIndex($line[count($line) - 1]);
                $this->tokenList->injectToken(
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
        $this->makeLines();

        $currentLevel = 0;
        foreach ($this->lines as $count => $line) {
            $nestingLevel = 0;
            if ($line[0] instanceof IndentationToken) {
                $nestingLevel = $line[0]->getNestingLevel();
            }

            /**
             * inject a block closer "}" if the previous token is not a closer 
             */
            if ($nestingLevel < $currentLevel) {
                if (isset($line[1]) && in_array($line[1]->getTokenName(), PythoPhant_Grammar::$blockClosers)) {
                    $currentLevel = $nestingLevel;
                    continue;
                }
                $prevLine = $this->lines[$count - 1];
                $lastToken = $prevLine[count($prevLine) - 1];
                if ($lastToken instanceof NewLineToken) {
                    $tok = $lastToken;
                    while ($currentLevel > $nestingLevel) {
                        $currentLevel--;
                        $tok = $this->injectBlockClosingAfter($tokenList, $tok, $currentLevel);
                    }

                    $lastToken->setContent(PHP_EOL);
                }
            }

            $currentLevel = $nestingLevel;
        }

        $this->closeBody($nestingLevel, $tokenList);
    }

    /**
     * checks the last token in list and inserts newline if necessary. then 
     * close open block with curly braces based on remaining indentation
     * 
     * @param int $currentLevel
     */
    private function closeBody($currentLevel, TokenList $tokenList)
    {
        if (count($tokenList) == 0) {
            return;
        }
        
        while ($currentLevel > 1) {
            $currentLevel--;
            $lastToken = $tokenList[count($tokenList) - 1];
            $lastNonWhiteSpace = $tokenList->getPreviousNonWhitespace($lastToken, false);

            if ($lastNonWhiteSpace && $lastNonWhiteSpace->getTokenName() == Token::T_CLOSE_BRACE) {
                $lastToken->setAuxValue(';');
            }
            $this->injectBlockClosingAfter(
                $tokenList,
                $lastToken,
                $currentLevel
            );
        }
    }

    /**
     * injects indentation and curly brace
     * 
     * @param Token  $token
     * @param int    $nestingLevel 
     * @param string $content 
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
