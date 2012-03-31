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
}