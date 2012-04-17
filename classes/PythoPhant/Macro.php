<?php
/**
 * PythoPhant_Macro
 * 
 * 
 */
class PythoPhant_Macro implements Macro
{    
    /**
     * raw sources
     * @var string  
     */
    private $source;
    
    /**
     * params containing values to replace in source
     * @var array
     */
    private $params = array();
    
    /**
     * construct with a file and have its contents used as source
     * 
     * @param SplFileObject $file 
     */
    public function __construct(SplFileObject $file = null)
    {
        if ($file instanceof SplFileObject) {
            $this->setSource(file_get_contents($file->getPathname()));
        }
    }
    
    /**
     * set the raw, unprocessed source
     * 
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = (string)$source;
    }
    
    /**
     * set the values which will be replaced in the source
     * 
     * @param array $params 
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    /**
     * scans the source and returns a string
     * 
     * @return string 
     */
    public function getSource()
    {
        return vsprintf($this->source, $this->params);
    }
}
