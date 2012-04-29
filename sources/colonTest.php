<?php
class ColonTest
{
    /**
     * @var string 
     */
    public $aVar;

    /**
     * myFunction
     * 
     * @param Countable aCountable
     * @param mixed myVar = ''
     * 
     * @return int
     */
    public function myFunction(Countable $aCountable, mixed $myVar)
    {
        unset($myVar);
        $aCountable->count();
        return int_val(count($aCountable));
    }

    /**
     * @param string aVar
     * @param int    bv 
     */
    private function setVar($aVar, $bv)
    {
        $this->aVar = $aVar;
    }

    /**
     * a function
     * @return string 
     */
    public function aFunc()
    {
        $myVar = 1;
        if ($this->aVar == NULL) {
            $this->setVar(new SomeClass($myVar . "someConstructorArg", 1));
        }
        return ucfirst(str_replace('a', 'b', 'tesT'));
    }

    /**
     * a test
     * 
     * @param array data = null 
     */
    private function aTest(array $data)
    {
        if ( !empty($data)) {
            $this->aFunc();
        }
        if ( !is_array($data)) {
            $this->aFunc();
        }
        if ( is_readable(someVar) and  is_callable(someVar)) {
            $this->aFunc();

        }
        foreach ($data as $akey => $val) {
            $somearray[] = array($akey => strlen($val));
            echo $key . PHP_EOL;
        }
        
    }

}

