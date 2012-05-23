<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';


/**
 * conversion test of ScalarTypeHintExceptionMacro
 * 
 */
class ScalarTypeHintExceptionMacroTest extends \PHPUnit_Framework_TestCase
{
    /**
     * get a configuted macro
     * 
     * @return PythoPhant_Macro 
     */
    public function getMacro()
    {
        $macro = new TemplateMacro();
        return $macro;
    }
    
    /**
     * ensures the correct conversion of the scalar type hint exception macro 
     */
    public function testConversion()
    {
       $macro = $this->getMacro();
       $filename = dirname(PATH_TEST) . '/macros/scalarTypeHintException.pp';
       $macro->setSource(file_get_contents($filename));
       $macro->setParams(array('bool', 'myVar'));
       
       $source = $macro->getSource();
       $factory = new Core\RegisteredTokenFactory;
       $scanner = new Core\TokenFactoryScanner($factory);
       
       $scanner->scanSource($source);
       $tokenList = $scanner->getTokenList();
       $this->assertInstanceOf("PythoPhant\TokenList", $tokenList);
       $this->assertEquals(23, $tokenList->count());
    }
}