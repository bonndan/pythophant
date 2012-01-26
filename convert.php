<?php

require_once dirname(__FILE__) . '/classes/autoload.php';

$filename = dirname(__FILE__) . '/sources/newEmptyPHP.php';
$scanner = new Scanner($tokenFactory = new TokenFactory());
$scanner->scanFile($filename);

$parser = new Parser($tokenFactory);
$parser->processTokenList($scanner->getTokenList());

new Renderer($parser->getTokenList());

