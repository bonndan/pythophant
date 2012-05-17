<?php
namespace PythoPhant\Event\Logger;

require_once dirname(__FILE__) . '/bootstrap.php';
/**
 * ConsoleTest
 * 
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testEventMessageIsPrinted()
    {
        $logger = new Console();
        $event = $this->getMock("PythoPhant\Event\Event");
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