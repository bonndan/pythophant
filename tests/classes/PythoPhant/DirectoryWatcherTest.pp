<?php

require_once dirname(dirname(__FILE__)) . '/bootstrap.php'

/**
 * Test class for Parser.
 * Generated by PHPUnit on 2012-01-21 at 18:29:24.
 */
class PythoPhant_DirectoryWatcherTest extends PHPUnit_Framework_TestCase
    
    /**
     * @var PythoPhant_DirectoryWatcher
     */
    private watcher

    setup:
        parent::setup()
        @watcher = new PythoPhant_DirectoryWatcher

    testAddDirectory:
        @watcher.addDirectory: dirname(__FILE__)
        @assertAttributeContains:  dirname(__FILE__), 'directories', @watcher
    
    testAddDirectoryFails:
        mock = @getMock: 'PythoPhant_Observer'
        mock.expects(@once()).method('update')
        @watcher.attach: mock
        @watcher.addDirectory: 'xxx'
    
    testRun:
        @watcher.addDirectory: dirname(__FILE__)
        @watcher.run()
        filename = dirname(__FILE__) '/' basename(__FILE__, 'php') 'pp'
        expected = array(filename => filemtime(filename))
        @assertAttributeEquals: expected, 'files', @watcher
    
    /**
     * test the recursive scan 
     */
    testRunScansRecursive:
        @watcher.addDirectory: dirname(dirname(__FILE__))
        @watcher.run()
        filename = dirname(__FILE__) '/' basename(__FILE__, 'php') 'pp'
        expected = array(filename => filemtime(filename))
        @assertAttributeEquals: expected, 'files', @watcher
        