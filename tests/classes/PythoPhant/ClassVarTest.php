<?php 

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_ClassVar.
 */
class PythoPhant_ClassVarTest extends PHPUnit_Framework_TestCase
{
    /**
     * create an instance
     * 
     * @return PythoPhant_ClassVar
     */
    protected function getClassVar($content = null)
    {
        if (!$content)
        $content = "/**
 * something
 * @var string
 */";
        $doc = new DocCommentToken('T_DOC_COMMENT', $content, 0);
        return new PythoPhant_ClassVar('myVar', $doc);
    }
    
    public function testConstructor()
    {
        $var = $this->getClassVar();
        $this->assertEquals('myVar', $var->getName());
        $this->assertEquals('string', $var->getType());
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
        $this->assertEquals('property', $var->isProperty());
    }
}