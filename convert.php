<?php

require_once dirname(__FILE__) . '/classes/autoload.php';

$filename = $argv[1];
$scanner = new Scanner($tokenFactory = new TokenFactory());
$scanner->scanFile($filename);

$parser = new Parser($tokenFactory);
$parser->processTokenList($scanner->getTokenList());

$renderer = new Renderer($parser->getTokenList());
echo $renderer->getPHPSource();

