<?php
class NewEmpty
{
    /**
     * hans
     * @var int 
     */
    private  $hans;

    /**
     * myCountable
     * @var Countable 
     */
    private  $myCountable;

    /**
     * wurst
     * @var string 
     */
    private $wurst;

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
    private function hanswurst(MyInterface $hans, $wurst)
    {
        $this->hans = $hans;
        self::$wurst = $wurst;
        $this->anArray[] = 1;
        $this->anArray[] = 2;

    }

    /**
     * final function
     * 
     * @param int nothing
     */
    final function someFunction($nothing)
    {
        $this->hanswurst(1, "hanswurst");
        $a = array('b', $var1, $nothing);
        return $this->hans . " ".  self::$wurst;

    }

    /**
     * another method
     * 
     * @return void
     */
    public function anotherMethod()
    {
        if (true) {
            echo "Hello World";
        }
        else {
            echo "World, Hello";
        }

    }

    /**
     * some method
     * 
     * @return void
     */
    public function anotherMethodAgain()
    {
        echo "Hello World";

    }

}

