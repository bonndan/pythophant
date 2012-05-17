<?php
namespace PythoPhant\Core;

/**
 * SourceFile
 * 
 * represents a pp source file
 */
class SourceFile
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
     * source file
     * @var SplFileObject
     */
    private $file = null;
    
    /**
     * line number (php target file) where the last error occurred
     * @var int|null
     */
    private $errorLine = null;
    
    /**
     * pass a filename
     * 
     * @param SplFileObject $file
     * 
     * @throws PythoPhant_Exception
     */
    public function __construct(\SplFileObject $file = null)
    {
        if ($file instanceof \SplFileObject) {
            $this->setFile($file);
        }
    }
    
    /**
     * set the source file
     * 
     * @param SplFileObject $file
     * 
     * @throws \PythoPhant\Exception
     */
    public function setFile(\SplFileObject $file)
    {
        if (!$file->isReadable()) {
            throw new \PythoPhant\Exception('The file is not accessible');
        }
        
        $this->dirname  = $file->getPath();
        $this->filename = $file->getBasename(self::EXT);
        $this->file     = $file;
    }
    
    /**
     * reads the file contents
     * 
     * @return string file contents
     */
    public function getContents()
    {
        return file_get_contents($this->file->getPathname());
    }
    
    /**
     * write php source to a target, lint the produced file
     * 
     * @param string $content     php content
     * @param string $destination optional file destination path
     * 
     * @return boolean success status
     */
    public function writeTarget($content, $destination = null)
    {
        if ($destination === null) {
            $destination = $this->dirname . DIRECTORY_SEPARATOR . $this->filename . 'php';
        }
        
        file_put_contents($destination, $content);
        
        ob_start();
        passthru('php -l ' . $destination . ' 2>&1', $returnVal);
        $output = ob_get_contents();
        ob_end_clean();
        
        if ($returnVal) {
            //$output = implode('', $output);
            preg_match('/on\sline\s([0-9]+)/i', $output, $matches);
            if (isset($matches[1])) {
                $this->errorLine = $matches[1];
            } else {
                $this->errorLine = null;
            }
            return false;
        } else {
            return true;
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
    
    /**
     * get the line number of the last lint error
     * 
     * @return int|null 
     */
    public function getErrorLine()
    {
        return $this->errorLine;
    }
}
