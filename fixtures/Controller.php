<?php
/**
 * 
 * 
 * @ppWatermark generated by PythoPhant on 2012/05/17 10:59:16 from Controller. #35e053d5e408af4605d0b1d8d6023137
 */
class XYZ_Controller
extends Zend_Controller_Action
{
    /**
     * @var string 
     */
    const MY_CLASS_CONST = 'const';


    /**
     * some var 
     * @var string
     */
    public $someVar;

    /**
     * function to test something
     * 
     * @param string myparam = null "an important param"
     * @param int    myparam1 = 1   "an important param too"
     * 
     * @return private
     */
    private function myFunction($myparam = null, $myparam1 = 1)
    {
        $myparam1 = strtolower($myparam1);
        return $myparam;
    }

    /**
     * "function to test something"
     * 
     * @param string myparam = null "an important param"
     * @param int    myparam1 = 1   "an important param too"
     */
    private function myFunction2($myparam = null, $myparam1 = 1)
    {
        $myparam1 = strtolower($myparam1);

        return $myparam;
    }

    /**
     * @param SomeInterface xyz
     */
    public function addAction(SomeInterface $xyz)
    {
        $json = array(12, 13);
        $json[3] = 'somevar';
        $json2 = (object)array(
            'uncle'=> array('bob', 'joe', 1),
            'names'=> array('first'=> 'walter'),
        );

        $something = new Myclass();
        $form = new MyForm;

        $isPost = $this->getRequest()->isPost();
        if ($isPost and $form->isValid($this->getAllParams())) {
            $something->setValues($form->getValues())->save();

        }
        foreach ($json2 as $key => $value) {
            echo $key;
            $this->$key = $value;

        }
        if (ctype_alnum($something->getInt())) {
            dosomething();
        }
        elseif (false) {
            echo MY_CONSTANT;
            echo PHP_EOL;
        }
    }

    /**
     * some other comment 
     */
    public function someOtherFunction()
    {
        donothing(self::MY_CLASS_CONST);
        if (! ctype_alnum($this->json[0])) {
            dosomething();
        }
        elseif (is_file('my.file')) {
            unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR.  'my.file');
            $myString = "a string ".  "b string".  $myVar;
            
        }
    }

}
