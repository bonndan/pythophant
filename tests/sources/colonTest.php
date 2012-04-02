<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * conversion test
 * 
 */
class ColonTest extends PHPUnit_Framework_TestCase
{
    public function testConversion()
    {
        $cwd = dirname(dirname(__DIR__));
        chdir($cwd);
        $file = $cwd . '/sources/colonTest.pp';
        
        ob_start();
        system('php pythophant.php ' . $file, $res);
        ob_end_clean();
        $this->assertEquals(0, $res);
    }
}