<?php
/**
 * PythoPhant invocation script
 * 
 *  
 */
require_once dirname(__FILE__) . '/classes/autoload.php';

define('PATH_PYTHOPHANT', __DIR__);
define('PATH_PYTHOPHANT_MACROS', PATH_PYTHOPHANT . DIRECTORY_SEPARATOR . 'macros');

$pp = new PythoPhant();
$res = $pp->main($argv);
if (!$res) {
    exit(1);
}
