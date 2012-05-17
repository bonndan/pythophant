<?php
namespace PythoPhant;


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
        }

        /*
         * prevent semicolon
         */
        if ($prev = $tokenList->getPreviousNonWhitespace($this)) {
            $preventSemicolon = $tokenList->isTokenIncluded(
                array($prev), Grammar::$preventSemicolon
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

        if ($this->state == '') {
            $this->state = self::STATE_REGULAR_LINE;
            $this->setAuxValue(';');
        }
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
        /*
         * the last line in the block
         */
        $nextIndentation = $this->getNextIndentationLevel($tokenList);
        if ($nextIndentation === null) {
            $this->injectClosingBraces(
                $tokenList, $currentIndentation, $tokenList->getBaseIndentation()
            );
            $this->setContent(PHP_EOL);
            if (!$this->isLineEmpty($tokenList)) {
                $this->setAuxValue(';');
            }
            $this->state = self::STATE_LAST_LINE;
            return true;
        }

        /*
         * indentation difference
         */
        $ownIndex = $tokenList->getTokenIndex($this);
        if ($nextIndentation > $currentIndentation) {
            $tokenList->injectToken(
                new PHPToken('T_OPEN_BLOCK', ' ' . Grammar::T_OPEN_BLOCK, $this->line), $ownIndex
            );
            $this->state = self::STATE_OPEN_BLOCK;
            $this->setAuxValue('');
            return true;
        } elseif ($nextIndentation < $currentIndentation) {
            $this->injectClosingBraces($tokenList, $currentIndentation, $nextIndentation);
            $this->setAuxValue(';');
            return true;
        }

        return false;
    }

    /**
     * returns the indentation level of the next indentation token
     * 
     * @param TokenList $tokenList 
     * 
     * @return int|null
     */
    private function getNextIndentationLevel(TokenList $tokenList)
    {
        $ownIndex = $tokenList->getTokenIndex($this);
        for ($i = $ownIndex; $i < count($tokenList); $i++) {
            $token = $tokenList->offsetGet($i);
            if ($token instanceof IndentationToken) {
                return $token->getNestingLevel();
            }
        }

        return null;
    }

    /**
     * injects as many braces as needed behind this token
     * 
     * @param TokenList $tokenList
     * @param int       $currentIndentation
     * @param int       $targetIndentation 
     */
    private function injectClosingBraces(TokenList $tokenList, $currentIndentation, $targetIndentation)
    {
        $position = $tokenList->getTokenIndex($this);

        while ($currentIndentation > $targetIndentation) {
            $currentIndentation--;
            //brace
            $close = new PHPToken(
                'T_CLOSE_BLOCK',
                Grammar::T_CLOSE_BLOCK,
                $this->getLine()
            );
            $position = $position + 1;
            $tokenList->injectToken($close, $position);
            
            //indent brace one lever higher
            $indent = IndentationToken::create($currentIndentation, $this->line);
            $tokenList->injectToken($indent, $position);
            $position = $tokenList->getTokenIndex($close);
            
            $nl = new NewLineToken('T_NEWLINE', PHP_EOL, $this->line);
            $nl->setAuxValue('');
            $tokenList->injectToken($nl, $position + 1);
            $position = $tokenList->getTokenIndex($nl);
        }
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