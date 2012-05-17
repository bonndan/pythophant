<?php
namespace PythoPhant\Event;

/**
 * PythoPhant_Event_FileChanged
 * 
 * notifies observers when a file has changed
 * 
 * @see PythoPhant\Core\DirectoryWatcher
 */
class FileChanged implements Event
{
    /**
     * file path
     * @var string 
     */
    private $path;
    
    /**
     * constructor requires a valid path
     * 
     * @param string $path 
     * 
     * @throws InvalidArgumentException
     */
    public function __construct($path)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('Not a file: ' . $path);
        }
        $this->path = $path;
    }
    
    /**
     * returns the path of the changed file
     * 
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * to string conversion
     * 
     * @return string 
     */
    public function __toString()
    {
        return 'File has changed: ' . $this->path;
    }

}
