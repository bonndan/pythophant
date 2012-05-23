<?php
namespace PythoPhant\Event;

use PythoPhant\Core\SourceFile;

/**
 * FileConverted
 * 
 * notifies observers when a pp file has been converted to php
 * 
 *  
 */
class FileConverted implements \PythoPhant\Event\Event
{
    /**
     * file path
     * @var string 
     */
    private $file;
    
    /**
     * pass an instance of a source file
     * 
     * @param SourceFile $file 
     */
    public function __construct(SourceFile $file)
    {
        $this->file = $file;
    }
    
    /**
     * returns the path of the changed file
     * 
     * @return SourceFile 
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * to string conversion
     * 
     * @return string 
     */
    public function __toString()
    {
        return 'File has been converted: ' . $this->file->getFilename();
    }

}
