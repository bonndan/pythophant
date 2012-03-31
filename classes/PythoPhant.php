<?php

/**
 * PythoPhant
 * 
 * @package PythoPhant
 */
class PythoPhant
{
    /**
     * @var PythoPhant_Converter 
     */
    private $converter;
    
    /**
     * @var PythoPhant_DirectoryWatcher 
     */
    private $dirWatcher;
    
    /**
     * @var PythoPhant_Event_Proxy 
     */
    private $eventProxy;
    
    /**
     * creates a converter and directory watcher, makes them listen to the 
     * event proxy and vice versa
     * 
     */
    public function __construct()
    {
        $this->eventProxy = new PythoPhant_Event_Proxy();
        $this->dirWatcher = $this->getDirectoryWatcher();
        $this->converter = $this->getConverter();
        
        $this->converter->attach($this->eventProxy);
        $this->dirWatcher->attach($this->eventProxy);
        
        $this->eventProxy->attach($this->converter);
        $this->eventProxy->attach(new PythoPhant_ConsoleLoggerObserver());
    }
    
    /**
     * get a converter instance
     * 
     * @return PythoPhant_Converter 
     */
    private function getConverter()
    {
        $scanner  = new PythoPhant_Scanner($tokenFactory = new PythoPhant_TokenFactory());
        $parser   = new PythoPhant_Parser($tokenFactory);
        $renderer = new PythoPhant_Renderer();
        
        return new PythoPhant_Converter($scanner, $parser, $renderer);
    }
    
    /**
     * get a dir watcher instance
     * 
     * @return PythoPhant_DirectoryWatcher 
     */
    private function getDirectoryWatcher()
    {
        return new PythoPhant_DirectoryWatcher();
    }
    
    /**
     * convert a file
     * 
     * @param string $filename
     * @param bool   $debug 
     */
    public function convert($filename, $debug = false)
    {
        $this->converter->convert(new PythoPhant_SourceFile($filename), $debug);
    }
}