<?php /** generated by PythoPhant on 2012/02/28 23:01:35 from sources/colonTest.pp #667f5fbb56443d582555960dfc780cf5 */

class ColonTest
{
    private $aVar;

    public function myFunction(Countable $aCountable, $myVar = '')
    {
        unset($myVar);
        $aCountable->count();
        return int_val(count($aCountable));
    }

    private function setVar($aVar, $bv)
    {
        $this->aVar = $aVar;
    }

    public function aFunc()
    {
        $myVar = 1;
        if ($this->aVar == NULL) {
            $this->setVar(new SomeClass($myVar . "someConstructorArg", 1));
        }

        return ucfirst(str_replace('a', 'b', 'tesT'));
    }

    private function aTest(array $data)
    {
        if (! empty($data)) {
            $this->aFunc();
        }

        foreach ($data as $key => $val) {
            echo $key . PHP_EOL;
        }

    }

}

