<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 *
 * 
 *  
 */
class MemberTokenTest extends PHPUnit_Framework_TestCase
{
    public function testGetContent()
    {
        $token = new MemberToken('T_MEMBER', '.', 1);
        $this->assertEquals('->', $token->getContent());
    }
}
