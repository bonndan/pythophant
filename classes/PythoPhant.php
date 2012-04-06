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
     * @var PythoPhant_Project 
     */
    private $project;

    /**
     * creates a converter and directory watcher if not passed, makes them 
     * listen to the event proxy and vice versa
     * 
     * @param PythoPhant_Converter        $converter
     * @param PythoPhant_DirectoryWatcher $watcher
     * @param PythoPhant_Event_Proxy      $proxy 
     */
    public function __construct(
        PythoPhant_Converter $converter = null,
        PythoPhant_DirectoryWatcher $watcher = null,
        PythoPhant_Event_Proxy $proxy = null
    )
    {
        $this->eventProxy = is_object($proxy) ? $proxy : new PythoPhant_Event_Proxy();
        $this->dirWatcher = is_object($watcher) ? $watcher : $this->getDirectoryWatcher();
        $this->converter = is_object($converter) ? $converter : $this->getConverter();

        $this->converter->attach($this->eventProxy);
        $this->dirWatcher->attach($this->eventProxy);

        $this->eventProxy->attach($this->converter);
    }

    /**
     * get a converter instance
     * 
     * @return PythoPhant_Converter 
     */
    private function getConverter()
    {
        $scanner = new PythoPhant_Scanner($tokenFactory = new PythoPhant_TokenFactory());
        $parser = new PythoPhant_Parser($tokenFactory);
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
     * if pythophant is run with a file as param, it is converted
     * 
     * @param array $args 
     */
    public function main(array $args)
    {
        $this->project = new PythoPhant_Project();

        $configFound = false;
        try {
            if ($this->project->readConfigurationFile()) {
                $configFound = true;

                foreach ($this->project->getLoggers() as $logger) {
                    $this->eventProxy->addLogger($logger);
                }
            } else {
                $this->eventProxy->addLogger(new PythoPhant_Logger_Console());
            }
        } catch (InvalidArgumentException $exc) {
            $this->eventProxy->update(new PythoPhant_Event_Info($exc->getMessage()));
        }

        /**
         * file param? 
         */
        $file = isset($args[1]) ? $args[1] : null;
        $debug = isset($args[2]) ? (bool)$args[2] : null;
        if ($file != null && is_file($file)) {
            return $this->convert($file, $debug);
        }

        /**
         * dir param or start watching all dirs from config
         */
        $dir = isset($args[1]) ? $args[1] : null;
        if (is_dir($dir)) {
            $this->dirWatcher->addDirectory($dir);
        } elseif ($configFound) {
            foreach ($this->project->getWatchedDirectories() as $dir) {
                $this->dirWatcher->addDirectory($dir);
            }
        }
        /**
         * fallback: start watching cwd 
         */
        elseif ($configFound == false) {
            $this->dirWatcher->addDirectory(getcwd());
        }
        
        $this->dirWatcher->run($this->project->getPollingInterval());
    }

    /**
     * convert a file
     * 
     * @param string $filename
     * @param bool   $debug 
     * 
     * @throws RuntimeException
     */
    public function convert($filename, $debug = false)
    {
        $file = new SplFileObject($filename);
        return $this->converter->convert(new PythoPhant_SourceFile($file), $debug);
    }

    /**
     * returns the current project (if main was called)
     * 
     * @return PythoPhant_Project|null
     */
    public function getProject()
    {
        return $this->project;
    }

}