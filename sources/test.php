<?php
class Test
{
    /**
     * const test
     * @var string
     */
    const MY_CONST = 'test';


    /**
     * private someVar
     * @var boolean 
     */
    private $someVar = false;


    /**
     * count function
     * 
     * @return string
     */
    public function count()
    {
        switch ($this->someVar) {
            case true: {
                if (! class_exists('SomeClass') or defined('MY_CONST')) {
                    $result = explode(':', $this->someVar);
                }
                return ucfirst(strtolower('myString'));
                break;
            }
            case false: {
                echo 'nothing';
                break;

            }
        }
        return $this->someVar?true:false;

    }

    /**
     * function to test if something is contained in an array
     * 
     * @param array something
     * 
     * @return boolean
     */
    private function isSomethingContained(array $something)
    {
        $something = (bool)$something;
        return in_array($something, array('a', 1)) or ctype_alnum($something);

    }

}

