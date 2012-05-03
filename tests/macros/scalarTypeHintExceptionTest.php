<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * conversion test
 * 
 */
class ScalarTypeHintExceptionMacroTest extends PHPUnit_Framework_TestCase
{
    /**
     * get a configuted macro
     * 
     * @return PythoPhant_Macro 
     */
    public function getMacro()
    {
        $macro = new PythoPhant_Macro();
        return $macro;
    }
    
    /**
     * ensures the correct conversion of the scalar type hint exception macro 
     */
    public function testConversion()
    {
       $macro = $this->getMacro();
       $filename = dirname(dirname(__DIR__)) . '/macros/scalarTypeHintException.pp';
       $macro->setSource(file_get_contents($filename));
       $macro->setParams(array('bool', 'myVar'));
       
       $source = $macro->getSource();
       $factory = new PythoPhant_TokenFactory();
       $scanner = new PythoPhant_Scanner($factory);
       
       $scanner->scanSource($source);
       $tokenList = $scanner->getTokenList();
       $this->assertInstanceOf('TokenList', $tokenList);
       $this->assertEquals(23, $tokenList->count());
    }
}