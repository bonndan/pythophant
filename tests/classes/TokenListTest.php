<?php

require_once dirname(__FILE__) . '/../../classes/TokenList.php';

/**
 * Test class for TokenList.
 * Generated by PHPUnit on 2012-01-23 at 20:09:33.
 */
class TokenListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TokenList
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TokenList;
    }

    private function getTokenMock()
    {
        return $this->getMockBuilder('Token')
            ->disableOriginalConstructor()
            ->getMock();
    }
    /**
     * testPushToken().
     */
    public function testPushToken()
    {
        $tokenMock = $this->getTokenMock();
        $this->assertFalse($this->object->offsetExists(0));
        
        $this->object->pushToken($tokenMock);
        $this->assertTrue($this->object->offsetExists(0));
        $this->assertEquals($tokenMock, $this->object->offsetGet(0));
    }

    /**
     * @todo Implement testInjectToken().
     */
    public function testInjectToken()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetTokenIndex().
     */
    public function testGetTokenIndex()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetNextNonWhitespace().
     */
    public function testGetNextNonWhitespace1()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $this->object->pushToken($token);
        $w = new PHPToken('T_WHITESPACE', "whitespace", 1);
        $this->object->pushToken($w);
        $string = new StringToken('T_STRING', "needle", 1);
        $this->object->pushToken($string);
        
        $res = $this->object->getNextNonWhitespace($token);
        $this->assertEquals($string, $res);
    }
    
    /**
     */
    public function testGetNextNonWhitespaceNull()
    {
        $token = new StringToken('T_STRING', 'my', 1);
        $this->object->pushToken($token);
        $w = new NewLineToken('T_NEWLINE', PHP_EOL, 1);
        $this->object->pushToken($w);
        $string = new StringToken('T_STRING', "needle", 1);
        $this->object->pushToken($string);
        
        $res = $this->object->getNextNonWhitespace($token);
        $this->assertNull($res);
    }

    /**
     */
    public function testGetPreviousNonWhitespace()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRewind().
     */
    public function testRewind()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCurrent().
     */
    public function testCurrent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testKey().
     */
    public function testKey()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testNext().
     */
    public function testNext()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValid().
     */
    public function testValid()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCount().
     */
    public function testCount()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetExists().
     */
    public function testOffsetExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetGet().
     */
    public function testOffsetGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetSet().
     */
    public function testOffsetSet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOffsetUnset().
     */
    public function testOffsetUnset()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

