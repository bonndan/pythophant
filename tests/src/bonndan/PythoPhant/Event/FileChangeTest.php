<?php
namespace PythoPhant\Event;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * FileChangedTest
 * 
 */
class FileChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $event = new FileChange(__FILE__);
        $this->assertAttributeEquals(__FILE__, 'path', $event);
    }
    
    public function testConstructorException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $event = new FileChange('test');
    }
    
    public function testGetPath()
    {
        $event = new FileChange(__FILE__);
        $this->assertEquals(__FILE__, $event->getPath());
    }
    
    public function testToString()
    {
        $event = new FileChange(__FILE__);
        $this->assertContains(__FILE__, $event->__tostring());
    }
}