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
     * @todo move to grammer or style 
     */
    const T_DECLARATION_BLOCK_OPEN = "{";
    const T_BLOCK_OPEN = " {";
    const T_BLOCK_CLOSE = "}\n\n";

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
     * status: inside a function?
     * @var type 
     */
    private $nestingLevel = 0;


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
     * process a token list
     * 
     * @param TokenList $tokenList
     */
    public function processTokenList(TokenList $tokenList)
    {
        $this->tokenList = $tokenList;
        
        foreach ($this->tokenList as $token) {
            if ($token instanceof CustomToken) {
                $token->affectTokenList($this->tokenList);
            }
        }

        $this->makeLines();
        $this->parse1();
        $this->parseStringTokens();
        
        $this->parseBlocks();
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
     * process all string token to turn the into their destinated usage or have 
     * them modify the token list
     * 
     * @return void 
     */
    public function parseStringTokens()
    {
        foreach ($this->tokenList as $token) {
            if ($token instanceof StringToken) {
                $token->affectTokenList($this->tokenList);
            }
        }
    }

    /**
     * - set appropriate newline token content 
     * - inject opening braces
     * - inject function declarations
     */
    public function parse1()
    {
        foreach ($this->lines as $index => $line) {
            $nestingLevel = 0;
            $indentToken = $line[0];
            if ($indentToken instanceof IndentationToken) {
                $nestingLevel = $indentToken->getNestingLevel();
            }
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
                $blockOpen = $this->tokenFactory->createToken(
                    'T_DECLARATION_BLOCK_OPEN',
                    self::T_DECLARATION_BLOCK_OPEN,
                    $newlineToken->getLine()
                );
                $this->tokenList->injectToken($blockOpen, $currentPos + 1);
                $nl = $this->tokenFactory->createToken(
                    'T_NEWLINE',
                    PHP_EOL,
                    $newlineToken->getLine() +1
                );
                $nl->setAuxValue('');
                $this->tokenList->injectToken($nl, $currentPos + 2);
                $this->injectIndentationBefore($nestingLevel, $blockOpen);
                $newlineToken->setAuxValue("");
                $newlineToken->setContent(PHP_EOL);
                if ($opened !== self::EXPLICITLY_OPENED) {
                    $this->injectFunctionInLine($line);
                }
                $this->nestingLevel++;
            } elseif ($this->isBlock($line)) {
                $newlineToken->setAuxValue(self::T_BLOCK_OPEN);
                $this->nestingLevel++;
            }
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
        $function = new PHPToken('T_FUNCTION', 'function ', 0);
        foreach ($line as $token) {
            if ($this->tokenList->isTokenIncluded(array($token), PythoPhant_Grammar::$modifiers)) {
                $visibilitySet = in_array($token->getTokenName(), PythoPhant_Grammar::$visibilities);
                continue;
            }

            /**
             * return value is a clear sign to inject the function token
             */
            if (in_array(trim($token->getContent()), TokenFactory::$returnValues)) {
                $this->injectTokenAfter($function, $token);
                break;
            }

            if ($next = $this->tokenList->getNextNonWhitespace($token)) {
                if ($next->getTokenName() == 'T_STRING') {
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
            $hasLead = $line[0]->getNestingLevel() == 1;
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
    private function isBlock(array $line)
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
    private function parseBlocks()
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
            $content = self::T_BLOCK_CLOSE;
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
