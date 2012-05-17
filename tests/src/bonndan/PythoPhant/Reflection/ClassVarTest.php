<?php 
namespace PythoPhant\Reflection;

require_once dirname(__FILE__) . '/bootstrap.php';

use PythoPhant\DocCommentToken;
/**
 * Test class for PythoPhant_Reflection_ClassVar.
 */
class ClassVarTest extends \PHPUnit_Framework_TestCase
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
        return new ClassVar('myVar', $doc);
    }
    
    public function testConstructor()
    {
        $var = $this->getClassVar();
        $this->assertEquals('myVar', $var->getName());
        $this->assertInstanceOf("PythoPhant\ReturnValueToken", $var->getType());
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