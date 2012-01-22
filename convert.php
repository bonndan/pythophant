<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/Token.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/TokenFactory.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/Scanner.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/Parser.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/Renderer.php';

$filename = dirname(__FILE__) . '/sources/newEmptyPHP.php';
$scanner = new Scanner($tokenFactory = new TokenFactory());
$scanner->scanFile($filename);

$parser = new Parser($tokenFactory);
$parser->processTokenList($scanner->getTokenList());

new Renderer($parser->getTokenList());

