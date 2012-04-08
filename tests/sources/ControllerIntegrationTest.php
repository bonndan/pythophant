<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * conversion test
 * 
 */
class ControllerIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function testConversion()
    {
        $cwd = dirname(dirname(__DIR__));
        chdir($cwd);
        $file = $cwd . '/sources/Controller.pp';
        
        ob_start();
        passthru('php pythophant.php ' . $file, $res);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(0, $res, $output);
    }
}