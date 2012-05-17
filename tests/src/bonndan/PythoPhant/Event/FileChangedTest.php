<?php
namespace PythoPhant\Event;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * PythoPhant_Event_ProxyTest
 * 
 */
class FileChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new FileChanged(__FILE__);
        $this->assertAttributeEquals(__FILE__, 'path', $event);
    }
    
    public function testConstructorException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $event = new FileChanged('test');
    }
    
    public function testGetPath()
    {
        $event = new FileChanged(__FILE__);
        $this->assertEquals(__FILE__, $event->getPath());
    }
    
    public function testToString()
    {
        $event = new FileChanged(__FILE__);
        $this->assertContains(__FILE__, $event->__tostring());
    }
}