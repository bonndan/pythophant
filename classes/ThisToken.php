<?php
/**
 * ThisToken
 * 
 * the dollar sign in prepended 
 */
class ThisToken extends CustomGenericToken
{
    /**
     * turns the "@" sign into "this->"
     * 
     * @return string 
     */
    public function setContent($content)
    {
        if ($content == PythoPhant_Grammar::T_THIS_MEMBER) {
            $content = 'this->';
        }
        parent::setContent($content);
    }
    
    /**
     * return the content and prepends the dollar sign
     * 
     * @return string 
     */
    public function getContent()
    {
        return '$' . $this->content;
    }
}
