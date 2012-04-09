<?php

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * PythoPhant_Event_ProxyTest
 * 
 */
class PythoPhant_Event_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PythoPhant_Event_Proxy 
     */
    private $proxy;
    
    public function setup()
    {
        parent::setup();
        $this->proxy = new PythoPhant_Event_Proxy();
    }
    
    public function testConstructor()
    {
        $this->assertAttributeInstanceOf('SplObjectStorage', 'observers', $this->proxy);
    }
    
    public function testUpdate()
    {
        $mock = $this->getMock('PythoPhant_Observer');
        $this->proxy->attach($mock);
        $event = $this->getMock('PythoPhant_Event');
        
        $mock->expects($this->once())->method('update')->with($event);
        $this->proxy->update($event);
    }
    
    
    public function testAddLoggerWithFullClassname()
    {
        $res = $this->proxy->addLogger('PythoPhant_Logger_Console');
        $this->assertInstanceOf('PythoPhant_Observer', $res);
    }
    
    public function testAddLoggerWithAbbrevClassname()
    {
        $res = $this->proxy->addLogger('Console');
        $this->assertInstanceOf('PythoPhant_Observer', $res);
    }
    
    public function testAddLoggerWithObserver()
    {
        $res = $this->proxy->addLogger($this->getMock('PythoPhant_Observer'));
        $this->assertInstanceOf('PythoPhant_Observer', $res);
    }
    
    public function testAddLoggerWithOtherInstance()
    {
        $observer = $this->getMock('PythoPhant_Observer');
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