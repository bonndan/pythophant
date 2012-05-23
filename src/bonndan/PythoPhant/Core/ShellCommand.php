<?php
namespace PythoPhant\Core;

/**
 * ShellCommand
 */
class ShellCommand
{
    /**
     * command
     * @var string|null 
     */
    private $command = null;
    /**
     * output
     * @var string|null 
     */
    private $ouput = null;
    
    /**
     * factory method
     * 
     * @param string $command
     * 
     * @return \PythoPhant\Core\ShellCommand 
     */
    public static function createWith($command)
    {
        $shellCommand = new self((string)$command);
        return $shellCommand;
    }
    
    /**
     * init with a command
     * 
     * @param string $command 
     */
    public function __construct($command)
    {
        $this->command = $command;
    }
    
    /**
     * returns the return code
     * 
     * @return int
     */
    public function execute()
    {
        $returnVal = null;
        ob_start();
        passthru($this->command, $returnVal);
        $this->output = ob_get_contents();
        ob_end_clean();
        
        return $returnVal;
    }
    
    /**
     * returns the script output
     * 
     * @return string|null 
     */
    public function getOutput()
    {
        return $this->output;
    }
}