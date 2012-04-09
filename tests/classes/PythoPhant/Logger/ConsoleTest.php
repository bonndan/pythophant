<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * PythoPhant_Event_ProxyTest
 * 
 */
class PythoPhant_Logger_ConsoleTest extends PHPUnit_Framework_TestCase
{
    public function testEventMessageIsPrinted()
    {
        $logger = new PythoPhant_Logger_Console();
        $event = $this->getMock('PythoPhant_Event');
        $event->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('test'));
        
        ob_start();
        $logger->update($event);
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('test', trim($res));
    }
}