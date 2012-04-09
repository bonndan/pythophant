<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Exception.
 * 
 * 
 */
class PythoPhant_ExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetSourceCodeLine()
    {
        $exc = new PythoPhant_Exception('test', 12);
        $this->assertEquals(12, $exc->getSourceLine());
    }
}