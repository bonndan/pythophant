<?php
namespace PythoPhant\Core;

use PythoPhant\Event\Event;
use PythoPhant\Event\Observer;
use PythoPhant\Event\Subject;
use PythoPhant\Event\AbstractSubject;
use PythoPhant\Event\Error;
use PythoPhant\Event\FileChange;
use PythoPhant\Event\Info;
use PythoPhant\Event\ScanTrigger;

/**
 * DirectoryWatcher
 * 
 */
class DirectoryWatcher extends AbstractSubject implements Subject, Observer
{
    /**
     * directories to watch
     * @var array(string) 
     */
    private $directories = array();


    /**
     * observed files
     * @var array
     */
    private $files = array();


    /**
     * milliseconds, no automatic polling by default
     * @var int 
     */
    private $pollingInterval = -1;


    /**
     * add a directory path to watch
     * 
     * @param string dir
     * 
     * @return \PythoPhant\Core\DirectoryWatcher  
     */
    public function addDirectory($dir)
    {
        if (!is_string($dir)) {
            throw new \InvalidArgumentException("dir is not of type string") ;
        }
        
        if (!is_dir($dir)) {
            return $this->notify(new Error('not a directory: ' . $dir, $dir));
        }
        
        $this->directories[] = $dir;
        $this->directories = array_unique($this->directories);

        return $this;
    }

    /**
     * loop over the directories, store file mtime for all watched files 
     * 
     * @param int pollingInterval = null
     * 
     * @return void
     */
    public function run($pollingInterval = null)
    {
        if ($pollingInterval !== null) {
            $this->setPollingInterval($pollingInterval);

        }
        $files = array();
        foreach ($this->directories as $dir) {
            $files = array_merge($files, $this->scanRecursive($dir));

        }
        foreach ($files as $filename) {
            $lastChange = filemtime($filename);
            if (isset($this->files[$filename])) {
                if (($this->files[$filename] - $lastChange) != 0) {
                    $this->notify(new FileChange($filename));
                }
            }
            else {
                $this->files[$filename] = $lastChange;
                $this->notify(new Info('Watching new file',$filename));

            }
        }
        if ($this->pollingInterval > -1) {
            sleep($this->pollingInterval / 1000);
            $this->notify(new ScanTrigger());
        }
    }

    /**
     * set the polling interval (microseconds)
     * 
     * @param int $interval
     * 
     * @return \PythoPhant\Core\DirectoryWatcher 
     */
    public function setPollingInterval($interval)
    {
        $this->pollingInterval = (int)$interval;
        return $this;
    }
    
    /**
     * scan recursive
     *
     * @param string path
     * 
     * @return array
     */
    private function scanRecursive($path)
    {
        if (! is_string($path)) {
            throw new \InvalidArgumentException("path is not of type string") ;
        }
        //
        $files = array();
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->getExtension() == 'pp') {
                $files[] = $file->getRealPath();
            } elseif ($file->isDir() && ! in_array($file->getBasename(), array('.', '..'))) {
                $files = array_merge($files, $this->scanRecursive($file->getRealPath()));

            }
        }
        return $files;
        
    }

    /**
     * for debugging. returns an array of watched fiels
     * 
     * @return array(path => filemtime)
     */
    public function getWatchedFiles()
    {
        return $this->files;
    }
    
    /**
     * observe ScanTrigger events
     * 
     * @param Event $event 
     */
    public function update(Event $event)
    {
        if ($event instanceof ScanTrigger) {
            $this->run();
        }
    }
}

