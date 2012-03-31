<?php
/**
 * PythoPhant_Event_Error
 * 
 * an error event
 *  
 */
class PythoPhant_Event_Error
implements PythoPhant_Event
{
    /**
     * message text
     * @var string 
     */
    private $message;
    
    /**
     * file / path where the error occurred
     * @var type 
     */
    private $path;
    
    /**
     * constructor requires message and path
     * 
     * @param string $message
     * @param string $path 
     */
    public function __construct($message, $path)
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
        return $this->message;
    }

}
