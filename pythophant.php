<?php
/**
 * PythoPhant invokation script
 * 
 *  
 */
require_once dirname(__FILE__) . '/classes/autoload.php';


$pp = new PythoPhant();
$pp->main($argv);
