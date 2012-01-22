<?php

require_once dirname(__FILE__) . '/../../classes/Parser.php';

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
            array('    ', ''),
            array('    '.PHP_EOL.PHP_EOL, ''),
            array('    '.PHP_EOL.PHP_EOL .'    ', '    '),
            array(PHP_EOL . '    '.PHP_EOL.PHP_EOL .'    ', '    '),
        );
    }
}