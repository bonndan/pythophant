<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_Renderer
 * 
 * .
 */
class PythoPhant_RendererHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * sut
     * @var PythoPhant_Renderer 
     */
    private $renderer;
    
    public function setup()
    {
        parent::setUp();
        $this->renderer = new PythoPhant_RenderHelper();
    }
    
    public function testSetTokenList()
    {
        $element = $this->getMock('PythoPhant_Reflection_Element');
        $this->renderer->setReflectionElement($element);
        $this->assertAttributeEquals($element, 'ref', $this->renderer);
    }
    
    public function testEnableDebugging()
    {
        $res = $this->renderer->enableDebugging(true);
        $this->assertAttributeEquals(true, 'debug', $this->renderer);
        $this->assertSame($this->renderer, $res);
    }
    
    public function testAddWatermark()
    {
        $element = $this->getMock('PythoPhant_Reflection_Element');
        $this->renderer->setReflectionElement($element);
        $this->assertAttributeEquals($element, 'ref', $this->renderer);
        
        $doc = $this->getMockBuilder('DocCommentToken')
            ->disableOriginalConstructor()
            ->getMock();
        $doc->expects($this->once())
            ->method('appendToLongDescription');
        $element->expects($this->once())
            ->method('getDocComment')
            ->will($this->returnValue($doc));
        $this->renderer->addWaterMark('test');
    }
    
    public function testGetPHPSource()
    {
        $docComment = new DocCommentToken('T_DOC_COMMENT', '', '');
        $element = new PythoPhant_Reflection_Class('test', $docComment);
        $this->renderer->setReflectionElement($element);
        
        $res = $this->renderer->getPHPSource();
        $this->assertContains('<?php', $res);
        $this->assertContains('class Test', $res);
    }
}
   