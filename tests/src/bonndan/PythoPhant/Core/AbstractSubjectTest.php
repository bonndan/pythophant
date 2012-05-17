<?php
namespace PythoPhant\Core;

require_once dirname(__FILE__) . '/bootstrap.php';

use PythoPhant\Event\AbstractSubject;

/**
 * AbstractSubjectTest
 * 
 * 
 */
class AbstractSubjectTest extends \PHPUnit_Framework_TestCase
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
        $this->assertAttributeInstanceOf("\SplObjectStorage", 'observers', $this->subject);
    }
    
    public function testAttach()
    {
        $mock = $this->getMock("PythoPhant\Event\Observer");
        $this->subject->attach($mock);
        $this->assertAttributeContains($mock, 'observers', $this->subject);
    }
    
    public function testDetach()
    {
        $mock = $this->getMock("PythoPhant\Event\Observer");
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
        $mock = $this->getMock("PythoPhant\Event\Observer");
        $this->subject->attach($mock);
        
        $event = $this->getMock("PythoPhant\Event");
        $mock->expects($this->once())->method('update')->with($event);
        $this->subject->testNotification($event);
    }
}

class TestSubject extends AbstractSubject
{
    public function testNotification(Event $event)
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