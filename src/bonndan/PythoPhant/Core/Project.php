<?php
namespace PythoPhant\Core;

/**
 * PythoPhant_Project
 * 
 * 
 */
class Project
{
    const DEFAULT_CONFIG_FILE = 'pythophant.json';
    /**
     * name of the project
     * @var type 
     */
    private $project = '';
    /**
     * directories to watch
     * @var array
     */
    private $watch = array();
    
    /**
     * @var boolean 
     */
    private $testAfterConversion = true;
    
    /**
     * observer-loggers
     * @var array
     */
    private $loggers = array();

    /**
     * interval between directory watcher runs
     * @var int 
     */
    private $pollingInterval = 1000;
    
    /**
     * read the project config json file
     * 
     * @param string $filename 
     * 
     * @throws InvalidArgumentException
     * @return boolean
     */
    public function readConfigurationFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \InvalidArgumentException('Config file not accessible: '. $filename);
        }
        
        $config = json_decode(file_get_contents($filename));
        return $this->readConfiguration($config);
    }
    
    /**
     * read the project config json file
     * 
     * @param stdClass|string $config 
     * 
     * @return boolean
     * @throws \RuntimeException
     */
    public function readConfiguration($config)
    {
        if (is_string($config)) {
            $config = json_decode($config);
        }
        if (!is_object($config)) {
            throw new \RuntimeException('Config broken: ' . serialize($config));
        }
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
        
        return true;
    }
    
    /**
     * returns the project name
     * 
     * @return string 
     */
    public function getProjectName()
    {
        return (string)$this->project;
    }
    
    /**
     * get the directories which have to be watched
     * 
     * @return array 
     */
    public function getWatchedDirectories()
    {
        if (!is_array($this->watch)) {
            return array();
        }
        
        return $this->watch;
    }
    
    /**
     * get loggers
     * 
     * @return array 
     */
    public function getLoggers()
    {
        if (!is_array($this->loggers)) {
            return array();
        }
        
        return $this->loggers;
    }
    
    /**
     * return whether converted files
     * @return bool 
     */
    public function getTestAfterConversion()
    {
        return (bool)$this->testAfterConversion;
    }
    
    /**
     * interval between directory watcher runs
     * 
     * @return int
     */
    public function getPollingInterval()
    {
        return $this->pollingInterval;
    }
}