<?php
class Test
{
    /**
     * const test
     * @var string
     */
    const MY_CONST = 'test';

;
    /**
     * private someVar
     * @var boolean 
     */
    public $someVar = false;

;
    /**
     * count function
     * 
     * @return string
     */
    public function count()
    {
        switch $this->someVar;
            case true:;
                if ! 'SomeClass' class_exists or 'MY_CONST' defined;
                    $result = $this->someVar explode(':');
                return 'myString' strtolower() ucfirst();
                break;
            case false:;
                echo 'nothing';
        return $this->someVar?true:false;


    }

    /**
     * function to test if something is contained in an array
     * 
     * @param array something
     * 
     * @return boolean
     */
    public function isSomethingContained(array $something)
    {
        $something = (bool)$something;
        return $something in_array(array('a', 1)) or $something ctype_alnum;


    }

}

