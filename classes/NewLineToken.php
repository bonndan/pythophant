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
     * returns PHP_EOL
     * 
     * @return string 
     */
    public function getContent()
    {
        $firstNL = strpos($this->content, PHP_EOL);
        $content = substr($this->content, 0, $firstNL);
        $content .= $this->auxValue;

        return $content
            . str_repeat(PHP_EOL, substr_count($this->content, PHP_EOL));
    }

    /**
     * create a newline token with just a PHP_EOL
     * 
     * @return NewLineToken 
     */
    public static function createEmpty()
    {
        $token = new NewLineToken(Token::T_NEWLINE, PHP_EOL, 0);
        $token->setAuxValue('');
        return $token;
    }
}