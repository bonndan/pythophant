<?php

/**
 * NewLineToken
 * 
 * Token representing the last token of a line. Can contain ; or { or } or nothing 
 */
class NewLineToken extends CustomGenericToken
{
    /**
     * state constants
     */
    const STATE_REGULAR_LINE  = 'STATE_REGULAR_LINE';
    const STATE_EMTPY_LINE    = 'STATE_EMTPY_LINE';
    const STATE_LAST_LINE     = 'STATE_LAST_LINE';
    const STATE_NO_SEMICOLON  = 'STATE_NO_SEMICOLON';
    const STATE_OPEN_BLOCK    = 'STATE_OPEN_BLOCK';
    const STATE_CLOSE_BLOCK   = 'STATE_CLOSE_BLOCK';
    
    /**
     * state / mode after affecting the tokenlist
     * @var string
     */
    private $state = '';

    /**
     * affect the tokenlist: determine own content
     * 
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $currentIndentation = 1;
        $currentIndent = $tokenList->getLineIndentationToken($this);
        if ($currentIndent !== null) {
            $currentIndentation = $currentIndent->getNestingLevel();
        }

        /**
         * close remaining open braces ?
         */
        $next = $tokenList->getNextNonWhitespace($this, false);
        if (!$next instanceof Token) {
            $ownIndex = $tokenList->getTokenIndex($this);
            while ($currentIndentation > 1) {
                $tokenList->injectToken(
                    IndentationToken::create($currentIndentation),
                    $ownIndex + 1
                );
                $tokenList->injectToken(
                    new PHPToken('T_CLOSE_BLOCK', PythoPhant_Grammar::T_CLOSE_BLOCK . PHP_EOL, $this->getLine()),
                    $ownIndex + 1
                );
                $currentIndentation--;
            }
            $this->setAuxValue(';');
            $this->state = self::STATE_LAST_LINE;
            return;
        }
        
        /*
         * blank line: no content
         */
        $previous = $tokenList->getAdjacentToken($this, -1, false);
        if ($previous instanceof NewLineToken || $previous instanceof IndentationToken) {
            $this->setAuxValue('');
            $this->state = self::STATE_EMTPY_LINE;
            return;
        }

        /*
         * prevent semicolon
         */
        if ($prev = $tokenList->getPreviousNonWhitespace($this)) {
            $preventSemicolon = $tokenList->isTokenIncluded(
                array($prev), PythoPhant_Grammar::$preventSemicolon
            );

            if ($preventSemicolon) {
                $this->setAuxValue("");
                $this->state = self::STATE_NO_SEMICOLON;
                return;
            }
        }

        /*
         * open or close blocks based on next line indentation
         */
        $nextIndentation = 1;
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList->getAdjacentToken($this, 1, false);
        if ($next instanceof IndentationToken) {
            $nextIndentation = $next->getNestingLevel();
        }
        if ($nextIndentation > $currentIndentation) {
            $tokenList->injectToken(
                new PHPToken('T_OPEN_BLOCK', PythoPhant_Grammar::T_OPEN_BLOCK, $this->line),
                $ownIndex + 1
            );
            $this->state = self::STATE_OPEN_BLOCK;
            return;
        } elseif ($nextIndentation < $currentIndentation) {
            $next = $tokenList->getNextNonWhitespace($this, false);
            $tokenList->injectToken(
                new PHPToken('T_CLOSE_BLOCK', PythoPhant_Grammar::T_CLOSE_BLOCK, $next->getLine()),
                $next
            );
            $this->state = self::STATE_CLOSE_BLOCK;
            return;
        }
        
        $this->setAuxValue(';');
        $this->state = self::STATE_REGULAR_LINE;
    }

    /**
     * returns PHP_EOL, preserves content before the first eol and appends the
     * auxval (in most cases ";") then appends other eols
     * 
     * @return string 
     */
    public function getContent()
    {
        $content = substr($this->content, 0, strpos($this->content, PHP_EOL));
        $content .= $this->auxValue;

        return $content
            . str_repeat(PHP_EOL, substr_count($this->content, PHP_EOL));
    }

    /**
     * create a newline token with just a PHP_EOL
     * 
     * @param int $line number of the sourcecode line
     * 
     * @return NewLineToken 
     */
    public static function createEmpty($line = 0)
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL, $line);
        $token->setAuxValue('');
        return $token;
    }

}