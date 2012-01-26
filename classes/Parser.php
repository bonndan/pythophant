<?php

require_once dirname(__FILE__) . '/TokenFactory.php';
require_once dirname(__FILE__) . '/TokenList.php';

/**
 * Parser
 * 
 *  
 */
class Parser
{
    /**
     * return value for explicitly declared blocks 
     */
    const EXPLICITLY_OPENED = 2;
    
    const T_DECLARATION_BLOCK_OPEN = "{\n";
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
    
    private $lines = array();
    
    /**
     * status: inside a function?
     * @var type 
     */
    private $nestingLevel = 0;
    
    /**
     *
     * @var array 
     */
    private static $modifiers = array(
        'T_PRIVATE',
        'T_PROTECTED',
        'T_PUBLIC',
        'T_FINAL',
        'T_ABSTRACT'
    );
    
    /**
     * pass a filename and a token factory
     * 
     * @param TokenFactory $factory  factory instance
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
        $currentLine = 1;
        foreach ($this->tokenList as $index => $token) {
            if ($token instanceof CustomToken) {
                $token->affectTokenList($this->tokenList);
            }
        }
        
        $this->parseStringTokens();
        $this->makeLines();
        $this->parse1();
        $this->makeLines();
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
            $newlineToken = $line[count($line)-1];
            if (!$newlineToken instanceof NewLineToken) {
                continue;
            }
            
            
            $currentPos = $this->tokenList->getTokenIndex($newlineToken);
            if($prev = $this->tokenList->offsetGet($currentPos-1)) {
                if ($this->isTokenIncluded(array($prev), array('T_OPEN_TAG', 'T_DOC_COMMENT'))) {
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
                $this->tokenList->injectToken($blockOpen, $currentPos+1);
                $this->injectIndentationBefore($nestingLevel, $blockOpen);
                $newlineToken->setAuxValue("");
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
     * @param Token $injected
     * @param Token $token 
     */
    private function injectTokenAfter(Token $injected, Token $token)
    {
        $currPos = $this->tokenList->getTokenIndex($token);
        $this->tokenList->injectToken($injected, $currPos+1);
    }
    
    /**
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
        
        $injected = new IndentationToken(
            'T_INDENT',
            str_repeat(" ", 4 * $nestingLevel),
            $token->getLine()
        );
        $this->injectTokenBefore($injected, $token);
    }
    
    /**
     * inject "function" for function declaration
     * @param array $line 
     */
    private function injectFunctionInLine(array $line)
    {
        $function = new PHPToken('T_FUNCTION', 'function ', 0);
        foreach ($line as $token) {
            if ($this->isTokenIncluded(array($token), self::$modifiers)) {
                continue;
            }
            
            /**
             * return value is a clear sign to inject a  
             */
            if (in_array(trim($token->getContent()), TokenFactory::$returnValues)) {
                $this->injectTokenAfter($function, $token);
                return;
            }
            
            if ($next = $this->tokenList->getNextNonWhitespace($token)) {
                if ($next->getTokenName() == 'T_STRING') {
                    $this->injectTokenBefore($function, $next);
                    return;
                }
            }
        }
    }
    
    /**
     * check if a token of a list if within a list of token names
     * 
     * @param array $tokens
     * @param array $hayStack
     * 
     * @return boolean 
     */
    public function isTokenIncluded(array $tokens, array $hayStack)
    {
        foreach ($tokens as $token) {
            if (is_null($token)) {
                return false;
            }
            if (in_array($token->getTokenName(), $hayStack)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * check if a line is a declaration
     * 
     * @param array $line Token[]
     * 
     * @return boolean 
     */
    private function isDeclarationOpened(array $line)
    {
        $declarations = array('T_FUNCTION', 'T_CLASS', 'T_INTERFACE');
        if ($this->isTokenIncluded($line, $declarations)) {
            return self::EXPLICITLY_OPENED;
        }
        
        /**
         * indications: second (with indentation) token has a lead a and braces found 
         */
        $hasLead = in_array($line[1]->getTokenName(), self::$modifiers);
        $openBrace = $this->isTokenIncluded($line, array('T_OPEN_BRACE'));
        $closeBrace = $this->isTokenIncluded($line, array('T_CLOSE_BRACE'));
        
        return $hasLead && $openBrace && $closeBrace;
    }
    
    /**
     * check if a line is a declaration
     * 
     * @param array $line Token[]
     * 
     * @return boolean 
     */
    private function isBlock(array $line)
    {
        $blocks = array('T_IF', 'T_FOR', 'T_FOREACH', 'T_WHILE');
        foreach ($line as $token) {
            if (in_array($token->getTokenName(), $blocks)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 
     */
    private function parseBlocks()
    {
        $currentLevel = 0;
        foreach ($this->lines as $count => $line) {
            $nestingLevel = 0;
            if ($line[0] instanceof IndentationToken) {
                $nestingLevel = $line[0]->getNestingLevel();
            } 
            
            if ($nestingLevel < $currentLevel) {
                $prevLine = $this->lines[$count-1];
                $lastToken = $prevLine[count($prevLine)-1];
                if ($lastToken instanceof NewLineToken) {
                    $lastToken->setContent(PHP_EOL);
                    $this->injectBlockClosingAfter($lastToken, $nestingLevel);
                }
            }
            $currentLevel = $nestingLevel;
        }
        
        while($currentLevel > 0) {
            $currentLevel--;
            $lastToken = $this->tokenList[count($this->tokenList)-1];
            
            $this->injectBlockClosingAfter($lastToken, $currentLevel);
        }
    }
    
    private function injectBlockClosingAfter(Token $token, $nestingLevel)
    {
        $index = $this->tokenList->getTokenIndex($token);
        $this->tokenList->injectToken(
            IndentationToken::create($nestingLevel),
            $index+1
        );
        $close = new StringToken('T_CLOSE_BLOCK', self::T_BLOCK_CLOSE, 0);
        $this->tokenList->injectToken($close, $index+2);
    }
    
    /**
     *
     * @param type $string
     * @return int
     * 
     * @throws LogicException 
     */
    private function getIndentation($string)
    {
        $string = str_replace(self::T_NEWLINE, '', $string);
        $level = strlen($string) / self::INDENTATION_LEVEL;
        if ($level >= 1) {
            if (strlen($string) % self::INDENTATION_LEVEL) {
                throw new LogicException('Wrong indentation ' . strlen($string));
            }
            return (int) $level;
        }
        
        return 0;
    }
    
    /**
     * get the token list
     * 
     * @return TokenList 
     */
    public function getTokenList()
    {
        return $this->tokenList;
    }
}
