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
     * @var PythoPhant_Event_Proxy 
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
        $res = $this->proxy->addLogger("PythoPhant\Logger\Console");
        $this->assertInstanceOf("PythoPhant\Event\Observer", $res);
    }
    
    public function testAddLoggerWithAbbrevClassname()
    {
        $res = $this->proxy->addLogger('Console');
        $this->assertInstanceOf("PythoPhant\Event\Observer", $res);
    }
    
    public function testAddLoggerWithObserver()
    {
        $res = $this->proxy->addLogger($this->getMock("PythoPhant\Observer"));
        $this->assertInstanceOf("PythoPhant\Event\Observer", $res);
    }
    
    public function testAddLoggerWithOtherInstance()
    {
        $observer = $this->getMock("PythoPhant\Event\Observer");
        $observer->expects($this->once())
            ->method('update');
        $this->proxy->attach($observer);
        
        $res = $this->proxy->addLogger('TokenList');
        
        $this->assertNull($res);
    }
    
    public function testAddLoggerWithArray()
    {
        $res = $this->proxy->addLogger(array());
        $this->assertNull($res);
    }
}