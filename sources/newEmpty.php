<?php

class Schinken extends Wurst
{
    /**
     * hans
     * @var int 
     */
    private $hans;
    /**
     * wurst
     * @var string 
     */
    private static $wurst;
    /**
     * an array
     * @var array
     */
    protected $anArray = array();

    /**
     * hanswurst
     * 
     * @param MyInterface hans 
     * @param string      wurst 
     * @return Schinken
     */
    private function hanswurst (MyInterface $hans, $wurst = 'Schinken')
    {
        $this->hans = $hans;
        self::$wurst = $wurst;
        $this->anArray[] = 1;
        $this->anArray[] = 2;

        return $this;
    }

    /**
     * final function
     */
    final function someFunction ()
    {
        $this->hanswurst(1, "hanswurst");
        $a = array('b', $var1, $var2);
        return $this->hans . " " . self::$wurst;
    }

    function anotherMethod()
    {
        if (true) {
            echo "Hello World";
        }

        else {
            echo "World, Hello";
        }

    }

    function anotherMethodAgain ()
    {
        echo "Hello World";
    }

}

