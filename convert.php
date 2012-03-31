<?php

require_once dirname(__FILE__) . '/classes/autoload.php';

$filename = $argv[1];

$pp = new PythoPhant();
echo $pp->convert($filename);
