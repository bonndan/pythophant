<?php

class SourceFile
{
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
     * @param string $filename 
     * 
     * @throws PPException
     */
    public function __construct($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new PPException('The file is not accessible');
        }
        $this->dirname  = dirname($filename);
        $this->filename = basename($filename, self::EXT);
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
     * write php source to a target
     * 
     * @param type $source 
     */
    public function writeTarget($source)
    {
        $targetFilename = $this->dirname . DIRECTORY_SEPARATOR . $this->filename . 'php';
        file_put_contents($targetFilename, $source);
        exec('php -l ' . $targetFilename, $output, $returnVal);
        
        if ($returnVal) {
            echo $source; 
        }
    }
}
