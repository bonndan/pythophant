<?php

class XYZ_Controller extends Zend_Controller_Action
    
    const MY_CLASS_CONST = 'const'

    /**
     * some var 
     */
    var someVar


    "function to test something"
    private void myFunction 
        @param string myparam = null "an important param"
        @param int    myparam1 = 1   "an important param too"
        myparam1 = strtolower: myparam1

        return myparam
    
    "function to test something"
    @param string myparam = null "an important param"
    @param int    myparam1 = 1   "an important param too"
    private void myFunction2
        
        myparam1 = strtolower: myparam1

        return myparam
    
    private void myFunction2: string myParam = null




    public addAction(SomeInterface xyz)
        json = [12, 13]
        json[3] = 'somevar'
        json2 = {
            'uncle': ['bob', 'joe', 1],
            'names': ['first': 'walter'],
        }
        
        something = new Myclass()
        form = new MyForm
    
        isPost = @getRequest().isPost()
        if isPost and form.isValid(@getAllParams())
            something.setValues(form.getValues()).save()
            
        foreach json2 as key => value
            echo key
            @$key = value
        
        if something.getInt() ctype_alnum?
            dosomething()
        elseif false
            echo MY_CONSTANT
            echo PHP_EOL

    /**
     * some other comment 
     */
    public someOtherFunction()
        donothing(self::MY_CLASS_CONST)
        if ! @json[0] ctype_alnum?
            dosomething()
        elseif 'my.file' is_file?
            unlink(dirname(__FILE__) DIRECTORY_SEPARATOR 'my.file')
            myString = "a string " "b string" myVar
            