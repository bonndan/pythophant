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

    const STATE_REGULAR_LINE = 'STATE_REGULAR_LINE';
    const STATE_EMTPY_LINE = 'STATE_EMTPY_LINE';
    const STATE_LAST_LINE = 'STATE_LAST_LINE';
    const STATE_NO_SEMICOLON = 'STATE_NO_SEMICOLON';
    const STATE_OPEN_BLOCK = 'STATE_OPEN_BLOCK';
    const STATE_CLOSE_BLOCK = 'STATE_CLOSE_BLOCK';

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
        /*
         * prevent multiple execution (because insert of open brace causes
         * endless loop) 
         */
        if ($this->state != '') {
            return;
        }

        $currentIndentation = 1;
        $currentIndent = $tokenList->getLineIndentationToken($this);
        if ($currentIndent !== null) {
            $currentIndentation = $currentIndent->getNestingLevel();
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
        if ($this->handleIndentationDifferences($tokenList, $currentIndentation)) {
            return;
        }

        $this->state = self::STATE_REGULAR_LINE;
        $this->setAuxValue(';');
    }

    /**
     * inserts curly braces, return true if a difference has been detected
     * 
     * @param TokenList $tokenList
     * @param int       $currentIndentation
     * 
     * @return boolean 
     */
    private function handleIndentationDifferences(TokenList $tokenList, $currentIndentation)
    {
        $nextIndentation = 2;
        $ownIndex = $tokenList->getTokenIndex($this);
        $next = $tokenList->getAdjacentToken($this, 1, false);
        /** skip one empty line @todo skip all empty lines*/
        if ($next instanceof NewLineToken && $next->isLineEmpty($tokenList)) {
            $next = $tokenList->getAdjacentToken($next, 1, false);
        }
        
        if ($next instanceof IndentationToken) {
            $nextIndentation = $next->getNestingLevel();
        }
        if ($nextIndentation > $currentIndentation) {
            $tokenList->injectToken(
                new PHPToken('T_OPEN_BLOCK', ' ' . PythoPhant_Grammar::T_OPEN_BLOCK, $this->line),
                $ownIndex
            );
            $this->state = self::STATE_OPEN_BLOCK;
            $this->setAuxValue('');
            return true;
        } elseif ($nextIndentation < $currentIndentation) {
            while ($currentIndentation > $nextIndentation) {
                $currentIndentation--;
                $this->injectTrailingClosingBrace($tokenList, $currentIndentation);
            }
            
            $this->setAuxValue(';');
            return true;
        }
        
        if ($next === null) {
            $this->setContent('');
            $this->setAuxValue(';');
            $this->state = self::STATE_LAST_LINE;
            return true;
        }

        return false;
    }

    /**
     * inject closing braces
     * 
     * @param TokenList $tokenList
     * @param int       $indentation 
     */
    private function injectTrailingClosingBrace(TokenList $tokenList, $indentation)
    {
        $next = $tokenList->getNextNonWhitespace($this, false);
        if ($next === null) {
            $position = $tokenList->getTokenIndex($this);
            $delete = $position + 1;
            while ($tokenList->offsetUnset($delete)) {
                echo  'delete ' . $delete .' ' ;
                $delete++;
            }
            $this->state = self::STATE_LAST_LINE;
            $this->setContent(PHP_EOL);
        } else {
            $this->state = self::STATE_CLOSE_BLOCK;
            $position = $tokenList->getTokenIndex($next) -1;
        }
        $next = $tokenList->getAdjacentToken($this, 1, false);
        if (!$next || !$next instanceof IndentationToken) {
            $tokenList->injectToken(
                IndentationToken::create($indentation, $this->line),
                $position +2 
            );
            $position++;
        }

        $tokenList->injectToken(
            new PHPToken('T_CLOSE_BLOCK', PythoPhant_Grammar::T_CLOSE_BLOCK . ' ', $this->getLine()),
            $position +1 
        );
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

    /**
     * check if the line if empty (or contains just whitespace)
     * 
     * @param TokenList $tokenList
     * 
     * @return boolean 
     */
    public function isLineEmpty(TokenList $tokenList)
    {
        $previous = $tokenList->getPreviousNonWhitespace($this);
        
        return is_null($previous);
    }
}