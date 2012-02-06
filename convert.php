<?php

require_once dirname(__FILE__) . '/classes/autoload.php';

$filename = $argv[1];
$source = new SourceFile($filename);
$scanner = new Scanner($tokenFactory = new TokenFactory());
$scanner->scanSource($source->getContents());

$parser = new Parser($tokenFactory);
$parser->processTokenList($scanner->getTokenList());

$renderer = new Renderer($parser->getTokenList());

$content = $renderer->getPHPSource();
    
$source->writeTarget($content);
echo $content;
