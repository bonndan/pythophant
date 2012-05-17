<?php
namespace PythoPhant;

/**
 * ThisToken
 * 
 * the dollar sign is prepended, "@" translated to "this->"
 */
class ThisToken extends CustomGenericToken
{
    /**
     * turns the "@" sign into "this->"
     * 
     * @return ThisToken 
     */
    public function setContent($content)
    {
        if ($content == Grammar::T_THIS_MEMBER) {
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
