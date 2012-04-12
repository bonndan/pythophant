<?php
/**
 * Token representing the last token of a line. Can contain ; or { or } or nothing 
 */
class NewLineToken extends CustomGenericToken
{
    /**
     * last character of a line
     * @var string 
     */
    protected $auxValue = ';';

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