<?php /** generated by PythoPhant on 2012/02/12 10:45:39 from sources/test.pp #afa86ba65e6128130e9019d3c1747b29 */

class Test extends StdClass implements Countable
{
    private $someVar = false;

    public function count()
    {
        switch ($this->someVar) {
            case true: {
                break;
            }

            case false: {
                echo "nothing";
            }

        }

        return $this->someVar?true:false;
    }

    /**
    private isSomethingContained (something)
        return something is in_array ['a', 1] or something ctype_alnum?
    */
}

