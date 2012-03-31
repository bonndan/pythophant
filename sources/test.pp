<?php

class Test extends StdClass implements Countable

    private someVar = false

    count()
        switch @someVar
            case true:
                if not 'SomeClass' class_exists? or 'MY_CONST' defined?
                    result = @someVar explode(':')!
                return 'myString' strtolower()! ucfirst()!
                break
            case false:
                echo 'nothing'

        return @someVar?true:false

    
    private isSomethingContained (something)
        return  in_array(something, ['a', 1]) or something ctype_alnum?
    
