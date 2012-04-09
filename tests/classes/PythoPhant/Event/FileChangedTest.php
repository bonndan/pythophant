<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * PythoPhant_Event_ProxyTest
 * 
 */
class PythoPhant_Event_FileChangedTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new PythoPhant_Event_FileChanged(__FILE__);
        $this->assertAttributeEquals(__FILE__, 'path', $event);
    }
    
    public function testConstructorException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $event = new PythoPhant_Event_FileChanged('test');
    }
    
    public function testGetPath()
    {
        $event = new PythoPhant_Event_FileChanged(__FILE__);
        $this->assertEquals(__FILE__, $event->getPath());
    }
    
    public function testToString()
    {
        $event = new PythoPhant_Event_FileChanged(__FILE__);
        $this->assertContains(__FILE__, $event->__tostring());
    }
}