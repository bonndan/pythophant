<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * IndentationTokenTest
 * 
 *  
 */
class IndentationTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider contentProvider
     * @param type $content 
     */
    public function testGetContent($content, $expected)
    {
        $token = new IndentationToken('T_INDENT', $content, 1);
        $this->assertEquals($expected, $token->getContent());
    }
    
    public function contentProvider()
    {
        return array(
            array('', ''),
            array('    ', '    '),
            array('    ' . PHP_EOL, ''),
            array('    '.PHP_EOL.PHP_EOL, ''),
            array('    '.PHP_EOL.PHP_EOL .'    ', '    '),
            array(PHP_EOL . '    '.PHP_EOL.PHP_EOL .'    ', '    '),
        );
    }
    
    /**
     * @dataProvider indentationProvider
     * @param type $content 
     */
    public function testGetNestingLevel($content, $expected)
    {
        $token = new IndentationToken('T_INDENT', $content, 1);
        $this->assertEquals($expected, $token->getNestingLevel());
    }
    
    public function indentationProvider()
    {
        return array(
            array('', 0),
            array('    ', 1),
            array('        ' , 2),
            array('    ' . PHP_EOL, 0),
            array(PHP_EOL . '    ', 1),
            array('    '.PHP_EOL.PHP_EOL, 0),
            array('    '.PHP_EOL.PHP_EOL .'    ', 1),
            array(PHP_EOL . '    '.PHP_EOL.PHP_EOL .'    ', 1),
            array(PHP_EOL . '    '.PHP_EOL.PHP_EOL .'        ', 2),
        );
    }
    
    public function testCreate()
    {
        $token = IndentationToken::create(2);
        $this->assertInstanceOf('IndentationToken', $token);
        $this->assertEquals('        ', $token->getContent());
    }
    
    public function testSetNestingLevel()
    {
        $token = IndentationToken::create(1);
        $token->setNestingLevel(2);
        
        $res = $token->getContent();
        $this->assertEquals(8, strlen($res));
    }
}