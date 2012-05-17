<?php
namespace PythoPhant\Event;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * PythoPhant_Event_InfoTest
 * 
 */
class InfoTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new Info('test', __FILE__);
        $this->assertEquals('test ' . __FILE__, $event->__ToString());
    }
}