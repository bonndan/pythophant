<?php

require_once dirname(__FILE__) . '/classes/autoload.php';

$filename = $argv[1];

$scanner  = new PythoPhant_Scanner($tokenFactory = new PythoPhant_TokenFactory());
$parser   = new PythoPhant_Parser($tokenFactory);
$renderer = new PythoPhant_Renderer();
        
$pp = new PythoPhant($scanner, $parser, $renderer);
echo $pp->convert($filename);
