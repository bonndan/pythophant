<?php
/**
 * PythoPhant_Converter
 * 
 * converts pp files to php
 * 
 * 
 */
class PythoPhant_Converter
extends PythoPhant_AbstractSubject
implements PythoPhant_Observer, PythoPhant_Subject
{
    /**
     * source file scanner
     * @var Scanner
     */
    private $scanner;
    
    /**
     * token list parser
     * @var Parser
     */
    private $parser;
    
    /**
     * token list Renderer
     * @var Renderer
     */
    private $renderer;
    
    /**
     * inject the dependencies
     * 
     * @param Scanner  $scanner
     * @param Parser   $parser
     * @param Renderer $renderer 
     */
    public function __construct(
        Scanner $scanner,
        Parser $parser,
        Renderer $renderer
    ) {
        $this->scanner  = $scanner;
        $this->parser   = $parser;
        $this->renderer = $renderer;
        
        parent::__construct();
    }
    
    /**
     * receives fileChanged events and converts the related files
     * 
     * @param PythoPhant_Event $event 
     */
    public function update(PythoPhant_Event $event)
    {
        if ($event instanceof PythoPhant_Event_FileChanged) {
            $source = new PythoPhant_SourceFile($event->getPath());
            $this->convert($source);
        }
        
        return $this;
    }
    
    /**
     * convert a pp source file into php
     * 
     * @param PythoPhant_SourceFile $filename 
     * @param bool                  $debug 
     * 
     * @return string
     */
    public function convert(PythoPhant_SourceFile $source, $debug = false)
    {
        $contents = $source->getContents();
        $this->scanner->scanSource($contents);
        $tokenList = $this->scanner->getTokenList();
        $this->parser->processTokenList($tokenList);

        $this->renderer->enableDebugging($debug);
        $this->renderer->setTokenList($tokenList);
        $date = date('Y/m/d H:i:s');
        $this->renderer->addWaterMark(
            'generated by PythoPhant on ' . $date 
            . ' from ' . $source->getFilename() 
            . ' #' . md5($contents)
        );
        $content = $this->renderer->getPHPSource();
        $source->writeTarget($content);
        
        return $content;
    }

}