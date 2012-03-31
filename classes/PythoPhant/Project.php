<?php
/**
 * PythoPhant_Project
 * 
 * 
 */
class PythoPhant_Project
{
    const DEFAULT_CONFIG_FILE = '.pythophant.json';
    /**
     * name of the project
     * @var type 
     */
    private $project = '';
    /**
     * directories to watch
     * @var array
     */
    private $watch = array('.');
    
    /**
     * @var boolean 
     */
    private $testAfterConversion = true;

    /**
     * read the project config json file
     * 
     * @param string $filename 
     */
    public function readConfigurationFile($filename = self::DEFAULT_CONFIG_FILE)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new InvalidArgumentException('Config file not accessible: '. $filename);
        }
        
        $config = json_decode(file_get_contents($filename));
        $this->readConfiguration($config);
    }
    
    /**
     * read the project config json file
     * 
     * @param stdClass $filename 
     */
    public function readConfiguration($config)
    {
        if (is_string($config)) {
            $config = json_decode($config);
        }
        if (!is_object($config)) {
            return;
        }
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
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
     * return whether converted files
     * @return bool 
     */
    public function getTestAfterConversion()
    {
        return (bool)$this->testAfterConversion;
    }
}