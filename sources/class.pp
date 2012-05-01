<?php
/**
 * MyClass 
 */
class MyClass 
extends ParentClass
implements Foo, Bar

    /**
     * foo
     * @return void 
     */
    fooMethod:
        parent::fooMethod()
    
    /**
     * modifies a string
     * 
     * @param string aString 
     * 
     * @return string
     */
    getCleanedString:
        if aString strlen? > 3
            return strtolower: aString
        else
            return aString

    /**
     * some funct
     * 
     * @param MyInterface anObject
     * @param int mustBeInt
     * @param boolean mustBeBool
     */
    someFunc:
        try
            return anObject.someFunc()
        catch Exception e
            echo e.getMessage()