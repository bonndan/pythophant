<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for DocCommentToken
 * 
 */
class DocCommentTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var DocCommentToken 
     */
    private $token;
    
    protected function getToken()
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
        
        $params = $this->token->getParams();
        $this->assertEquals(array('string', 'some var', null), $params['test']);
        
        $this->assertEquals('test', $this->token->getShortDescription());
        $this->assertContains('more lines', $this->token->getLongDescription());
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
}