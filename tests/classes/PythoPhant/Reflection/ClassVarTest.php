<?php 

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Reflection_ClassVar.
 */
class PythoPhant_Reflection_ClassVarTest extends PHPUnit_Framework_TestCase
{
    /**
     * create an instance
     * 
     * @return PythoPhant_Reflection_ClassVar
     */
    protected function getClassVar($content = null)
    {
        if (!$content)
        $content = "/**
 * something
 * @var string
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        return new PythoPhant_Reflection_ClassVar('myVar', $doc);
    }
    
    public function testConstructor()
    {
        $var = $this->getClassVar();
        $this->assertEquals('myVar', $var->getName());
        $this->assertInstanceOf('ReturnValueToken', $var->getType());
        $this->assertEquals('string', $var->getType()->getContent(true));
    }
    
    /**
     * 
     */
    public function testIsProperty()
    {
        $content = "/**
 * something
 * @var string
 * @property
 */";
        $var = $this->getClassVar($content);
        $this->assertTrue($var->isProperty());
    }
}