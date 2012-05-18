<?php
namespace PythoPhant\Event;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * PythoPhant_Event_ProxyTest
 * 
 */
class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Proxy 
     */
    private $proxy;
    
    public function setup()
    {
        parent::setup();
        $this->proxy = new Proxy();
    }
    
    public function testConstructor()
    {
        $this->assertAttributeInstanceOf('\SplObjectStorage', 'observers', $this->proxy);
    }
    
    public function testUpdate()
    {
        $mock = $this->getMock("PythoPhant\Event\Observer");
        $this->proxy->attach($mock);
        $event = $this->getMock("PythoPhant\Event\Event");
        
        $mock->expects($this->once())->method('update')->with($event);
        $this->proxy->update($event);
    }
    
    
    public function testAddLoggerWithFullClassname()
    {
        $res = $this->proxy->addLogger("PythoPhant\Event\Logger\Console");
        $this->assertInstanceOf("PythoPhant\Event\Observer", $res);
    }
    
    public function testAddLoggerWithObserver()
    {
        $res = $this->proxy->addLogger($this->getMock("PythoPhant\Event\Observer"));
        $this->assertInstanceOf("PythoPhant\Event\Observer", $res);
    }
    
    public function testAddLoggerUnloadable()
    {
        $this->setExpectedException("PythoPhant\Exception");
        $res = $this->proxy->addLogger("\PythoPhant\Unloadable");
    }
    
    public function testAddLoggerFails()
    {
        $this->setExpectedException("PythoPhant\Exception");
        $this->proxy->addLogger("\PythoPhant\TokenList");
    }
    
    public function testAddLoggerWithArray()
    {
        $this->setExpectedException("PythoPhant\Exception");
        $res = $this->proxy->addLogger(array());
    }
    
}