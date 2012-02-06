<?php

class XYZ_Controller extends Zend_Controller_Action
    
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
        if isPost && form.isValid(@getAllParams())
            something.setValues(form.getValues()).save()
            
        foreach json2 as key => value
            echo key
            @$key = value
        
        if ctype_alnum(json[0])
            dosomething()
        elseif false
            echo MY_CONSTANT
            echo PHP_EOL

    /**
     * some other comment 
     */
    public someOtherFunction()
        donothing()
    