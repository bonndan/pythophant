<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test for the Macro
 * 
 *  
 */
class PythoPhant_MacroTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PythoPhant_Macro 
     */
    private $macro;
        
    public function setup()
    {
        parent::setUp();
        $this->macro = new PythoPhant_Macro();
    }
    
    public function testConstructor()
    {
        $this->assertInstanceOf('Macro', $this->macro);
    }
    
    public function testConstructorWithSplFileObject()
    {
        $filepath = dirname(__DIR__) . '/bootstrap.php';
        $file = new SplFileObject($filepath);
        $macro = new PythoPhant_Macro($file);
        $this->assertEquals(file_get_contents($filepath), $macro->getSource());
    }
    
    /*
     * ensures that the source is set properly
     */
    public function testSetSource()
    {
       $this->macro->setSource('test');
       $this->assertAttributeEquals('test', 'source', $this->macro);
    }
    
    /*
     * ensures that the params are set properly
     */
    public function testSetParams()
    {
       $this->macro->setParams(array('test'));
       $this->assertAttributeEquals(array('test'), 'params', $this->macro);
    }
    
    public function testGetSource()
    {
        $this->macro->setSource('<?php echo %1$s');
        $this->macro->setParams(array('test'));
        
        $res = $this->macro->getSource();
        $this->assertEquals('<?php echo test', $res);
    }
    
    /**
     * get a scanner
     * 
     * @return PythoPhant_Scanner 
     */
    protected function getScanner()
    {
        $scanner = new PythoPhant_Scanner(new PythoPhant_TokenFactory());
        return $scanner;
    }

    /**
     *  
     */
    public function testCleanTokenListWithOpenTag()
    {
        $scanner = $this->getScanner();
        $scanner->scanSource('<?php ' . PHP_EOL .'echo "test"');
        $tokenList = $scanner->getTokenList();
        $count = $tokenList->count();
        
        $this->macro->cleanTokenList($tokenList);
        
        $this->assertEquals($count-2, $tokenList->count());
    }
    
    /**
     *  
     */
    public function testCleanTokenListWithOpenTagAndIndentation()
    {
        $scanner = $this->getScanner();
        $scanner->scanSource('<?php ' . PHP_EOL .'echo "test"');
        $tokenList = $scanner->getTokenList();
        $count = $tokenList->count();
        
        $this->macro->cleanTokenList($tokenList, 1);
        
        $this->assertEquals($count-1, $tokenList->count());
        $first = $tokenList[0];
        $this->assertInstanceOf('IndentationToken', $first, $first->getContent());
    }
}