<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * PythoPhant_Event_InfoTest
 * 
 */
class PythoPhant_Event_InfoTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new PythoPhant_Event_Info('test', __FILE__);
        $this->assertEquals('test ' . __FILE__, $event->__ToString());
    }
}