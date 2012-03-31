<?php

require_once dirname(__FILE__) . '/classes/autoload.php';


$watcher = 
    
$scanner  = new PythoPhant_Scanner($tokenFactory = new PythoPhant_TokenFactory());
$parser   = new PythoPhant_Parser($tokenFactory);
$renderer = new PythoPhant_Renderer();
$pp = new PythoPhant($scanner, $parser, $renderer);

$watcher->attach($pp);
$watcher->attach(new PythoPhant_ConsoleLoggerObserver());

$dirname = realpath($argv[1]);
$watcher->addDirectory($dirname);

$watcher->run(1000);
