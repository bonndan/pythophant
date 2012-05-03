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
     * @var PythoPhant_RenderHelper
     */
    private $renderer;
    
    /**
     * inject the dependencies
     * 
     * @param Scanner             $scanner
     * @param Parser              $parser
     * @param PythoPhant_Renderer $renderer 
     */
    public function __construct(
        Scanner $scanner,
        Parser $parser,
        PythoPhant_RenderHelper $renderer
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
            $source = new PythoPhant_SourceFile(
                new SplFileObject($event->getPath())
            );
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
        try {
            $this->scanner->scanSource($contents);
        } catch (PythoPhant_Exception $exc) {
            $event = new PythoPhant_Event_Error(
                'Error scanning the source: ' .$exc->getMessage(),
                $source->getFilename(),
                $exc->getSourceLine()
            );
            return $this->notify($event);
        }
        $tokenList = $this->scanner->getTokenList();
        try {
            $this->parser->parseElement($tokenList);
        } catch (PythoPhant_Exception $exc) {
            $event = new PythoPhant_Event_Error(
                'Error parsing the token list: ' . $exc->getMessage(),
                $source->getFilename(),
                $exc->getSourceLine()
            );
            $this->notify($event);
            if (!$debug) {
                return false;
            }
        }
        
        $this->renderer->enableDebugging($debug);
        $this->renderer->setReflectionElement($this->parser->getElement());
        $date = date('Y/m/d H:i:s');
        $this->renderer->addWaterMark(
            'generated by PythoPhant on ' . $date 
            . ' from ' . $source->getFilename() 
            . ' #' . md5($contents)
        );
        $content = $this->renderer->getPHPSource();
        echo $content;
        return $source->writeTarget($content);
    }

}