<?php
namespace PythoPhant;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test class for DocCommentToken
 * 
 */
class DocCommentTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var DocCommentToken 
     */
    private $token;

    
    /**
     * test the whole block indentation function 
     */
    public function testIndent()
    {
        $this->token = $this->getToken();
        
        $this->token->indent(1);
        $content =   "    /**
     * test
     *
     * more lines
     * here
     * 
     * @param string          test some var
     * @param SomeInterface[] test2 = array() some other var
     *
     * @return void
     * @author Daniel Pozzi <bonndan76@googlemail.com>
     */";
        $this->assertEquals($content, $this->token->getContent());
    }
    
    public function testProcessPHPDoc()
    {
        $this->token = $this->getToken();
        
        $this->assertAttributeEquals('test', 'shortDesc', $this->token);
        $this->assertAttributeContains('more lines', 'longDesc', $this->token);
        $this->assertAttributeContains('here', 'longDesc', $this->token);
        $value = $this->token->getAnnotation('author');
        $this->assertContains('Daniel Pozzi', $value[0], serialize($value));
        $this->assertEquals('void', $this->token->getReturnType());
        $this->assertEquals('test', $this->token->getShortDescription());
        $this->assertContains('more lines', $this->token->getLongDescription());
    }
    
    /**
     * 
     */
    public function testProcessPHPDocRecognizesParams()
    {
        $this->token = $this->getToken();
        
        $params = $this->token->getParams();
        $this->assertArrayHasKey('test', $params);
        $this->assertArrayHasKey('test2', $params);
        $this->assertEquals(array('string', 'some var', null), $params['test']);
        
    }
    
    public function testProcessPHPDocRecognizesParamDefault()
    {
        $this->token = $this->getToken();

        $params = $this->token->getParams();
        $this->assertEquals(array('SomeInterface[]', 'some other var', 'array()'), $params['test2']);
    }
    
    /**
     * ensures that any annotation is noted 
     */
    public function testSetCustomAnnotation()
    {
        $content = 
"/**
 * test
 *
 * more lines
 * here
 * 
 * @customAnnotation test123
 */";
        $this->token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
        $custom = $this->token->getAnnotation('customAnnotation');
        $this->assertEquals('test123', $custom[0]);
    }
    
    public function testIsNotMethodComment()
    {
                $content = 
"/**
 * test
 * @var string
 */";
        $this->token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
        $this->assertFalse($this->token->isMethodComment());
    }
    
    public function testIsMethodComment()
    {
                $content = 
"/**
 * test
 * @return string
 */";
        $this->token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
        $this->assertTrue($this->token->isMethodComment());
    }
    
    /**
     * 
     */
    public function testGetRebuiltContent()
    {
                $content = 
"/**
 * test
 *
 * more lines
 * here
 * 
 * @param string          test some var
 * @param SomeInterface[] test2 = array() some other var
 *
 * @return void
 * @throws InvalidArgumentException
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @someToken someContent
 */";
        $token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
        
        $content = $token->getRebuiltContent();
        $this->assertContains(' * test', $content);
        $this->assertContains(' * @param string $test some var', $content);
        $this->assertContains(' * @param SomeInterface[] $test2 some other var', $content);
        $this->assertContains(' * @return void', $content);
        $this->assertContains(' * @throws InvalidArgumentException', $content, $content);
        $this->assertContains(' * @someToken someContent', $content, $content);
        $this->assertContains(' * @author Daniel Pozzi <bonndan76@googlemail.com>', $content);
    }
    
    /**
     * get an isntance
     * @return \DocCommentToken 
     */
    protected function  getToken()
    {
        $content = 
"/**
 * test
 *
 * more lines
 * here
 * 
 * @param string          test some var
 * @param SomeInterface[] test2 = array() some other var
 *
 * @return void
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */";
        $token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
        
        return $token;
    }
}