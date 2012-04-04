<?php
/**
 * PyhtoPhant_SourceFile
 */
class PythoPhant_SourceFile
{
    /**
     * source file suffix
     * @var string 
     */
    const EXT = 'pp';
    
    /**
     * directory
     * @var string 
     */
    private $dirname = '';
    
    /**
     * filename without extension
     * @var string 
     */
    private $filename = '';
    
    /**
     * pass a filename
     * 
     * @param SplFileObject $file
     * 
     * @throws PythoPhant_Exception
     */
    public function __construct(SplFileObject $file)
    {
        if (!$file->isReadable()) {
            throw new PythoPhant_Exception('The file is not accessible');
        }
        
        $this->dirname  = $file->getPath();
        $this->filename = $file->getBasename(self::EXT);
    }
    
    /**
     * reads the file contents
     * 
     * @return string file contents
     */
    public function getContents()
    {
        return file_get_contents(
            $this->dirname . DIRECTORY_SEPARATOR . $this->filename . self::EXT
        );
    }
    
    /**
     * write php source to a target, lint the produced file while discarding 
     * 
     * @param type $source 
     */
    public function writeTarget($source)
    {
        $targetFilename = $this->dirname . DIRECTORY_SEPARATOR . $this->filename . 'php';
        file_put_contents($targetFilename, $source);
        exec('php -l ' . $targetFilename, $output, $returnVal);
        
        if ($returnVal) {
            echo $output; 
        }
    }
    
    /**
     * returns the path and file name of the source file
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
