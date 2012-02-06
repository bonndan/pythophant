<?php
/**
 * turns an @-char into "$this->"
 * 
 *  
 */
class ThisMemberToken extends CustomGenericToken
{
    /**
     * alway returns this->
     * 
     * @return string 
     */
    public function getContent()
    {
        return '$this->';
    }
}
