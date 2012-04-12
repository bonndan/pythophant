<?php

class MyClass 
extends ParentClass
implements Foo, Bar

    fooMethod()
        parent::fooMethod()
    
    getCleanedString: aString
        if aString strlen? > 3
            return strtolower: aString
        else
            return aString
            
    someFunc: MyInterface anObject, int mustBeInt, boolean mustBeBool
        try
            return anObject.someFunc()
        catch Exception e
            echo e.getMessage()