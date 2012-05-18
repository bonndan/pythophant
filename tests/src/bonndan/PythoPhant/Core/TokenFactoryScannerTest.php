<?php
namespace PythoPhant\Core;

require_once dirname(__FILE__) . '/bootstrap.php';

use PythoPhant\PHPToken;

/**
 * Test class for Scanner.
 */
class TokenFactoryScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Scanner
     */
    protected $object;
    
    /**
     *
     */
    private $tokenFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->tokenFactory = $this->getMock("PythoPhant\Core\TokenFactory");
        $this->object = new TokenFactoryScanner(
            $this->tokenFactory
        );
    }

    public function makeToken($tokenName, $content, $line)
    {
        return new PHPToken($tokenName, $content, $line);
    }
    
    /**
     * testParse().
     */
    public function testScanSource()
    {
        $this->object = new TokenFactoryScanner(new RegisteredTokenFactory());
            
        $source = "<?php \necho 'Hello World';";
        $res = $this->object->scanSource($source);
        
        $tokens = $this->object->getTokenList();
        $this->assertEquals(6, count($tokens));
        $this->assertEquals('T_OPEN_TAG', $tokens[0]->getTokenName());
        $this->assertEquals('<?php ', $tokens[0]->getContent());
        $this->assertEquals('T_NEWLINE', $tokens[1]->getTokenName());
        $this->assertEquals('T_ECHO', $tokens[2]->getTokenName());
        $this->assertEquals('T_WHITESPACE', $tokens[3]->getTokenName());
        $this->assertEquals('T_CONSTANT_ENCAPSED_STRING', $tokens[4]->getTokenName());
    }
    
    /**
     * 
     */
    public function testScanSourceWithLogicException()
    {
        $tokenFactory = $this->tokenFactory;
        
        $this->object = new TokenFactoryScanner($tokenFactory);
        $tokenFactory->expects($this->any())
            ->method('getTokenName')
            ->will($this->throwException(new \LogicException('test', 1)));
        
        $this->setExpectedException("\PythoPhant\Exception");
        $source = "<?php \necho 'Hello World';";
        $res = $this->object->scanSource($source);
    }
    
    /**
     * 
     */
    public function testScanSourceWithPythoPhantException()
    {
        $tokenFactory = $this->tokenFactory;
        
        $this->object = new TokenFactoryScanner($tokenFactory);
        $tokenFactory->expects($this->any())
            ->method('getTokenName')
            ->will($this->returnValue(array('T_STRING')));
        
        $tokenFactory->expects($this->any())
            ->method('createToken')
            ->will($this->throwException(new \PythoPhant\Exception('test', 1)));
        
        $this->setExpectedException("\PythoPhant\Exception");
        $source = "<?php \necho 'Hello World';";
        $res = $this->object->scanSource($source);
    }
    
    /**
     * testParse().
     */
    public function testScanJsonSource()
    {
        $this->object = new TokenFactoryScanner(new RegisteredTokenFactory());
            
        $source = "<?php \nmyVar = {'uncle': ['bob', 'joe']}\n";
        $res = $this->object->scanSource($source);
        
        $tokens = $this->object->getTokenList();
        $this->assertEquals(18, count($tokens));
        $this->assertEquals('T_OPEN_TAG', $tokens[0]->getTokenName());
        $this->assertEquals('<?php ', $tokens[0]->getContent());
        $this->assertEquals('T_NEWLINE', $tokens[1]->getTokenName());
        $this->assertEquals('T_STRING', $tokens[2]->getTokenName());
        $this->assertEquals('myVar', $tokens[2]->getContent());
        $this->assertEquals('T_WHITESPACE', $tokens[3]->getTokenName());
        $this->assertEquals('T_ASSIGN', $tokens[4]->getTokenName());
        $this->assertEquals('T_JSON_OPEN_OBJECT', $tokens[6]->getTokenName());
    }

    public function testCreate()
    {
        $scanner = TokenFactoryScanner::create();
        $this->assertInstanceOf("PythoPhant\Core\Scanner", $scanner);
    }
}

