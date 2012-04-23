<?php
class Schinken
{
    /**
     * hans
     * @var int 
     */
    public $hans;
    /**
     * myCountable
     * @var Countable 
     */
    public $myCountable;
    /**
     * wurst
     * @var string 
     */
    public $wurst;
    /**
     * an array
     * @var array
     */
    public $anArray = array();

;
    /**
     * hanswurst
     * 
     * @param MyInterface hans 
     * @param string      wurst 
     * @return Schinken
     */
    public function hanswurst(MyInterface $hans, string $wurst)
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
    public function someFunction(int $nothing)
    {
        $this->hanswurst(1, "hanswurst");
        $a = array('b', $var1, $nothing);
        return $this->hans " " self::$wurst;


    }

    /**
     * another method
     * 
     * @return void
     */
    public function anotherMethod()
    {
        if true;
            echo "Hello World";
        else;
            echo "World, Hello";


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

