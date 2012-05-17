<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test for the Macro
 * 
 *  
 */
class TemplateMacroTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PythoPhant_Macro 
     */
    private $macro;
        
    public function setup()
    {
        parent::setUp();
        $this->macro = new TemplateMacro();
    }
    
    public function testConstructor()
    {
        $this->assertInstanceOf("PythoPhant\Macro", $this->macro);
    }
    
    public function testConstructorWithSplFileObject()
    {
        $filepath = dirname(__DIR__) . '/bootstrap.php';
        $file = new \SplFileObject($filepath);
        $macro = new TemplateMacro($file);
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
        $scanner = new Core\TokenFactoryScanner(new Core\RegisteredTokenFactory());
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