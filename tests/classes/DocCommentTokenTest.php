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
    
    public function setup()
    {
        parent::setUp();
        $content = 
"/**
 * test
 *
 * more lines
 * here
 * 
 * @param string \$test some var
 *
 * @return void
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */";
        $this->token = new DocCommentToken('T_DOC_COMMENT', $content, 1);
    }
    
    public function testIndent()
    {
        $this->token->indent(1);
        $content =   "    /**
     * test
     *
     * more lines
     * here
     * 
     * @param string \$test some var
     *
     * @return void
     * @author Daniel Pozzi <bonndan76@googlemail.com>
     */";
        $this->assertEquals($content, $this->token->getContent());
    }
    
    public function testProcessPHPDoc()
    {
        $this->token->processPHPDoc();
        
        $this->assertAttributeEquals('test', 'shortDesc', $this->token);
        $this->assertAttributeContains('more lines', 'longDesc', $this->token);
        $this->assertAttributeContains('here', 'longDesc', $this->token);
        $this->assertAttributeEquals(array('void', null, null), 'return', $this->token);
        $this->assertAttributeContains(array('string', '$test', 'some var'), 'param', $this->token);
    }
}