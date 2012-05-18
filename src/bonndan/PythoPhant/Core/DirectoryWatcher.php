<?php
namespace PythoPhant\Core;

use PythoPhant\Event\Subject as Subject;
use PythoPhant\Event\AbstractSubject as AbstractSubject;
use PythoPhant\Event\Error as Error;
use PythoPhant\Event\FileChanged as FileChanged;
use PythoPhant\Event\Info as Info;

/**
 * DirectoryWatcher
 * 
 */
class DirectoryWatcher extends AbstractSubject implements Subject
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
        if (! is_string($dir)) {
            throw new \InvalidArgumentException("dir is not of type string") ;
        }
        //
        if ((!is_dir($dir))) {
            return $this->notify(new Error('not a directory', $dir));

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
            $this->pollingInterval = $pollingInterval;

        }
        $files = array();
        foreach ($this->directories as $dir) {
            $files = array_merge($files, $this->scanRecursive($dir));

        }
        foreach ($files as $filename) {
            $lastChange = filemtime($filename);
            if (isset($this->files[$filename])) {
                if (($this->files[$filename] - $lastChange) != 0) {
                    $this->notify(new FileChanged($filename));
                }
            }
            else {
                $this->files[$filename] = $lastChange;
                $this->notify(new Info('Watching new file',$filename));

            }
        }
        if ($this->pollingInterval > -1) {
            sleep($this->pollingInterval / 1000);
            call_user_func(array($this, 'run'));
        }
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
            }
            elseif ($file->isDir() && ! in_array($file->getBasename(), array('.', '..'))) {
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
}
