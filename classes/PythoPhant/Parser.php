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
     * lines
     * @var array 
     */
    private $lines = array();

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
        $this->parseLineEnds();
        $this->parseBlocks();
    }

    /**
     * the "magic" 
     */
    public function parseListAffections()
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
     * make lines based on newline tokens
     * 
     * @return void
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
            if ($prev = $this->tokenList->offsetGet($currentPos - 1)) {
                if ($this->tokenList->isTokenIncluded(
                    array($prev),
                    PythoPhant_Grammar::$preventSemicolon)
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
     * handle class or function declarations on a line
     * 
     * @param int $lineNumber
     * @param int $opened
     * 
     * @return void 
     */
    private function handleDeclaration($lineNumber, $opened)
    {
        $line = $this->lines[$lineNumber];
        
        $newlineToken = $line[count($line)-1];
        $currentPos = $this->tokenList->getTokenIndex($newlineToken);
        
        if(isset($this->lines[$lineNumber+1])) {
            $nextOpened = $this->isDeclarationOpened($this->lines[$lineNumber+1]);
            if ($nextOpened === self::EXPLICITLY_OPENED) {
                $newlineToken->setAuxValue('');
                return;
            }
        }

        $blockOpen = $this->tokenFactory->createToken(
            'T_DECLARATION_BLOCK_OPEN',
            PythoPhant_Grammar::T_OPEN_BLOCK,
            $newlineToken->getLine()
        );
        $this->tokenList->injectToken($blockOpen, $currentPos + 1);
        $this->tokenList->injectToken(NewLineToken::createEmpty(), $currentPos + 2);
        
        $nestingLevel = $this->getLineNestingLevel($line);
        $this->injectIndentationBefore($nestingLevel, $blockOpen);
        $newlineToken->setAuxValue("");
        $newlineToken->setContent(PHP_EOL);
        if ($opened !== self::EXPLICITLY_OPENED) {
            $this->injectFunctionInLine($line);
        }
    }
    
    /**
     *
     * @param Token $injected
     * @param Token $token 
     */
    private function injectTokenBefore(Token $injected, Token $token)
    {
        $currPos = $this->tokenList->getTokenIndex($token);
        $this->tokenList->injectToken($injected, $currPos);
    }

    /**
     *
     * @param Token $injected new token to be injected
     * @param Token $token 
     */
    private function injectTokenAfter(Token $injected, Token $token)
    {
        $currPos = $this->tokenList->getTokenIndex($token);
        $this->tokenList->injectToken($injected, $currPos + 1);
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
        $this->injectTokenBefore($injected, $token);
    }

    /**
     * inject "function" for function declaration
     * 
     * @param array $line 
     */
    private function injectFunctionInLine(array $line)
    {
        $visibilitySet = false;
        $function = $this->tokenFactory->createToken('T_FUNCTION', 'function ', 0);
            
        foreach ($line as $token) {
            if ($this->tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$modifiers)) {
                $visibilitySet = in_array($token->getTokenName(), PythoPhant_Grammar::$visibilities);
            }

            /**
             * return value is a clear sign to inject the function token
             */
            if (in_array(trim($token->getContent()), PythoPhant_Grammar::$returnValues)) {
                $this->injectTokenAfter($function, $token);
                break;
            }

            /**
             * T_STRING is the function name 
             */
            if ($next = $this->tokenList->getNextNonWhitespace($token)) {
                if ($next->getTokenName() == Token::T_STRING) {
                    $this->injectTokenBefore($function, $next);
                    break;
                }
            }
        }
        
        /**
         * @todo inject visibility as token 
         */
        if (!$visibilitySet) {
            $function->setContent('public function ');
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
         * indications: second (with indentation) token has a lead a and braces found 
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
                    $this->tokenFactory->createToken(Token::T_OPEN_BRACE, '('),
                    $index + 2
                );
                $index = $this->tokenList->getTokenIndex($line[count($line) - 1]);
                $this->tokenList->injectToken(
                    $this->tokenFactory->createToken(Token::T_CLOSE_BRACE, ')'),
                    $index
                );
                break;
            }
        }

        return $found;
    }

    /**
     * parses blocks based on indentation
     * 
     */
    public function parseBlocks()
    {
        /**
         * the last token has to be a newline 
         */
        $lastToken = $this->tokenList[count($this->tokenList)-1];
        if (!$lastToken instanceof NewLineToken) {
            $this->tokenList->pushToken(NewLineToken::createEmpty());
        }
        
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
                        $tok = $this->injectBlockClosingAfter($tok, $currentLevel);
                    }
                    
                    $lastToken->setContent(PHP_EOL);
                }
            }
            
            $currentLevel = $nestingLevel;
        }
        
        $this->closeClass($nestingLevel);
    }

    /**
     * checks the last token in list and inserts newline if necessary. then 
     * close open block with curly braces based in remaining indentation
     * 
     * @param int $currentLevel
     */
    private function closeClass($currentLevel)
    {
        while ($currentLevel > 0) {
            $currentLevel--;
            $lastToken = $this->tokenList[count($this->tokenList) - 1];
            $lastNonWhiteSpace = $this->tokenList->getPreviousNonWhitespace($lastToken, false);
            
            if ($lastNonWhiteSpace->getTokenName() == Token::T_CLOSE_BRACE) {
                $lastToken->setAuxValue(';');
            }
            $this->injectBlockClosingAfter(
                $lastToken, $currentLevel
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
        Token $token,
        $nestingLevel,
        $content = NULL
    ) {
        if ($content === NULL) {
            $content = PythoPhant_Grammar::T_CLOSE_BLOCK . PHP_EOL . PHP_EOL;
        }

        $index = $this->tokenList->getTokenIndex($token);
        if (!$token instanceof IndentationToken) {
            $this->tokenList->injectToken(
                IndentationToken::create($nestingLevel), $index + 1
            );
        } else {
            $token->setNestingLevel($nestingLevel);
        }
        
        $this->tokenList->injectToken(
            $close = $this->tokenFactory->createToken('T_CLOSE_BLOCK', $content),
            $index + 2
        );

        return $close;
    }
}
