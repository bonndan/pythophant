<?php
/**
 * PythoPhant_DirectoryWatcher
 * 
 * recursively scans directories for pp files and notifies observers on change
 */
class PythoPhant_DirectoryWatcher
extends PythoPhant_AbstractSubject
implements PythoPhant_Subject

    /**
     * directories to watch
     * @var array(string) 
     */
    private directories = []
    
    /**
     * observed files
     * @var array
     */
    private files = []
    
    /**
     * milliseconds, no automatic polling by default
     * @var int 
     */
    private pollingInterval = -1
    
    /**
     * add a directory path to watch
     * 
     * @param string $dir
     * 
     * @return \PythoPhant_DirectoryWatcher  
     */
    addDirectory: dir
        if (!is_dir(dir))
            return @notify: new PythoPhant_Event_Error: 'not a directory', dir
        
        @directories[] = dir
        @directories = array_unique: @directories
        
        return this
    
    /**
     * loop over the directories, store file mtime for all watched files 
     * 
     * @param int pollingInterval
     * 
     * @return void
     */
    run: pollingInterval = null
        
        if pollingInterval !== null
            @pollingInterval = pollingInterval
        
        files = []
        foreach @directories as dir
            files = array_merge: files, @scanRecursive(dir)

        foreach files as filename
            lastChange = filemtime: filename
            if isset(@files[filename])
                if (@files[filename] - lastChange) != 0
                    @notify(new PythoPhant_FileChangedEvent(filename))
            else
                @files[filename] = lastChange
                @notify(new PythoPhant_Event_Info('Watching new file',filename))

        if @pollingInterval > -1
            sleep: @pollingInterval / 1000
            call_user_func: array(this, 'run')
            
    /**
     * scan recursive
     *
     * @param string pattern
     * 
     * @return array
     */
    private scanRecursive: path
        
        files = []
        foreach new DirectoryIterator(path) as file
            if file.getExtension() == 'pp'
                files[] = file.getRealPath()
            elseif file.isDir() && not in_array(file.getBasename(), array('.', '..'))
                files = array_merge: files, @scanRecursive: file.getRealPath()
        
        return files
        