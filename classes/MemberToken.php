<?php
/**
 * the content of this always T_OBJECT_OPERATOR 
 */
class MemberToken extends CustomGenericToken
{
    /**
     * T_OBJECT_OPERATOR
     * @return string 
     */
    public function getContent()
    {
        return "->";
    }
}
