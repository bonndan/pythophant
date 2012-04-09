<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * PythoPhant_Event_InfoTest
 * 
 */
class PythoPhant_Event_ErrorTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new PythoPhant_Event_Error('test', __FILE__, 1);
        $this->assertEquals('test', $event->__ToString());
        $this->assertEquals(__FILE__, $event->getPath());
        $this->assertEquals(1, $event->getLine());
    }
}