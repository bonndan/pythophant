<?php
namespace PythoPhant\Core;

require_once dirname(__FILE__) . '/bootstrap.php';

use PythoPhant\Exception;

/**
 * Test class for PythoPhant_Exception.
 * 
 * 
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceCodeLine()
    {
        $exc = new Exception('test', 12);
        $this->assertEquals(12, $exc->getSourceLine());
    }
}