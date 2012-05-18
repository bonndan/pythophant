<?php
namespace PythoPhant\Core;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Project
     */
    protected $project;
    
    public function setup()
    {
        parent::setup();
        $this->project = new Project();
    }
    
    /**
     * 
     */
    public function testReadConfigFileWithWrongFile()
    {
        $this->assertTrue($this->project->getTestAfterConversion());
        $this->setExpectedException('InvalidArgumentException');
        $this->project->readConfigurationFile('test');
    }
    
    /**
     * 
     */
    public function testReadConfigFile()
    {
        $file = dirname(PATH_TEST)
            . DIRECTORY_SEPARATOR 
            . Project::DEFAULT_CONFIG_FILE
        ;
        $this->project->readConfigurationFile($file);
    }
    
    /**
     * 
     */
    public function testReadConfigWithJson()
    {
        $this->assertTrue($this->project->getTestAfterConversion());
        $string = '{"testAfterConversion": false}';
        $this->project->readConfiguration($string);
        $this->assertFalse($this->project->getTestAfterConversion());
    }
    
    /**
     * 
     */
    public function testGetProjectName()
    {
        $string = '{"project": "test"}';
        $this->project->readConfiguration($string);
        $this->assertEquals("test", $this->project->getProjectName());
    }
    
    /**
     * 
     */
    public function testGetWatchedDirs()
    {
        $string = '{"watch": ["."]}';
        $this->project->readConfiguration($string);
        $this->assertContains(".", $this->project->getWatchedDirectories());
    }
    
    /**
     * 
     */
    public function testGetWatchedDirsFalseReturnsArray()
    {
        $string = '{"watch": false}';
        $this->project->readConfiguration($string);
        $this->assertEmpty($this->project->getWatchedDirectories());
    }
    
    /**
     * 
     */
    public function testGetLoggers()
    {
        $string = '{"loggers": ["Console"]}';
        $this->project->readConfiguration($string);
        $this->assertContains("Console", $this->project->getLoggers());
    }
    
    /**
     * 
     */
    public function testGetLoggersFalseReturnsArray()
    {
        $string = '{"loggers": false}';
        $this->project->readConfiguration($string);
        $this->assertEmpty($this->project->getLoggers());
    }
}