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
        try {
            return $anObject->someFunc();
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }

}

