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
    
    /**
     * @return PythoPhant_Scanner
     */
    protected function getScanner()
    {
        return new PythoPhant_Scanner($this->tokenFactory);
    }

    /**
     * 
     */
    public function testProcessTokenListFindsClass()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest

";
        $scanner->scanSource($source);
        
        $this->object->processTokenList($scanner->getTokenList());
        $class = $this->object->getReflectionElement();
        $this->assertInstanceOf('PythoPhant_Reflection_Class', $class);
        $this->assertEquals('MyTest', $class->getName() );
    }
    
        /**
     * 
     */
    public function testProcessTokenListFindsClassWithExtends()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest
extends Something

";
        $scanner->scanSource($source);
        
        $this->object->processTokenList($scanner->getTokenList());
        $class = $this->object->getReflectionElement();
        $this->assertAttributeEquals('Something', 'extends', $class);
    }
    
    /**
     * ensure implemented interfaces are passed
     */
    public function testProcessTokenListFindsClassWithImplements()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest
implements Something, SomeOtherThing

";
        $scanner->scanSource($source);
        
        $this->object->processTokenList($scanner->getTokenList());
        $class = $this->object->getReflectionElement();
        $this->assertAttributeContains('Something', 'implements', $class);
        $this->assertAttributeContains('SomeOtherThing', 'implements', $class);
    }
    

    
    /**
     * 
     */
    public function testProcessTokenListThrowsMissingDocCommentException()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest

    private aVar
";
        $scanner->scanSource($source);
        $this->setExpectedException('PythoPhant_Exception');
        $this->object->processTokenList($scanner->getTokenList());
    }
    
    /**
     * 
     */
    public function testProcessTokenListFindsClassVar()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest

    /**
     * some description
     * @var string
     */
    private aVar = 1
    
    /**
     * more description
     * @var array
     */
    private static anArray
";
        $scanner->scanSource($source);
        $tokenList = $scanner->getTokenList(); 
        $this->object->processTokenList($tokenList);
        $class = $this->object->getReflectionElement();
        $vars = $class->getVars();
        $this->assertEquals(2, count($vars));
        $this->assertArrayHasKey('aVar', $vars);
        $this->assertArrayHasKey('anArray', $vars);
    }

    public function testProcessTokenListFindsConstant()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest

    /**
     * some constant
     * @var string
     */
    const MY_CONST = 'test'
    
";
        $scanner->scanSource($source);
        $tokenList = $scanner->getTokenList();
        $this->object->processTokenList($tokenList);
        $class = $this->object->getReflectionElement();
        $consts = $class->getConstants();
        $this->assertEquals(1, count($consts));
        $this->assertArrayHasKey('MY_CONST', $consts);
    }
    
    /**
     * 
     */
    public function testProcessTokenListFindsClassMethods()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest
extends Something

    /**
     * some description
     * @param string myString
     * @return void
     */
    private myFunction
        echo 'test'

";
        $scanner->scanSource($source);
        
        $this->object->processTokenList($scanner->getTokenList());
        $class = $this->object->getReflectionElement();
        $methods = $class->getMethods();
        $this->assertEquals(1, count($methods));
        $this->assertEquals('myFunction', key($methods));
    }
    
    /**
     * testing of a function body
     */
    public function testParseBlocks()
    {
        $scanner = $this->getScanner();
        $source = "<?php
/**
 * doc comment
 */
class MyTest
extends Something

    /**
     * some description
     * @param string myString
     */
    myFunction:
        if something == true
            echo 'Test'
        else
            echo 'No Test'

";
        $scanner->scanSource($source);
        
        $this->object->processTokenList($scanner->getTokenList());
        $class = $this->object->getReflectionElement();
    }
}

