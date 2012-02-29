<?php

class MyClass 
extends ParentClass
implements Foo, Bar

    fooMethod()
        parent::fooMethod()
    
    /**
     * @test 'Myvar': assertEquals : 'myvar' 
     * @test 'My Var': assertEquals : 'my var' 
     * @test 'My': assertEquals : 'My' 
     */
    getCleanedString: aString
        if aString strlen? > 3
            return strtolower: aString
        else
            return aString
            
            
    /**
     * @testMock 'MyInterface': expects : once : 'someFunc' : returns : 'test'
     * @test : assertEquals : 'test'
     */
    someFunc: MyInterface anObject
        try
            return anObject.someFunc()
        catch Exception e
            echo e.getMessage()