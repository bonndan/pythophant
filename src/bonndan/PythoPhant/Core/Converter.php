<?php
namespace PythoPhant\Core;

use PythoPhant\Event\AbstractSubject;
use PythoPhant\Event\Observer;
use PythoPhant\Event\Subject;
use PythoPhant\Event\Event;
use PythoPhant\Event\FileChange;
use PythoPhant\Event\Error;
use PythoPhant\Exception;
use PythoPhant\Renderer\Helper as RenderHelper;

/**
 * PythoPhant_Converter
 * 
 * converts pp files to php
 * 
 * 
 */
class Converter extends AbstractSubject implements Observer, Subject
{
    /**
     * path of the directory where required tests can be found. If null no tests
     * are required nor executed
     * 
     * @var string 
     */
    private $requireTestFileDir = null;
    
    /**
     * source file scanner
     * @var Scanner
     */
    private $scanner;
    
    /**
     * token list parser
     * @var Parser
     */
    private $parser;
    
    /**
     * token list Renderer
     * @var PythoPhant_RenderHelper
     */
    private $renderer;
    
    /**
     * inject the dependencies
     * 
     * @param Scanner             $scanner
     * @param Parser              $parser
     * @param PythoPhant_Renderer $renderer 
     */
    public function __construct(
        Scanner $scanner,
        Parser $parser,
        RenderHelper $renderer
    ) {
        $this->scanner  = $scanner;
        $this->parser   = $parser;
        $this->renderer = $renderer;
        
        parent::__construct();
    }
    
    /**
     * set / enable test-forcing
     * 
     * @param string $dir
     * 
     * @throws \InvalidArgumentException
     * @return Converter
     */
    public function setTestFileDir($dir)
    {
        if (!is_null($dir) && !is_dir($dir)) {
            throw new \InvalidArgumentException('test dir not found: ' . $dir);
        }
        
        $this->requireTestFileDir = $dir;
        return $this;
    }
    
    /**
     * receives fileChange events and converts the related files
     * 
     * @param Event $event 
     */
    public function update(Event $event)
    {
        if ($event instanceof FileChange) {
            $source = new SourceFile(
                new \SplFileObject($event->getPath())
            );
            $this->convert($source);
        }
        
        return $this;
    }
    
    /**
     *
     * @param SourceFile $source
     * 
     * @return string 
     */
    private function getTestFileName(SourceFile $source)
    {
        $testFileName = basename($source->getFilename(), '.php');
        $testFileName .= 'Test.php';
        
        $sharedPath = dirname($this->requireTestFileDir);
        $extra = substr($source->getPath(), strlen($sharedPath));
        
        $testFile = $this->requireTestFileDir . $extra . DIRECTORY_SEPARATOR . $testFileName;
        
        return $testFile;
    }
    
    /**
     *
     * @param string $testFile
     * 
     * @return boolean 
     */
    private function isTestFilePresent($testFile)
    {
        if (!file_exists($testFile)) {
            $event = new Error(
                'Test file not found: ' . $testFile,
                $testFile,
                0
            );
            $this->notify($event);
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * runs the unit test if one present, sends events
     * 
     * @param string $testFile
     * 
     * @return void 
     */
    private function runTest($testFile) {
        $unit = 'phpunit ' . $testFile . ' 2>&1';
        $command = ShellCommand::createWith($unit);
        $returnVal = $command->execute();
        if ($returnVal) {
            $event = new Error(
                'Error executing test: ' . $command->getOutput(),
                $testFile,
                0
            );
            $this->notify($event);
            return;
        } 
        
        $info = new \PythoPhant\Event\Info('Test results: ' . $command->getOutput());
        $this->notify($info);
    }
    
    /**
     * convert a pp source file into php
     * 
     * @param SourceFile $filename 
     * @param bool       $debug 
     * 
     * @return string
     */
    public function convert(SourceFile $source, $debug = false)
    {
        if ($this->requireTestFileDir !== null) {
            $testFile = $this->getTestFileName($source);
            if (!$this->isTestFilePresent($testFile)) {
                return false;
            }
            $this->runTest($testFile);
        }
        
        $contents = $source->getContents();
        try {
            $this->scanner->scanSource($contents);
        } catch (\PythoPhant\Exception $exc) {
            $event = new Error(
                'Error scanning the source: ' .$exc->getMessage(),
                $source->getFilename(),
                $exc->getSourceLine()
            );
            return $this->notify($event);
        }
        $tokenList = $this->scanner->getTokenList();
        try {
            $this->parser->parseElement($tokenList);
        } catch (Exception $exc) {
            $event = new Error(
                'Error parsing the token list: ' . $exc->getMessage(),
                $source->getFilename(),
                $exc->getSourceLine()
            );
            $this->notify($event);
            if (!$debug) {
                return false;
            }
        }
        
        $this->renderer->enableDebugging($debug);
        $this->renderer->setReflectionElement($this->parser->getElement());
        $date = date('Y/m/d H:i:s');
        $this->renderer->addWaterMark(
            'generated by PythoPhant on ' . $date 
            . ' from ' . $source->getFilename() 
            . ' #' . md5($contents)
        );
        $content = $this->renderer->getPHPSource();
        echo $content;
        return $source->writeTarget($content);
    }

}