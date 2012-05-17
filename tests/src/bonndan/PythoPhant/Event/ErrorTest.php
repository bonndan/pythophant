<?php
namespace PythoPhant\Event;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * PythoPhant_Event_InfoTest
 * 
 */
class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new Error('test', __FILE__, 1);
        $this->assertEquals('test', $event->__ToString());
        $this->assertEquals(__FILE__, $event->getPath());
        $this->assertEquals(1, $event->getLine());
    }
}