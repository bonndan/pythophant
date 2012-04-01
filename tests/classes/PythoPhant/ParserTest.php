<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class PythoPhant_ParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $object;
    
    /**
     * @var PythoPhant_TokenFactory
     */
    private $tokenFactory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->tokenFactory = new PythoPhant_TokenFactory();
        $this->object = new PythoPhant_Parser(
            $this->tokenFactory
        );
    }

    protected function getTokenList()
    {
        return new TokenList();
    }
    
    /**
     * testScanTokenList().
     */
    public function testprocessTokenList()
    {
        $tokenList = new TokenList();
        $tokenMock = $this->getMockBuilder('CustomGenericToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($tokenMock);
        
        $tokenMock->expects($this->once())
            ->method('affectTokenList');
        
        $this->object->processTokenList($tokenList);
    }

    public function testParseLineEndsWithSimpleClassDeclaration()
    {
        $tokenList = $this->getTokenList();
        $this->object->setTokenList($tokenList);
        $tokenList
            ->pushToken($this->tokenFactory->createToken('T_CLASS', 'class'))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'MyClass'));
        $nlToken = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($nlToken);
        
        $nlToken->expects($this->once())
            ->method('setAuxValue')
            ->with("");
        $nlToken->expects($this->once())
            ->method('setContent')
            ->with(PHP_EOL);
        $this->object->parseLineEnds();
        
        $this->assertEquals(5, count($tokenList));
    }
    
    public function testParseLineEndsWithClassDeclarationAndExtends()
    {
        $tokenList = $this->getTokenList();
        $this->object->setTokenList($tokenList);
        $nlToken = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
         
        $tokenList
            ->pushToken($this->tokenFactory->createToken('T_CLASS', 'class'))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'MyClass'))
            ->pushToken(NewLineToken::createEmpty())
            ->pushToken($this->tokenFactory->createToken('T_EXTENDS', 'extends'))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'SomeClass'))
            ;
        $nlToken2 = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($nlToken2);
        
        $nlToken->expects($this->never())
            ->method('setContent')
            ;
        $nlToken2->expects($this->once())
            ->method('setAuxValue')
            ->with("");
        $nlToken2->expects($this->once())
            ->method('setContent')
            ->with(PHP_EOL);
        $this->object->parseLineEnds();
        
        $this->assertEquals(8, count($tokenList));
    }
    
    public function testSimpleFunctionDeclaration()
    {
        $tokenList = $this->getTokenList();
        $this->object->setTokenList($tokenList);
        $tokenList
            ->pushToken(IndentationToken::create(1))
            ->pushToken($this->tokenFactory->createToken('T_FUNCTION', 'function'))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'myFunction'))
            ->pushToken($this->tokenFactory->createToken('T_OPEN_BRACE', '('))
            ->pushToken($this->tokenFactory->createToken('T_CLOSE_BRACE', ')'))
            ;
        $nlToken = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($nlToken);
        
        $nlToken->expects($this->once())
            ->method('setAuxValue')
            ->with("");
        $nlToken->expects($this->once())
            ->method('setContent')
            ->with(PHP_EOL);
        $this->object->parseLineEnds();
        
        $this->assertEquals(9, count($tokenList));
    }
    
    public function testImplicitFunctionDeclaration()
    {
        $tokenList = $this->getTokenList();
        $this->object->setTokenList($tokenList);
        $tokenList
            ->pushToken(IndentationToken::create(1))
            ->pushToken($this->tokenFactory->createToken('T_PRIVATE', 'private'))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'myFunction'))
            ->pushToken($this->tokenFactory->createToken('T_OPEN_BRACE', '('))
            ->pushToken($this->tokenFactory->createToken('T_CLOSE_BRACE', ')'))
            ;
        $nlToken = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($nlToken);
        
        $nlToken->expects($this->once())
            ->method('setAuxValue')
            ->with("");
        $nlToken->expects($this->once())
            ->method('setContent')
            ->with(PHP_EOL);
        $this->object->parseLineEnds();
        
        $this->assertEquals(10, count($tokenList));
        
        $func = $tokenList->offsetGet(1);
        $this->assertEquals('private', $func->getContent());
        $func = $tokenList->offsetGet(2);
        $this->assertEquals('function', trim($func->getContent()), serialize($func->getContent()));
    }
    
    public function testBlockIsOpened()
    {
        $tokenList = $this->getTokenList();
        $this->object->setTokenList($tokenList);
        $tokenList
            ->pushToken(IndentationToken::create(1))
            ->pushToken($this->tokenFactory->createToken('T_IF', 'if'))
            ->pushToken($this->tokenFactory->createToken('T_OPEN_BRACE', '('))
            ->pushToken($this->tokenFactory->createToken('T_STRING', 'false'))
            ->pushToken($this->tokenFactory->createToken('T_CLOSE_BRACE', ')'))
            ;
        $nlToken = $this->getMockBuilder('NewLineToken')
            ->disableOriginalConstructor()
            ->getMock();
        $tokenList->pushToken($nlToken);
        
        $nlToken->expects($this->once())
            ->method('setAuxValue')
            ->with(' ' . PythoPhant_Grammar::T_OPEN_BLOCK);
        $nlToken->expects($this->never())
            ->method('setContent');
        $this->object->parseLineEnds();
        
        $this->assertEquals(8, count($tokenList));
    }
}

