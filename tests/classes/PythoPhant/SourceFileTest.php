<?php
require_once dirname(dirname(__FILE__)) . '/bootstrap.php';

/**
 * Test class for PythoPhant_ScourceFile
 */
class PythoPhant_SourceFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var PythoPhant_SourceFile 
     */
    private $source;
    
    /**
     * get the sut
     * 
     * @param SplFileObject $file
     * 
     * @return \PythoPhant_SourceFile 
     */
    private function getSourceFile($file = null)
    {
        if ($file === null) {
            $filename = dirname(PATH_TEST) . DIRECTORY_SEPARATOR . 'sources'
                . DIRECTORY_SEPARATOR . 'test.pp';
            $file = new SplFileObject($filename);
        }
        
        return new PythoPhant_SourceFile($file);
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('SplFileObject')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testConstructorIsFile()
    {
        $this->source = $this->getSourceFile();
        $this->assertAttributeEquals('test.', 'filename', $this->source);
    }
    
    public function testConstructorFileIsNotAccessible()
    {
        $mock = $this->getFileMock();
        $mock->expects($this->once())
            ->method('isReadable')
            ->will($this->returnValue(false));
        $this->setExpectedException('PythoPhant_Exception');
        $this->source = $this->getSourceFile($mock);
        
    }
    
    public function testGetContents()
    {
        $this->source = $this->getSourceFile();
        $res = $this->source->getContents();
        $filename = dirname(PATH_TEST) . DIRECTORY_SEPARATOR . 'sources'
                . DIRECTORY_SEPARATOR . 'test.pp';
        $this->assertEquals(file_get_contents($filename), $res);
    }
    
    public function testWriteTarget()
    {
        $this->source = $this->getSourceFile();
        $destination = tempnam(sys_get_temp_dir(), 'test');
        $res = $this->source->writeTarget('<?php exit();', $destination);
        $this->assertEquals('<?php exit();', file_get_contents($destination));
    }
    
    public function testWriteTargetWithInvalidcontent()
    {
        $this->source = $this->getSourceFile();
        $destination = tempnam(sys_get_temp_dir(), 'test');
        $res = $this->source->writeTarget('<?php does not compute );', $destination);
        $this->assertGreaterThan(1, $res);
        $this->assertEquals(1, $this->source->getErrorLine());
    }
}