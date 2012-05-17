<?php
namespace PythoPhant\Event;
/**
 * PythoPhant_Event_Error
 * 
 * an error event
 *  
 */
class Error implements Event
{
    /**
     * message text
     * @var string 
     */
    private $message;
    
    /**
     * file / path where the error occurred
     * @var string 
     */
    private $path;
    
    /**
     * line number where the error occurred
     * @var int 
     */
    private $line;
    
    /**
     * constructor requires message and path
     * 
     * @param string $message
     * @param string $path 
     * @param int    $line 
     */
    public function __construct($message, $path, $line = null)
    {
        $this->message = (string)$message;
        $this->path = $path;
        $this->line = $line;
    }
    
    /**
     * to string conversion
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * file and path of the error
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * line number of the error
     * 
     * @return int|null 
     */
    public function getLine()
    {
        return $this->line;
    }
}
