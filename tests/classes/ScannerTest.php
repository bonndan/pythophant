<?php

require_once dirname(__FILE__) . '/../../classes/Scanner.php';

/**
 * Test class for Scanner.
 */
class ScannerTest extends PHPUnit_Framework_TestCase
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
        $this->tokenFactory = $this->getMock('TokenFactory');
        $this->object = new Scanner(
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
        $this->object = new Scanner(new TokenFactory);
            
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
}

