<?php

class Test extends StdClass implements Countable
{
    private $someVar = false;

    function count()
    {
        switch ($this->someVar) {
            case true: {
                break;
            }

            case false: {
                echo "nothing";
            }

        }

        return $tmp = ($this->someVar)?true:false;
    }

    /**
    private isSomethingContained (something)
        return something is in_array ['a', 1] or something is ctype_alnum
    */
}

