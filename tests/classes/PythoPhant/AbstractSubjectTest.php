<?php
require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * PythoPhant_AbstractSubjectTest
 * 
 * 
 */
class PythoPhant_AbstractSubjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestSubject 
     */
    private $subject;
    
    public function setup()
    {
        parent::setUp();
        $this->subject = new TestSubject();
    }
    
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('SplObjectStorage', 'observers', $this->subject);
    }
    
    public function testAttach()
    {
        $mock = $this->getMock('PythoPhant_Observer');
        $this->subject->attach($mock);
        $this->assertAttributeContains($mock, 'observers', $this->subject);
    }
    
    public function testDetach()
    {
        $mock = $this->getMock('PythoPhant_Observer');
        $this->assertFalse(
            $this->subject->getObservers()->contains($mock)
        );
        $this->subject->attach($mock);
        $this->assertTrue(
            $this->subject->getObservers()->contains($mock)
        );
        $this->subject->detach($mock);
        $this->assertFalse(
            $this->subject->getObservers()->contains($mock)
        );
        $this->assertAttributeNotContains($mock, 'observers', $this->subject);
    }
    
    public function testNotification()
    {
        $mock = $this->getMock('PythoPhant_Observer');
        $this->subject->attach($mock);
        
        $event = $this->getMock('PythoPhant_Event');
        $mock->expects($this->once())->method('update')->with($event);
        $this->subject->testNotification($event);
    }
}

class TestSubject extends PythoPhant_AbstractSubject
{
    public function testNotification(PythoPhant_Event $event)
    {
        $this->notify($event);
    }
    
    /**
     * get the observer storage
     * @return SplObjectStorage 
     */
    public function getObservers()
    {
        return parent::getObservers();
    }
}