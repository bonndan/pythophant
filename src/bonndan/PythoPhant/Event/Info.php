<?php
namespace PythoPhant\Event;

/**
 * Info
 * 
 * an info / debug event
 *  
 */
class Info implements Event
{
    /**
     * message text
     * @var string 
     */
    private $message;
    
    /**
     * file / path where the event occurred
     * @var type 
     */
    private $path;
    
    /**
     * cosntruct with a message
     * 
     * @param string $message
     * @param string $path 
     */
    public function __construct($message, $path = null)
    {
        $this->message = (string)$message;
        $this->path = $path;
    }
    
    /**
     * to string conversion
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->message . ' ' . $this->path;
    }

}
