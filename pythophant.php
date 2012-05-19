<?php
namespace PythoPhant;
/**
 * PythoPhant invocation script
 * 
 *  
 */
require_once __DIR__ . '/vendor/autoload.php';

define('PATH_PYTHOPHANT', __DIR__);
define('PATH_PYTHOPHANT_MACROS', PATH_PYTHOPHANT . DIRECTORY_SEPARATOR . 'macros');

$pp = new Application();
$res = $pp->main($argv);
if (!$res) {
    exit(1);
}
