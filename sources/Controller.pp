<?php

class XYZ_Controller extends Zend_Controller_Action
    
    const MY_CLASS_CONST = 'const'

    public addAction(SomeInterface xyz)
        json = [12, 13]
        json[3] = 'somevar'
        json2 = {
            'uncle': ['bob', 'joe'],
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
            