<?php
/**
 * ColonTest 
 */
class ColonTest

    /**
     * @var string 
     */
    private aVar

    /**
     * myFunction
     * 
     * @param Countable aCountable
     * @param mixed myVar = ''
     * 
     * @return int
     */
    public myFunction:
        unset: myVar
        aCountable.count:
        return int_val: count: aCountable

    /**
     * @param string aVar
     * @param int    bv 
     */
    private setVar:
        @aVar = aVar

    /**
     * a function
     * @return string 
     */
    aFunc:
        myVar = 1
        if @aVar == NULL
            @setVar: new SomeClass: myVar "someConstructorArg", 1
        return ucfirst: str_replace: 'a', 'b', 'tesT'

    /**
     * a test
     * 
     * @param array data = null 
     */
    private aTest:
        if data is not empty?
            @aFunc()
        if data is not array?
            @aFunc()
        if someVar is readable? and someVar is callable?
            @aFunc()
    
        foreach data as akey => val
            somearray[] = [akey : strlen(val)]
            echo key PHP_EOL

