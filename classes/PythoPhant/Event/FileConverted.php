<?php
/**
 * PythoPhant_Event_FileConverted
 * 
 * notifies observers when a pp file has been converted to php
 * 
 *  
 */
class PythoPhant_Event_FileConverted
implements PythoPhant_Event
{
    /**
     * file path
     * @var string 
     */
    private $file;
    
    /**
     *
     * @param PythoPhant_SourceFile $file 
     */
    public function __construct(PythoPhant_SourceFile $file)
    {
        $this->file = $file;
    }
    
    /**
     * returns the path of the changed file
     * 
     * @return PythoPhant_SourceFile 
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
