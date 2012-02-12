<?php

class Test extends StdClass implements Countable

    private someVar = false

    count()
        switch @someVar
            case true:
                break
            case false:
                echo "nothing"

        return @someVar?true:false

    /**
    private isSomethingContained (something)
        return something is in_array ['a', 1] or something ctype_alnum?
    */
