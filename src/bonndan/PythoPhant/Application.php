<?php
namespace PythoPhant;

use PythoPhant\Event\Proxy;
use PythoPhant\Event\Info;

use PythoPhant\Core\DirectoryWatcher;
use PythoPhant\Core\TokenFactoryScanner;
use PythoPhant\Core\Converter;
use PythoPhant\Core\Scanner;
use PythoPhant\Core\Parser;
use PythoPhant\Core\ReflectionParser;
use PythoPhant\Core\TokenFactory;
use PythoPhant\Core\RegisteredTokenFactory;
use PythoPhant\Core\Project;

use PythoPhant\Renderer\Helper as RenderHelper;

/**
 * Application
 * 
 * main class
 * 
 * @package PythoPhant
 */
class Application
{

    /**
     * @var PythoPhant\Core\Converter 
     */
    private $converter;

    /**
     * @var PythoPhant\Core\DirectoryWatcher 
     */
    private $dirWatcher;

    /**
     * @var PythoPhant\Event\Proxy 
     */
    private $eventProxy;

    /**
     * @var Project 
     */
    private $project;

    /**
     * creates a converter and directory watcher if not passed, makes them 
     * listen to the event proxy and vice versa
     * 
     * @param Converter        $converter
     * @param DirectoryWatcher $watcher
     * @param Proxy      $proxy 
     */
    public function __construct(
        Converter $converter = null,
        DirectoryWatcher $watcher = null,
        Proxy $proxy = null
    ) {
        $this->eventProxy = is_object($proxy) ? $proxy : new Proxy();
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
        $scanner = new TokenFactoryScanner(new RegisteredTokenFactory());
        $parser = new ReflectionParser();
        $renderer = new RenderHelper();

        return new Converter($scanner, $parser, $renderer);
    }

    /**
     * get a dir watcher instance
     * 
     * @return PythoPhant_DirectoryWatcher 
     */
    private function getDirectoryWatcher()
    {
        return new DirectoryWatcher();
    }

    /**
     * if pythophant is run with a file as param, it is converted
     * 
     * @param array $args 
     */
    public function main(array $args)
    {
        $this->project = new Project();

        $configFound = false;
        try {
            if ($this->project->readConfigurationFile()) {
                $configFound = true;

                foreach ($this->project->getLoggers() as $logger) {
                    $this->eventProxy->addLogger($logger);
                }
            } else {
                $this->eventProxy->addLogger(new PythoPhant\Core\Logger\Console());
            }
        } catch (\InvalidArgumentException $exc) {
            $this->eventProxy->update(new Info($exc->getMessage()));
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
        $file = new \SplFileObject($filename);
        return $this->converter->convert(new SourceFile($file), $debug);
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