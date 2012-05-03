<?php
class MyClass
{
    /**
     * foo
     * @return void 
     */
    public function fooMethod()
    {
        parent::fooMethod();

    }

    /**
     * modifies a string
     * 
     * @param string aString 
     * 
     * @return string
     */
    public function getCleanedString($aString)
    {
    php/*asdasd*/;
        if (! is_string($aString)) {
            throw new InvalidArgumentException("aString is not of type string") ;
        }
    //
        if (strlen($aString) > 3) {
            return strtolower($aString);
        }
        else {
            return $aString;
        }

    }

    /**
     * some funct
     * 
     * @param MyInterface anObject
     * @param int mustBeInt
     * @param boolean mustBeBool
     */
    public function someFunc(MyInterface $anObject, $mustBeInt, $mustBeBool)
    {
    php/*asdasd*/;
        if (! is_bool($mustBeBool)) {
            throw new InvalidArgumentException("mustBeBool is not of type bool") ;
        }
    //
    php/*asdasd*/;
        if (! is_int($mustBeInt)) {
            throw new InvalidArgumentException("mustBeInt is not of type int") ;
        }
    //
        try {
            return $anObject->someFunc();
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}

